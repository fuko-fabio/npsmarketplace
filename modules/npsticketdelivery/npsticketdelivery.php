<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_PS_VERSION_' ) )
    exit;

require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/Ticket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/TicketsGenerator.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsTicketDelivery extends Module {
        const INSTALL_SQL_FILE = 'install.sql';

    public function __construct() {
        $this->name = 'npsticketdelivery';
        $this->tab = 'shipping_logistics';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Ticket Delivery' );
        $this->description = $this->l( 'Generates and sends tickets via e-mail.' );
    }

    public function install() {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
        $shop_url = Tools::getHttpHost(true).__PS_BASE_URI__;

        if (!parent::install()
            || !$this->registerHook('actionOrderHistoryAddAfter')
            || !$this->registerHook('displayBeforeVirtualCarrier')
            || !$this->registerHook('actionPostProcessCarrier')
            || !$this->registerHook('displayOrderDetail')
            || !$this->registerHook('displaySellerOrderDetail')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->createTables($sql))
            return false;
        return true;
    }
    public function uninstall() {
        if (!parent::uninstall()
            || !$this->unregisterHook('actionOrderHistoryAddAfter')
            || !$this->unregisterHook('displayBeforeVirtualCarrier')
            || !$this->unregisterHook('actionPostProcessCarrier')
            || !$this->unregisterHook('displayOrderDetail')
            || !$this->unregisterHook('displaySellerOrderDetail')
            || !$this->unregisterHook('displayCustomerAccount')
            || !$this->deleteTables())
            return false;
        return true;
    }

    public function hookDisplayCustomerAccount() {
        $seller = new Seller(null, $this->context->customer->id);
       if ($seller->requested == 1 && $seller->active == 1) {
            $this->context->smarty->assign(array(
                'tickets_sold_link' => $this->context->link->getModuleLink('npsticketdelivery', 'TicketsSold'),
            )
        );
        return $this->display(__FILE__, 'npsticketsdelivery.tpl');
       }
    }

    public function hookDisplayOrderDetail($params) {
        $order = $params['order'];
        $id_cart = Cart::getCartIdByOrderId($order->id);
        if ($id_cart) {
            $tickets = CartTicket::getAllTicketsByCartId($id_cart);
            $this->smarty->assign(array(
                'tickets' => $this->fillTickets($tickets),
                'is_seller' => true
            ));

            return $this->display(__FILE__, 'order_details.tpl');
        }
    }

    public function hookDisplaySellerOrderDetail($params) {
        $seller = $params['seller'];
        $order = $params['order'];
        if ($seller->id && $order->id) {
            $tickets = CartTicket::getAllTicketsByCartId(Cart::getCartIdByOrderId($order->id), $seller->id);
            $this->smarty->assign(array(
                'tickets' => $this->fillTickets($tickets),
                'is_seller' => false
            ));

            return $this->display(__FILE__, 'order_details.tpl');
        }
    }

    public function fillTickets($tickets) {
        foreach ($tickets as $key => $value) {
            $tickets[$key]['code'] = TicketsGenerator::getCode($value);
            $tickets[$key]['seller'] = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'seller WHERE id_seller='.$value['id_seller']);
            $tickets[$key]['seller_shop'] = $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $value['id_seller']));
        }
        return $tickets;
    }
 
    public function hookActionOrderHistoryAddAfter($params) {
        $order_history = $params['order_history'];
        $id_o_s = Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED');
        if ($order_history->id_order_state == $id_o_s || $order_history->id_order_state == 2) {
            $info_seller = array();
            $cart = Cart::getCartByOrderId($order_history->id_order);
            $c_t = CartTicket::getByCartId($cart->id);
            $persons = json_decode($c_t->persons);
            foreach ($cart->getProducts() as $product) {
                $id_seller = Seller::getSellerByProduct($product['id_product']);
                if (!$id_seller) {
                    PrestaShopLogger::addLog('Unable to find owner of product '.$product['name'].'with ID: '.$product['id_product'].' Ticket cannot be generated');
                    continue;
                }
                $qty = $product['cart_quantity'];
                $seller = new Seller($id_seller);
                if (isset($info_seller[$seller->id])) {
                    $info_seller[$seller->id]['products'][] = $product;
                } else {
                    $info_seller[$seller->id] = array('seller' => $seller, 'products' => array($product));
                }
                $date = null;
                if (isset($product['attributes_small']) && !empty($product['attributes_small'])) {
                    $attrs = explode(',', $product['attributes_small']);
                    $date = date_create($attrs[0].$attrs[1]);
                    $date = date_format($date, 'Y-m-d H:i:s');
                }
                $address = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_ADDRESS_ID'));
                $town = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_TOWN_ID'));
                $district = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_DISTRICT_ID'));
                $extras = Product::getExtras($product['id_product'], $this->context->language->id);

                $entries = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_ENTRIES_ID'));
                $d_from = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_FROM_ID'));
                $d_to = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_TO_ID'));

                
                for ($x=1; $x<=$qty; $x++) {
                    $t = new Ticket();
                    $t->id_cart_ticket = $c_t->id;
                    $t->id_seller = $seller->id;
                    $t->date = $date;
                    $t->generated = date("Y-m-d H:i:s");
                    $t->price = $product['price'];
                    $t->name = $product['name'];
                    $t->address = $address;
                    $t->town = $town;
                    $t->district = $district;
                    $t->person = $persons->$product['id_product']->$x;
                    $t->type = $extras['type'];
                    $t->entries = $entries;
                    $t->to = $d_to;
                    $t->from = $d_from;
                    $t->save();
                }
            }
            TicketsGenerator::generateAndSend($c_t->id, $this->context);
            $this->sendEmails($order_history->id_order, $cart->id, $c_t, $info_seller);
        }
    }

    private function sendEmails($id_order, $id_cart, $cart_ticket, $info_seller) {
        $invoice_requested = Db::getInstance()->getValue('SELECT invoice FROM '._DB_PREFIX_.'cart WHERE id_cart='.$id_cart);

        $order = new Order($id_order);
        $customer = $order->getCustomer();

        $invoice = new Address($order->id_address_invoice);
        $invoice_state = $invoice->id_state ? new State($invoice->id_state) : false;

        $invoice_info = $invoice_requested ? $this->l('Customer asks for an invoice') : $this->l('Bill address');
        $invoice_block_txt = $this->_getFormatedAddress($invoice, "\n");
        $invoice_block_html = $this->_getFormatedAddress($invoice, '<br />', array(
            'firstname' => '<span style="font-weight:bold;">%s</span>',
            'lastname'  => '<span style="font-weight:bold;">%s</span>'
        ));

        $delivery = new Address($order->id_address_delivery);
        $delivery_state = $delivery->id_state ? new State($delivery->id_state) : false;
        $delivery_block_txt = $this->_getFormatedAddress($delivery, "\n");
        $delivery_block_html = $this->_getFormatedAddress($delivery, '<br />', array(
            'firstname' => '<span style="font-weight:bold;">%s</span>',
            'lastname'  => '<span style="font-weight:bold;">%s</span>'
        ));

        $confirmation_data = array();

        foreach ($info_seller as $data) {
            $seller = $data['seller'];
            $seller_customer = new Customer($seller->id_customer);
            // Construct order detail table for the email
            $products_list = '';
            $product_var_tpl_list = array();
            foreach ($data['products'] as $product) {
                $price = Product::getPriceStatic((int)$product['id_product'], false, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                $price_wt = Product::getPriceStatic((int)$product['id_product'], true, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

                $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt;

                $product_var_tpl = array(
                    'reference' => $product['reference'],
                    'name' => $product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : ''),
                    'unit_price' => Tools::displayPrice($product_price, $this->context->currency, false),
                    'price' => Tools::displayPrice($product_price * $product['quantity'], $this->context->currency, false),
                    'quantity' => $product['quantity'],
                    'customization' => array()
                );

                $customized_datas = Product::getAllCustomizedDatas((int)$order->id_cart);
                if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
                    $product_var_tpl['customization'] = array();
                    foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                        $customization_text = '';
                        if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD]))
                            foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text)
                                $customization_text .= $text['name'].': '.$text['value'].'<br />';

                        if (isset($customization['datas'][Product::CUSTOMIZE_FILE]))
                            $customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';

                        $customization_quantity = (int)$product['customization_quantity'];

                        $product_var_tpl['customization'][] = array(
                            'customization_text' => $customization_text,
                            'customization_quantity' => $customization_quantity,
                            'quantity' => Tools::displayPrice($customization_quantity * $product_price, $this->context->currency, false)
                        );
                    }
                }

                $product_var_tpl_list[] = $product_var_tpl;
            } // end foreach ($products)

            $product_list_txt = '';
            $product_list_html = '';
            if (count($product_var_tpl_list) > 0) {
                $product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
                $product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
            }
            $product_var_tpl_list = array();

            $data = array(
                '{name}' => $seller->name,
                '{email}' => $seller_customer->email,
                '{firstname}' => $seller_customer->firstname,
                '{invoice_info}' => $invoice_info,
                '{invoice_block_txt}' => $invoice_block_txt,
                '{invoice_block_html}' => $invoice_block_html,
                '{invoice_company}' => $invoice->company,
                '{invoice_vat_number}' => $invoice->vat_number,
                '{invoice_firstname}' => $invoice->firstname,
                '{invoice_lastname}' => $invoice->lastname,
                '{invoice_address2}' => $invoice->address2,
                '{invoice_address1}' => $invoice->address1,
                '{invoice_city}' => $invoice->city,
                '{invoice_postal_code}' => $invoice->postcode,
                '{invoice_country}' => $invoice->country,
                '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                '{invoice_other}' => $invoice->other,
                '{order_name}' => $order->getUniqReference(),
                '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                '{payment}' => Tools::substr($order->payment, 0, 32),
                '{products}' => $product_list_html,
                '{products_txt}' => $product_list_txt,
                '{delivery_email}' => $cart_ticket->email,
                '{seller_orders_url}' => $this->context->link->getModuleLink('npsmarketplace', 'Orders'),
            );

            if (Validate::isEmail($seller_customer->email)) {
                Mail::Send(Context::getContext()->language->id,
                    'order_info',
                    $this->l('New order'),
                    $data,
                    $seller_customer->email,
                    $seller->name,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'npsticketdelivery/mails/');
            }
            $seller_address = new Address($seller->id_address);
            $seller_address_block_txt = $this->_getFormatedAddress($seller_address, "\n");
            $seller_address_block_html = $this->_getFormatedAddress($seller_address, '<br />', array(
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname'  => '<span style="font-weight:bold;">%s</span>'
            ));
            $confirmation_data[] = array(
                'seller' => $seller,
                'customer' => $seller_customer,
                'address_html' => $seller_address_block_html,
                'address_txt' => $seller_address_block_txt,
                'products_html' => $product_list_html,
                'products_txt' => $product_list_txt,
            );
        }

        $seller_product_list_txt = $this->getEmailTemplateContent(
            'order_conf_seller_product_list.txt',
            Mail::TYPE_TEXT,
            $confirmation_data,
            _PS_MODULE_DIR_.'npsticketdelivery/views/templates/hook'
        );
        $seller_product_list_html = $this->getEmailTemplateContent(
            'order_conf_seller_product_list.tpl',
            Mail::TYPE_HTML,
            $confirmation_data,
            _PS_MODULE_DIR_.'npsticketdelivery/views/templates/hook'
        );

        // Notify customer
        $data = array(
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
            '{delivery_block_txt}' => $delivery_block_txt,
            '{invoice_block_txt}' => $invoice_block_txt,
            '{delivery_block_html}' => $delivery_block_html,
            '{invoice_block_html}' => $invoice_block_html,
            '{delivery_company}' => $delivery->company,
            '{delivery_firstname}' => $delivery->firstname,
            '{delivery_lastname}' => $delivery->lastname,
            '{delivery_address1}' => $delivery->address1,
            '{delivery_address2}' => $delivery->address2,
            '{delivery_city}' => $delivery->city,
            '{delivery_postal_code}' => $delivery->postcode,
            '{delivery_country}' => $delivery->country,
            '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
            '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
            '{delivery_other}' => $delivery->other,
            '{invoice_company}' => $invoice->company,
            '{invoice_vat_number}' => $invoice->vat_number,
            '{invoice_firstname}' => $invoice->firstname,
            '{invoice_lastname}' => $invoice->lastname,
            '{invoice_address2}' => $invoice->address2,
            '{invoice_address1}' => $invoice->address1,
            '{invoice_city}' => $invoice->city,
            '{invoice_postal_code}' => $invoice->postcode,
            '{invoice_country}' => $invoice->country,
            '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
            '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
            '{invoice_other}' => $invoice->other,
            '{order_name}' => $order->getUniqReference(),
            '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
            //'{carrier}' => ($virtual_product || !isset($carrier->name)) ? Tools::displayError('No carrier') : $carrier->name,
            '{payment}' => Tools::substr($order->payment, 0, 32),
            '{seller_products}' => $seller_product_list_html,
            '{seller_products_txt}' => $seller_product_list_txt,
            //'{discounts}' => $cart_rules_list_html,
            //'{discounts_txt}' => $cart_rules_list_txt,
            '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
            '{total_products}' => Tools::displayPrice($order->total_paid - $order->total_shipping - $order->total_wrapping + $order->total_discounts, $this->context->currency, false),
            '{total_discounts}' => Tools::displayPrice($order->total_discounts, $this->context->currency, false),
            '{total_shipping}' => Tools::displayPrice($order->total_shipping, $this->context->currency, false),
            '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $this->context->currency, false),
            '{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency, false));

        // Join PDF invoice
        //if ((int)Configuration::get('PS_INVOICE') && $order_status->invoice && $order->invoice_number){
        //    $pdf = new PDF($order->getInvoicesCollection(), PDF::TEMPLATE_INVOICE, $this->context->smarty);
        //    $file_attachement['content'] = $pdf->render(false);
        //    $file_attachement['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
        //    $file_attachement['mime'] = 'application/pdf';
        //} else
            $file_attachement = null;

        if (Validate::isEmail($customer->email))
            Mail::Send(
                (int)$order->id_lang,
                'order_seller_conf',
                $this->l('Order summary'),
                $data,
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                null,
                null,
                $file_attachement,
                null,
                _PS_MODULE_DIR_.'npsticketdelivery/mails/',
                false,
                (int)$order->id_shop
            );
    }

    private function getFeatureValue($features, $id_feature) {
        foreach ($features as $key => $value) {
            if($value['id_feature'] == $id_feature) {
               $rows = FeatureValue::getFeatureValueLang($value['id_feature_value']);
                foreach ($rows as $row) {
                    if($row['id_lang'] == $this->context->language->id)
                        return $row['value'];
                }
            }
        }
        return null;
    }

    public function hookDisplayBeforeVirtualCarrier() {
        $this->context->controller->addJS(_PS_JS_DIR_.'validate.js');
        $this->context->smarty->assign(array(
            'send_tickets_info_url' => Configuration::get('NPS_TICKETS_INFO_URL'),
            
        ));
        
        return $this->display(__FILE__, 'views/templates/hook/virtual_carrier.tpl');
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            Configuration::updateValue('NPS_TICKETS_INFO_URL', Tools::getValue('NPS_TICKETS_INFO_URL'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output.$this->displayForm();
    }

    public function hookActionPostProcessCarrier($params) {
        if ($params['ticket_email'] != $this->context->customer->email)
            $emails = implode(',', array($this->context->customer->email, $params['ticket_email']));
        else
            $emails = $params['ticket_email'];

        $ticket = new CartTicket(null, $params['id_cart']);
        $ticket->id_cart = $params['id_cart'];
        $ticket->id_customer = $this->context->customer->id;
        $ticket->email = $emails;
        $ticket->id_currency = $this->context->currency->id;
        $ticket->persons = json_encode($params['ticket_person']);
        $ticket->save();
    }

    private function displayForm() {
        $this->context->controller->addJqueryPlugin('tagify');
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0] = $this->linksForm();
        $helper = new HelperForm();
         
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
         
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
         
        // Load current value
        $helper->fields_value = $this->getConfigFieldsValues();
        return $helper->generateForm($fields_form);
    }


    private function linksForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('User Guide URL\'s'),
                ),
                'input' => array(
                     array(
                        'type' => 'text',
                        'label' => $this->l('Send tickets info URL'),
                        'name' => 'NPS_TICKETS_INFO_URL',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submit'.$this->name,
                )
            )
        );
    }
    public function getConfigFieldsValues() {
        return array(
            'NPS_TICKETS_INFO_URL' => Tools::getValue('NPS_TICKETS_INFO_URL', Configuration::get('NPS_TICKETS_INFO_URL')),
        );
    }
    private function createTables($sql) {
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;

        return Db::getInstance()->Execute('alter table ' . _DB_PREFIX_ . 'cart add invoice tinyint(1)');
    }

    private function deleteTables() {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'cart_ticket`,
            `'._DB_PREFIX_.'ticket`')
            && Db::getInstance()->Execute('alter table ' . _DB_PREFIX_ . 'cart drop invoice');
    }

    /**
     * @param Object Address $the_address that needs to be txt formated
     * @return String the txt formated address block
     */

    protected function _getFormatedAddress(Address $the_address, $line_sep, $fields_style = array()) {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }

    /**
     * Fetch the content of $template_name inside the folder current_theme/mails/current_iso_lang/ if found, otherwise in mails/current_iso_lang
     *
     * @param string  $template_name template name with extension
     * @param integer $mail_type     Mail::TYPE_HTML or Mail::TYPE_TXT
     * @param array   $var           list send to smarty
     *
     * @return string
     */
    protected function getEmailTemplateContent($template_name, $mail_type, $var, $template_dir = null) {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH)
            return '';

        $theme_template_path = _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;
        $default_mail_template_path = _PS_MAIL_DIR_.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;

        if (Tools::file_exists_cache($theme_template_path))
            $default_mail_template_path = $theme_template_path;

        if ($template_dir != null)
            $default_mail_template_path = $template_dir.DIRECTORY_SEPARATOR.$template_name;

        if (Tools::file_exists_cache($default_mail_template_path)) {
            $this->context->smarty->assign('list', $var);
            return $this->context->smarty->fetch($default_mail_template_path);
        }
        return '';
    }
}
