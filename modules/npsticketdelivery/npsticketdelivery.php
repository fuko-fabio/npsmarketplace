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
            || !$this->createTables($sql))
            return false;
        return true;
    }
    public function uninstall() {
        if (!parent::uninstall()
            || !$this->unregisterHook('actionOrderHistoryAddAfter')
            || !$this->unregisterHook('displayBeforeVirtualCarrier')
            || !$this->unregisterHook('actionPostProcessCarrier')
            || !$this->deleteTables())
            return false;
        return true;
    }

    public function hookActionOrderHistoryAddAfter($params) {
        $order_history = $params['order_history'];
        $id_order_state = Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED');
        if ($id_order_state == $order_history->id_order_state || $order_history->id_order_state == 2) {
            $info_seller = array();
            $id_order = $order_history->id_order;
            $cart = Cart::getCartByOrderId($id_order);
            $c_t = CartTicket::getByCartId($cart->id);
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
                $attrs = explode(',', $product['attributes_small']);
                $date = date_create($attrs[0].$attrs[1]);
                $date = date_format($date, 'Y-m-d H:i:s');
                $address = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_ADDRESS_ID'));
                $town = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_TOWN_ID'));
                $district = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_DISTRICT_ID'));
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
                    $t->save();
                }
            }
            TicketsGenerator::generateAndSend($c_t->id);
            $this->sendInfoToSellers($cart, $c_t, $info_seller);
        }
    }

    private function sendInfoToSellers($cart, $cart_ticket, $info_seller) {
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $shop_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        $shop_email = Configuration::get('PS_SHOP_EMAIL');
        $order = new Order(Order::getOrderByCartId($cart->id));
        $sql = 'SELECT invoice FROM '._DB_PREFIX_.'cart WHERE id_cart='.$this->context->cart->id;
        $invoice_request = Db::getInstance()->getValue($sql);
        foreach ($info_seller as $data) {
            $seller = $data['seller'];
            $seller_customer = new Customer($seller->id_customer);
            // Construct order detail table for the email
            $products_list = '';
            $virtual_product = true;
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
                // Check if is not a virutal product for the displaying of shipping
                if (!$product['is_virtual'])
                    $virtual_product &= false;
            } // end foreach ($products)

            $product_list_txt = '';
            $product_list_html = '';
            if (count($product_var_tpl_list) > 0) {
                $product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
                $product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
            }

            $invoice = new Address($order->id_address_invoice);
            $data = array(
                '{name}' => $seller->name,
                '{email}' => $seller->email,
                '{firstname}' => $seller_customer->firstname,
                '{invoice_info}' => $invoice_request == 1 ? 'Kupujący prosi o wystawienie faktury na dane:' : 'Dane do rachunku',
                '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
                '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', array(
                    'firstname' => '<span style="font-weight:bold;">%s</span>',
                    'lastname'  => '<span style="font-weight:bold;">%s</span>'
                )),
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

            Mail::Send(Context::getContext()->language->id,
                'order_info',
                Mail::l('New order'),
                $data,
                $seller->email,
                $seller->name,
                $shop_email,
                $shop_name,
                null,
                null,
                _PS_MODULE_DIR_.'npsticketdelivery/mails/');
        }
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
        return $this->display(__FILE__, 'views/templates/hook/virtual_carrier.tpl');
    }

    public function hookActionPostProcessCarrier($params) {
        $ticket = new CartTicket(null, $params['id_cart']);
        $ticket->id_cart = $params['id_cart'];
        $ticket->id_customer = $this->context->customer->id;
        $ticket->email = $params['ticket_email'];
        $ticket->id_currency = $this->context->currency->id;
        $ticket->save();
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
    protected function getEmailTemplateContent($template_name, $mail_type, $var) {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH)
            return '';

        $theme_template_path = _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;
        $default_mail_template_path = _PS_MAIL_DIR_.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name;

        if (Tools::file_exists_cache($theme_template_path))
            $default_mail_template_path = $theme_template_path;

        if (Tools::file_exists_cache($default_mail_template_path)) {
            $this->context->smarty->assign('list', $var);
            return $this->context->smarty->fetch($default_mail_template_path);
        }
        return '';
    }
}
