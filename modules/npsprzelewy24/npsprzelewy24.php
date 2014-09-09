<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

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
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

        Configuration::updateValue('NPS_P24_ORDER_STATE_1', 0);
        Configuration::updateValue('NPS_P24_ORDER_STATE_2', 0);
        Configuration::updateValue('NPS_P24_COMMISION', 2.5);
        Configuration::updateValue('NPS_P24_UNIQUE_KEY', '');
        Configuration::updateValue('NPS_P24_CRC_KEY', '');
        Configuration::updateValue('NPS_P24_API_KEY', '');
        Configuration::updateValue('NPS_P24_WEB_SERVICE_URL', 'https://secure.przelewy24.pl/external/wsdl/service.php?wsdl');
        Configuration::updateValue('NPS_P24_URL', 'https://secure.przelewy24.pl');
        Configuration::updateValue('NPS_P24_SANDBOX_URL', 'https://sandbox.przelewy24.pl');
        Configuration::updateValue('NPS_P24_SANDBOX_ERROR', '');
        Configuration::updateValue('NPS_P24_SANDBOX_WEB_SERVICE_URL', 'https://sandbox.przelewy24.pl/external/__wsdl/service.php?wsdl');

        $rq = Db::getInstance()->getRow(
            'SELECT `id_order_state`
            FROM `'._DB_PREFIX_.'order_state_lang`
            WHERE id_lang = \''.pSQL('1').'\'
            AND  name = \''.pSQL('Oczekiwanie na płatność Przelewy24').'\'');
        if ($rq && isset($rq['id_order_state']) && intval($rq['id_order_state']) > 0) {
            Configuration::updateValue('NPS_P24_ORDER_STATE_1', $rq['id_order_state']);
        } else {
            Db::getInstance()->Execute(
                'INSERT INTO `'._DB_PREFIX_.'order_state` (`unremovable`, `invoice`, `module_name`, `color`)
                VALUES(1, 1, \''.$this->name.'\', \'LightBlue\')');
            $stateid = Db::getInstance()->Insert_ID();
            $result = Db::getInstance()->ExecuteS('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`');
            foreach ($result as $row) {
                Db::getInstance()->Execute(
                    'INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`)
                    VALUES('.intval($stateid).', '.intval($row['id_lang']).', \'Oczekiwanie na płatność Przelewy24\')');
            }
            Configuration::updateValue('NPS_P24_ORDER_STATE_1', $stateid);
        }

        $rq = Db::getInstance()->getRow(
            'SELECT `id_order_state`
            FROM `'._DB_PREFIX_.'order_state_lang`
            WHERE id_lang = \''.pSQL('1').'\'
            AND  name = \''.pSQL('Płatność Przelewy24 zatwierdzona').'\'');
        if ($rq && isset($rq['id_order_state']) && intval($rq['id_order_state']) > 0) {
            Configuration::updateValue('NPS_P24_ORDER_STATE_2', $rq['id_order_state']);
        } else {
            Db::getInstance()->Execute(
                'INSERT INTO `'._DB_PREFIX_.'order_state` (`unremovable`, `invoice`, `module_name`, `color`)
                VALUES(1, 1,\''.$this->name.'\', \'RoyalBlue\')');
            $stateid = Db::getInstance()->Insert_ID();
            foreach ($result as $row) {
                Db::getInstance()->Execute(
                    'INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`)
                    VALUES('.intval($stateid).', '.intval($row['id_lang']).', \'Płatność Przelewy24 przyjęta\')');
            }
            Configuration::updateValue('NPS_P24_ORDER_STATE_2', $stateid);
        }

        if (!parent::install()
            || !$this->registerHook('payment')
            || !$this->registerHook('displayOrderDetail')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->createTables($sql))
            return false;
        return true;
    }

    public function uninstall() {
        return parent::uninstall()
            && $this->deleteTables()
            && $this->unregisterHook('displayCustomerAccount')
            && $this->unregisterHook('payment')
            && $this->unregisterHook('displayOrderDetail');
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit')) {
            Configuration::updateValue('NPS_P24_MERCHANT_ID', Tools::getValue('NPS_P24_MERCHANT_ID'));
            Configuration::updateValue('NPS_P24_UNIQUE_KEY', Tools::getValue('NPS_P24_UNIQUE_KEY'));
            Configuration::updateValue('NPS_P24_CRC_KEY', Tools::getValue('NPS_P24_CRC_KEY'));
            Configuration::updateValue('NPS_P24_URL', Tools::getValue('NPS_P24_URL'));
            Configuration::updateValue('NPS_P24_COMMISION', Tools::getValue('NPS_P24_COMMISION'));
            Configuration::updateValue('NPS_P24_API_KEY', Tools::getValue('NPS_P24_API_KEY'));
            $output .= $this->displayConfirmation($this->l('Merchant settings updated sucessfully'));
        } else if (Tools::isSubmit('submitSandbox')) {
            Configuration::updateValue('NPS_P24_SANDBOX_URL', Tools::getValue('NPS_P24_SANDBOX_URL'));
            Configuration::updateValue('NPS_P24_WEB_SERVICE_URL', Tools::getValue('NPS_P24_WEB_SERVICE_URL'));
            Configuration::updateValue('NPS_P24_SANDBOX_ERROR', Tools::getValue('NPS_P24_SANDBOX_ERROR'));
            Configuration::updateValue('NPS_P24_SANDBOX_WEB_SERVICE_URL', Tools::getValue('NPS_P24_SANDBOX_WEB_SERVICE_URL'));
            Configuration::updateValue('NPS_P24_SANDBOX_MODE', Tools::getValue('NPS_P24_SANDBOX_MODE'));
            $output .= $this->displayConfirmation($this->l('Sandbox settings updated sucessfully'));
        } else if (Tools::isSubmit('submitUrls')) {
            Configuration::updateValue('NPS_P24_REGULATIONS_URL', Tools::getValue('NPS_P24_REGULATIONS_URL'));
            $output .= $this->displayConfirmation($this->l('URL\'s settings updated sucessfully'));
        }
        return $output.$this->displayForm();
    }

    /**
     * @return bool
     */
    private function createTables($sql) {
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;
        return true;
    }

    private function deleteTables() {
        return Db::getInstance()->Execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'p24_payment`,
            `'._DB_PREFIX_.'p24_payment_statement`,
            `'._DB_PREFIX_.'p24_seller_company`');
    }

    private function displayForm() {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0] = $this->configurationForm();
        $fields_form[1] = $this->configurationUrlsForm();
        $fields_form[2] = $this->configurationSandboxForm();
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

    private function configurationForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Marketplace Przelewy24 Settings'),
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
                        'label' => $this->l('SOAP Web Service API Key'),
                        'hint' => $this->l('SOPA Web Service API key retrieved from Przelewy24'),
                        'name' => 'NPS_P24_API_KEY',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Web Service URL'),
                        'name' => 'NPS_P24_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('SOAP Web Service URL'),
                        'hint' => $this->l('Endpoint of the Przelewy24 WebService'),
                        'name' => 'NPS_P24_WEB_SERVICE_URL',
                        'required' => true
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

    private function configurationUrlsForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Marketplace Przelewy24 URL\'s Settings'),
                ),
                'input' => array(
                     array(
                        'type' => 'text',
                        'label' => $this->l('Regulations of Przelewy24 URL'),
                        'name' => 'NPS_P24_REGULATIONS_URL',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitUrls',
                )
            )
        );
    }

    private function configurationSandboxForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('nps Marketplace Sandbox Przelewy24 Settings'),
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
                        'label' => $this->l('SOAP Web Service Sandbox URL'),
                        'hint' => $this->l('Endpoint of the Przelewy24 WebService'),
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

    public function hookPayment() {
        $this->context->smarty->assign('p24_payment_url', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation'));
        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookDisplayOrderDetail() {
        global $smarty;

        $orderID = $_GET['id_order'];
        $result = Db::getInstance()->getRow('SELECT module, current_state FROM `'._DB_PREFIX_.'orders` WHERE `id_order`="'.$orderID.'"');

        if(count($result) && $result['module'] == 'przelewy24' && $result['current_state'] == Configuration::get('NPS_P24_ORDER_STATE_1')) {
            $smarty->assign('p24_retryPaymentUrl', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation', array('order_id'=> $orderID)));
            return $this->display(__FILE__, 'renewPaymentOrderDetail.tpl');
        }
    }

    public function reportError($logs = array()) {
        $id_lang = (int)$this->context->language->id;
        $iso_lang = Language::getIsoById($id_lang);

        if (!is_dir(dirname(__FILE__).'/mails/'.Tools::strtolower($iso_lang)))
            $id_lang = Language::getIdByIso('en');

        Mail::Send($id_lang,
            'error_reporting',
            Mail::l('Error reporting from your Przelewy24 module',
            (int)$this->context->language->id),
            array('{logs}' => implode('<br />', $log)),
            Configuration::get('PS_SHOP_EMAIL'),
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.$this->name.'/mails/');
    }
}