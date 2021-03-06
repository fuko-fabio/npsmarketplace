<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

if ( !defined( '_NPS_REPORTS_DIR_' ) )
    define('_NPS_REPORTS_DIR_', '/sales_reports/');
if ( !defined( '_NPS_SELLER_REPORTS_DIR_' ) )
    define('_NPS_SELLER_REPORTS_DIR_', _NPS_REPORTS_DIR_.'sellers/');

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24TransactionDispatcher.php');

class NpsPrzelewy24 extends PaymentModule {

    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct() {
        $this->name = 'npsprzelewy24';
        $this->tab = 'payments_gateways';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Przelewy24' );
        $this->description = $this->l( 'nps Marketplace Przelewy24 payment service 1.0' );
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /**
     * @return bool
     */
    public function install() {
        if (!extension_loaded('soap')) {
            $this->_errors[] = $this->l('Soap Client lib is not installed');
            return false;
        }
        Configuration::updateValue('NPS_P24_ORDER_STATE_AWAITING', 0);
        Configuration::updateValue('NPS_P24_ORDER_STATE_ACCEPTED', 0);
        Configuration::updateValue('NPS_P24_COMMISION', 2.5);
        Configuration::updateValue('NPS_P24_UNIQUE_KEY', '');
        Configuration::updateValue('NPS_P24_CRC_KEY', '');
        Configuration::updateValue('NPS_P24_API_KEY', '');
        Configuration::updateValue('NPS_P24_WEB_SERVICE_URL', 'https://secure.przelewy24.pl/external/wsdl/service.php?wsdl');
        Configuration::updateValue('NPS_P24_URL', 'https://secure.przelewy24.pl');
        Configuration::updateValue('NPS_P24_SANDBOX_URL', 'https://sandbox.przelewy24.pl');
        Configuration::updateValue('NPS_P24_SANDBOX_ERROR', '');
        Configuration::updateValue('NPS_P24_SANDBOX_WEB_SERVICE_URL', 'https://sandbox.przelewy24.pl/external/__wsdl/service.php?wsdl');

        return parent::install()
            && $this->createTables()
            && $this->createOrderStates()
            && $this->createTab()
            && $this->registerHook('payment')
            && $this->registerHook('adminSellerView')
            && $this->registerHook('displayOrderDetail')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('displayOrderDetail');
    }

    public function uninstall() {
        return parent::uninstall()
            && $this->unregisterHook('displayCustomerAccount')
            && $this->unregisterHook('payment')
            && $this->unregisterHook('displayOrderDetail')
            && $this->unregisterHook('adminSellerView')
            && $this->unregisterHook('displayOrderDetail')
            && $this->deleteTab()
            && $this->deleteOrderStates()
            && $this->deleteTables();
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submitKeys')) {
            Configuration::updateValue('NPS_P24_MERCHANT_ID', Tools::getValue('NPS_P24_MERCHANT_ID'));
            Configuration::updateValue('NPS_P24_UNIQUE_KEY', Tools::getValue('NPS_P24_UNIQUE_KEY'));
            Configuration::updateValue('NPS_P24_CRC_KEY', Tools::getValue('NPS_P24_CRC_KEY'));
            Configuration::updateValue('NPS_P24_API_KEY', Tools::getValue('NPS_P24_API_KEY'));
            $output .= $this->displayConfirmation($this->l('Przelewy24 access keys settings updated sucessfully'));
        } else if (Tools::isSubmit('submit')) {
            Configuration::updateValue('NPS_P24_MERCHANT_SPID', Tools::getValue('NPS_P24_MERCHANT_SPID'));
            Configuration::updateValue('NPS_P24_COMMISION', Tools::getValue('NPS_P24_COMMISION'));
            $output .= $this->displayConfirmation($this->l('Przelewy24 settings updated sucessfully'));
        } else if (Tools::isSubmit('submitAccessUrls')) {
            Configuration::updateValue('NPS_P24_URL', Tools::getValue('NPS_P24_URL'));
            Configuration::updateValue('NPS_P24_WEB_SERVICE_URL', Tools::getValue('NPS_P24_WEB_SERVICE_URL'));
            Configuration::updateValue('NPS_P24_REGULATIONS_URL', Tools::getValue('NPS_P24_REGULATIONS_URL'));
            Configuration::updateValue('NPS_P24_USER_ACCESS_URL', Tools::getValue('NPS_P24_USER_ACCESS_URL'));
            $output .= $this->displayConfirmation($this->l('Przelewy24 URL\'s updated sucessfully'));
        }else if (Tools::isSubmit('submitSandbox')) {
            Configuration::updateValue('NPS_P24_SANDBOX_URL', Tools::getValue('NPS_P24_SANDBOX_URL'));
            Configuration::updateValue('NPS_P24_SANDBOX_ERROR', Tools::getValue('NPS_P24_SANDBOX_ERROR'));
            Configuration::updateValue('NPS_P24_SANDBOX_WEB_SERVICE_URL', Tools::getValue('NPS_P24_SANDBOX_WEB_SERVICE_URL'));
            Configuration::updateValue('NPS_P24_SANDBOX_MODE', Tools::getValue('NPS_P24_SANDBOX_MODE'));
            $output .= $this->displayConfirmation($this->l('Sandbox settings updated sucessfully'));
        }
        return $output.$this->displayForm();
    }

