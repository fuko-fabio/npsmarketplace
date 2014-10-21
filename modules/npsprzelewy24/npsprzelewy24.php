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
            && $this->registerHook('displayCustomerAccount');
    }

    public function uninstall() {
        return parent::uninstall()
            && $this->unregisterHook('displayCustomerAccount')
            && $this->unregisterHook('payment')
            && $this->unregisterHook('displayOrderDetail')
            && $this->unregisterHook('adminSellerView')
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
        $this->context->smarty->assign('p24_payment_url', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation'));
        return $this->display(__FILE__, 'payment.tpl');
    }

    public function reportError($logs = array()) {
        PrestaShopLogger::addLog(implode(' | ', $logs), 3);

        $id_lang = (int)$this->context->language->id;
        $iso_lang = Language::getIsoById($id_lang);

        if (!is_dir(dirname(__FILE__).'/mails/'.Tools::strtolower($iso_lang)))
            $id_lang = Language::getIdByIso('en');

        Mail::Send($id_lang,
            'error_reporting',
            Mail::l('Error reporting from your Przelewy24 module',
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

    private function deleteOrderStates() {
        $os = new OrderState(Configuration::get('NPS_P24_ORDER_STATE_AWAITING'));
        $os->delete();
        $os = new OrderState(Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED'));
        $os->delete();
    }
}