    /**
     * @return bool
     */
    private function createTables() {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;
        return true;
    }

    private function deleteTables() {
        return Db::getInstance()->Execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'p24_payment`,
            `'._DB_PREFIX_.'shop_invoice`,
            `'._DB_PREFIX_.'seller_invoice`,
            `'._DB_PREFIX_.'seller_invoice_data`,
            `'._DB_PREFIX_.'p24_payment_statement`,
            `'._DB_PREFIX_.'p24_seller_company`,
            `'._DB_PREFIX_.'p24_dispatch_history`,
            `'._DB_PREFIX_.'p24_dispatch_history_detail');
    }

    private function displayForm() {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0] = $this->configurationKeysForm();
        $fields_form[1] = $this->configurationForm();
        $fields_form[2] = $this->configurationAccessUrlsForm();
        $fields_form[3] = $this->configurationSandboxForm();
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

    private function configurationKeysForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Przelewy24 Access Keys'),
                ),
                'input' => array(
                     array(
                        'type' => 'text',
                        'label' => $this->l('Merchant ID'),
                        'hint' => $this->l('Partner ID in Przelewy24 system'),
                        'name' => 'NPS_P24_MERCHANT_ID',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant Unique Key'),
                        'hint' => $this->l('Unique key retrieved from Przelewy24'),
                        'name' => 'NPS_P24_UNIQUE_KEY',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant CRC Key'),
                        'hint' => $this->l('CRC key retrieved from Przelewy24'),
                        'name' => 'NPS_P24_CRC_KEY',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('WSDL Web Service API Key'),
                        'hint' => $this->l('WSDL Web Service API key retrieved from Przelewy24'),
                        'name' => 'NPS_P24_API_KEY',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitKeys',
                )
            )
        );
    }

    private function configurationForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Marketplace Przelewy24 Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant SPID'),
                        'hint' => $this->l('Merchant account SPID generated by Przelewy24 Partner Account'),
                        'name' => 'NPS_P24_MERCHANT_SPID',
                        'required' => false
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Przelewy24 Commision'),
                        'name' => 'NPS_P24_COMMISION',
                        'class' => 'fixed-width-xs',
                        'suffix' => '%',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submit',
                )
            )
        );
    }

    private function configurationAccessUrlsForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Przelewy Access URL\'s'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('End user Przelewy24 account access URL (Leave emty to disable)'),
                        'name' => 'NPS_P24_USER_ACCESS_URL',
                        'required' => false
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Regulations of Przelewy24 URL'),
                        'name' => 'NPS_P24_REGULATIONS_URL',
                        'required' => true
                    ),
                     array(
                        'type' => 'text',
                        'label' => $this->l('Web Service URL'),
                        'hint' => $this->l('Endpoint of the Przelewy24 standard service'),
                        'name' => 'NPS_P24_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('WSDL Web Service URL'),
                        'hint' => $this->l('Endpoint of the Przelewy24 WSDL WebService'),
                        'name' => 'NPS_P24_WEB_SERVICE_URL',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitAccessUrls',
                )
            )
        );
    }

    private function configurationSandboxForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Przelewy24 Sandbox Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Web Service Sandbox URL'),
                        'name' => 'NPS_P24_SANDBOX_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('WSDL Web Service Sandbox URL'),
                        'hint' => $this->l('Endpoint of the Przelewy24 WSDL WebService'),
                        'name' => 'NPS_P24_SANDBOX_WEB_SERVICE_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'NPS_P24_SANDBOX_ERROR',
                        'label' => $this->l('Test error code'),
                        'options' => array(
                                        'query' => array(
                                            array(
                                                'label' => '',
                                            ),
                                            array(
                                                'label' => 'TEST_ERR04',
                                            ),
                                            array(
                                                'label' => 'TEST_ERR54',
                                            ),
                                            array(
                                                'label' => 'TEST_ERR102',
                                            ),
                                            array(
                                                'label' => 'TEST_ERR103',
                                            ),
                                            array(
                                                'label' => 'TEST_ERR110',
                                            ),
                                         ),
                                        'id' => 'label',
                                        'name' => 'label'
                                    ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Sandbox mode'),
                        'name' => 'NPS_P24_SANDBOX_MODE',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitSandbox',
                )
            )
        );
    }

    public function getConfigFieldsValues() {
        return array(
            'NPS_P24_MERCHANT_ID' => Tools::getValue('NPS_P24_MERCHANT_ID', Configuration::get('NPS_P24_MERCHANT_ID')),
            'NPS_P24_UNIQUE_KEY' => Tools::getValue('NPS_P24_UNIQUE_KEY', Configuration::get('NPS_P24_UNIQUE_KEY')),
            'NPS_P24_CRC_KEY' => Tools::getValue('NPS_P24_CRC_KEY', Configuration::get('NPS_P24_CRC_KEY')),
            'NPS_P24_SANDBOX_MODE' => Tools::getValue('NPS_P24_SANDBOX_MODE', Configuration::get('NPS_P24_SANDBOX_MODE')),
            'NPS_P24_URL' => Tools::getValue('NPS_P24_URL', Configuration::get('NPS_P24_URL')),
            'NPS_P24_SANDBOX_URL' => Tools::getValue('NPS_P24_SANDBOX_URL', Configuration::get('NPS_P24_SANDBOX_URL')),
            'NPS_P24_WEB_SERVICE_URL' => Tools::getValue('NPS_P24_WEB_SERVICE_URL', Configuration::get('NPS_P24_WEB_SERVICE_URL')),
            'NPS_P24_SANDBOX_ERROR' => Tools::getValue('NPS_P24_SANDBOX_ERROR', Configuration::get('NPS_P24_SANDBOX_ERROR')),
            'NPS_P24_SANDBOX_WEB_SERVICE_URL' => Tools::getValue('NPS_P24_SANDBOX_WEB_SERVICE_URL', Configuration::get('NPS_P24_SANDBOX_WEB_SERVICE_URL')),
            'NPS_P24_REGULATIONS_URL' => Tools::getValue('NPS_P24_REGULATIONS_URL', Configuration::get('NPS_P24_REGULATIONS_URL')),
            'NPS_P24_USER_ACCESS_URL' => Tools::getValue('NPS_P24_USER_ACCESS_URL', Configuration::get('NPS_P24_USER_ACCESS_URL')),
            'NPS_P24_COMMISION' => Tools::getValue('NPS_P24_COMMISION', Configuration::get('NPS_P24_COMMISION')),
            'NPS_P24_API_KEY' => Tools::getValue('NPS_P24_API_KEY', Configuration::get('NPS_P24_API_KEY')),
            'NPS_P24_MERCHANT_SPID' => Tools::getValue('NPS_P24_MERCHANT_SPID', Configuration::get('NPS_P24_MERCHANT_SPID')),
        );
    }

    public function hookDisplayCustomerAccount() {
        $seller = new Seller(null, $this->context->customer->id);
       if ($seller->requested == 1 && $seller->active == 1 && $seller->locked == 0) {
            $this->context->smarty->assign(array(
                'payment_settings_link' => $this->context->link->getModuleLink('npsprzelewy24', 'PaymentSettings'),
                'p24_access_url' => Configuration::get('NPS_P24_USER_ACCESS_URL'),
            )
        );
        return $this->display(__FILE__, 'npsprzelewy24.tpl');
       }
    }

    public function hookAdminSellerView() {
        $this->context->smarty->assign(
            'id_seller', Tools::getValue('id_seller')
        );
        return $this->display(__FILE__, 'seller_generate_report.tpl');
    }

    public function hookPayment() {
        $this->context->controller->addJS (_PS_MODULE_DIR_.'npsprzelewy24/js/order-payment.js');
        $this->context->smarty->assign(array(
            'p24_agreement_url' => Configuration::get('NPS_P24_REGULATIONS_URL'),
            'p24_payment_url' => $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation')
        ));
        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookDisplayOrderDetail($params) {
        $order = $params['order'];
        $result = Db::getInstance()->executeS('SELECT id_order_state FROM `'._DB_PREFIX_.'order_history` WHERE `id_order`="'.$order->id.'"');

        $can_retry = true;
        foreach ($result as $key => $value) {
            if ($value['id_order_state'] == Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED')) {
                $can_retry = false;
                break;
            } else if ($value['id_order_state'] == 2) {
                return;
            }
        }
        if ($can_retry) {
            $this->context->smarty->assign(
                'p24_retryPaymentUrl', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation', array('order_id' => $order->id, 'renew' => true))
            );
        } else {
            $id_cart = CartCore::getCartIdByOrderId($order->id);
            $summary = P24PaymentStatement::getSummaryByCartId($id_cart);
            if ($summary && $summary['id_payment_statement']) {
                $this->context->smarty->assign(array(
                    'statement' => $summary['statement']
                ));
            }
        }
        return $this->display(__FILE__, 'order_detail.tpl');
    }

    public function reportError($logs = array()) {
        PrestaShopLogger::addLog(implode(' | ', $logs), 3);

        $id_lang = (int)$this->context->language->id;
        $iso_lang = Language::getIsoById($id_lang);

        if (!is_dir(dirname(__FILE__).'/mails/'.Tools::strtolower($iso_lang)))
            $id_lang = Language::getIdByIso('en');

        Mail::Send($id_lang,
            'error_reporting',
            $this->l('Error reporting from your Przelewy24 module',
            (int)$this->context->language->id),
            array('{logs}' => implode('<br />', $logs)),
            Configuration::get('PS_SHOP_EMAIL'),
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.$this->name.'/mails/');
    }

    private function createTab() {
        $tabs = Tab::getCollectionFromModule('npsmarketplace');

        $t = new Tab();
        $t->id_parent = $tabs[0]->id;
        $t->position = 1;
        $t->module = $this->name;
        $t->class_name = 'AdminDispatchHistory';
        $languages = Language::getLanguages();
        foreach ($languages AS $language)
            $t->name[intval($language['id_lang'])] = 'Payment History';
        $resutl = $t->add();

        $t = new Tab();
        $t->id_parent = $tabs[0]->id;
        $t->position = 2;
        $t->module = $this->name;
        $t->class_name = 'AdminSellerCompany';
        $languages = Language::getLanguages();
        foreach ($languages AS $language)
            $t->name[intval($language['id_lang'])] = 'Przelewy24 Accounts';
        $resutl && $t->add();

        $t = new Tab();
        $t->id_parent = $tabs[0]->id;
        $t->position = 3;
        $t->module = $this->name;
        $t->class_name = 'AdminSellerInvoices';
        $languages = Language::getLanguages();
        foreach ($languages AS $language)
            $t->name[intval($language['id_lang'])] = 'Sellers Reports';
        $resutl && $t->add();

        $t = new Tab();
        $t->id_parent = $tabs[0]->id;
        $t->position = 4;
        $t->module = $this->name;
        $t->class_name = 'AdminShopInvoices';
        $languages = Language::getLanguages();
        foreach ($languages AS $language)
            $t->name[intval($language['id_lang'])] = 'Shop Reports';

        return $resutl && $t->add();
    }

    private function deleteTab() {
        $tabs = Tab::getCollectionFromModule($this->name);
        if (!empty($tabs)) {
            foreach ($tabs as $tab)
                $tab->delete();
            return true;
        }
        return false;
    }

    public function createOrderStates() {
         $avaiting_names = array();
         foreach (Language::getLanguages() as $language) {
            if (Tools::strtolower($language['iso_code']) == 'pl')
                $avaiting_names[$language['id_lang']] = 'Oczekiwanie na płatność Przelewy24';
            else
                $avaiting_names[$language['id_lang']] = 'Waiting for payment Przelewy24';
         }
         $done_names = array();
         foreach (Language::getLanguages() as $language) {
            if (Tools::strtolower($language['iso_code']) == 'pl')
                $done_names[$language['id_lang']] = 'Płatność Przelewy24 przyjęta';
            else
                $done_names[$language['id_lang']] = 'Payment accepted by Przelewy24';
        }
        $states = array(
            'awaiting' => array(
                'name' => $avaiting_names,
                'color' => 'LightBlue',
                'invoice' => false,
                'paid' => false,
                'config' => 'NPS_P24_ORDER_STATE_AWAITING'
            ),
            'done' => array(
                'name' => $done_names,
                'color' => 'RoyalBlue',
                'invoice' => true,
                'paid' => true,
                'config' => 'NPS_P24_ORDER_STATE_ACCEPTED'
            )
        );
        $result = true;
        foreach($states as $s) {
            $os = new OrderState();
            $os->name = $s['name'];
            $os->color = $s['color'];
            $os->invoice = $s['invoice'];
            $os->logable = true;
            $os->hidden = false;
            $os->send_email = false;
            $os->delivery = false;
            $os->paid = $s['paid'];
            $os->module_name =$this->name;
            $result = $result && $os->add();
            Configuration::updateValue($s['config'], $os->id);
        }
        return $result;
    }

    public function validateP24Response($p24_error_code, $p24_token, $p24_session_id, $p24_amount,
            $p24_currency, $p24_order_id, $p24_method, $p24_statement, $p24_sign, $manual = false) {
        if (isset($p24_session_id) && !empty($p24_session_id)) {
            $session_id_array = explode('|', $p24_session_id);
            $id_cart = $session_id_array[1];
            $id_order = Order::getOrderByCartId($id_cart);
            if (empty($p24_error_code)) {
                $validator = new P24PaymentValodator(
                    $p24_session_id,
                    $p24_amount,
                    $p24_currency,
                    $p24_order_id,
                    $p24_method,
                    $p24_statement,
                    $p24_sign
                );
                if ($manual) {
            	    $result = $validator->validateManually();
                } else {
            	    $result = $validator->validate($p24_token, true);
                }
                if ($result['error'] == 0) {
                    PrestaShopLogger::addLog('Background payment. Verification success. Session ID: '.$p24_session_id.' Dispatching money.');
                    $dispatcher = new P24TransactionDispatcher($id_cart);
                    $dispatcher->dispatchMoney();
                } else {
                    PrestaShopLogger::addLog('Background payment. Verification Failed!. '.$result['errorMessage']);
                    $history = new OrderHistory();
                    $history->id_order = intval($id_order);
                    $history->changeIdOrderState(8, intval($id_order));
                    $history->addWithemail(true);
                }
            } else {
                $history = new OrderHistory();
                $history->id_order = intval($id_order);
                $history->changeIdOrderState(8, intval($id_order));
                $history->addWithemail(true);
                $this->reportError(array(
                    'Background payment. Unabe to verify payment. Error code: '.$p24_error_code,
                    'Requested URL: '.Context::getContext()->link->getModuleLink('npsprzelewy24', 'paymentState'),
                    'GET params: '.implode(' | ', $_GET),
                    'POST params: '.implode(' | ', $_POST),
                ));
            }
        } else {
            $this->reportError(array(
                'Background payment. Unabe to verify payment. Missing session ID.',
                'Requested URL: '.Context::getContext()->link->getModuleLink('npsprzelewy24', 'paymentState'),
                'GET params: '.implode(' | ', $_GET),
                'POST params: '.implode(' | ', $_POST),
            ));
        }
    }

    private function deleteOrderStates() {
        $os = new OrderState(Configuration::get('NPS_P24_ORDER_STATE_AWAITING'));
        $os->delete();
        $os = new OrderState(Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED'));
        $os->delete();
    }

    public function errorMsg($errorCode = null) {
        $result = null;
        $messages = array(
            'err00' => $this->l('Incorrect call.'),
            'err01' => $this->l('Authorization answer confirmation was not received.'),
            'err02' => $this->l('Authorization answer was not received.'),
            'err03' => $this->l('This query has been already processed.'),
            'err04' => $this->l('Authorization query incomplete or incorrect.'),
            'err05' => $this->l('Store configuration cannot be read.'),
            'err06' => $this->l('Saving of authorization query failed.'),
            'err07' => $this->l('Another payment is being concluded.'),
            'err08' => $this->l('Undetermined store connection status.'),
            'err09' => $this->l('Permitted corrections amount has been exceeded.'),
            'err10' => $this->l('Incorrect transaction value!'),
            'err49' => $this->l('To high transaction risk factor.'),
            'err51' => $this->l('Incorrect reference method.'),
            'err52' => $this->l('Incorrect feedback on session information!'),
            'err53' => $this->l('Transaction error!'),
            'err54' => $this->l('Incorrect transaction value!'),
            'err55' => $this->l('Incorrect transaction id!'),
            'err56' => $this->l('Incorrect card.'),
            'err57' => $this->l('Incompatibility of TEST flag!'),
            'err58' => $this->l('Incorrect sequence number!'),
            'err101' => $this->l('Incorrect call.'),
            'err102' => $this->l('Allowed transaction time has expired.'),
            'err103' => $this->l('Incorrect transfer value.'),
            'err104' => $this->l('Transaction awaits confirmation.'),
            'err105' => $this->l('Transaction finished after allowed time.'),
            'err106' => $this->l('Transaction result verification error.'),
            'err161' => $this->l('Transaction request terminated by user.'),
            'err162' => $this->l('Transaction request terminated by user.'),

            // Web service errors
            '1' => $this->l('Access denied.'),
            '500' => $this->l('Missing required data: Company Name, City, PostCode, Street, E-mail or NIP.'),
            '501' => $this->l('Incorrect format for NIP.'),
            '502' => $this->l('Incorrect format for e-mail.'),
            '503' => $this->l('Incorrect IBAN number.'),
            '510' => $this->l('Company already exists!'),
            '511' => $this->l('Company already exists, but not active.'),
            '600' => $this->l('Repeated batch number!'),
            '601' => $this->l('Empty refund list!'),
            '610' => $this->l('Transaction not found.'),
            '699' => $this->l('Errors in refund list. Refunds rejected.'),
            '10000' => $this->l('Unknown error.'),
            
            #Internal errors
            'intErr00' => $this->l('Internal validation failed. Unable to check available funds.'),
            'intErr01' => $this->l('Unable to verifi payment'),
            'intErr02' => $this->l('Inalid verification token'),
            'intErr03' => $this->l('Payment has been already finalized and verified'),
            'intErr04' => $this->l('Unable to verifi payment. Invalid session ID'),
        );
        if ($errorCode && array_key_exists($errorCode, $messages))
            $result = $messages[$errorCode];
        return $result != null ? $result : $this->l('Unknown error occured.');
    }
}