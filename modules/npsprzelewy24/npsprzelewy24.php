<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

class NpsPrzelewy24 extends PaymentModule {

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

        if (Configuration::get('NPS_P24_ORDER_STATE_1') === null){
            Configuration::updateValue('NPS_P24_ORDER_STATE_1', 1);
        }
        if (Configuration::get('NPS_P24_ORDER_STATE_2') === null){
            Configuration::updateValue('NPS_P24_ORDER_STATE_2', 2);
        }

        if (Configuration::get('NPS_P24_INSTALLMENT_SHOW') === null){
            Configuration::updateValue('NPS_P24_INSTALLMENT_SHOW', 0);
        }
    }

    /**
     * @return bool
     */
    public function install() {
        parent::install();
        
        if (!defined('NPS_P24_ORDER_STATE_1')) {
            $rq = Db::getInstance()->getRow('SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state_lang` WHERE id_lang = \''.pSQL('1').'\' AND  name = \''.pSQL('Oczekiwanie na płatność Przelewy24').'\'');
            if ($rq && isset($rq['id_order_state']) && intval($rq['id_order_state']) > 0) {
                define('NPS_P24_ORDER_STATE_1', $rq['id_order_state']);
                Configuration::updateValue('NPS_P24_ORDER_STATE_1', $rq['id_order_state']);
            } else {
                Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state` (`unremovable`, `color`) VALUES(1, \'lightblue\')');
                $stateid = Db::getInstance()->Insert_ID();
                $result = Db::getInstance()->ExecuteS('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`');
                foreach ($result as $row) {
                    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`) VALUES('.intval($stateid).', '.intval($row['id_lang']).', \'Oczekiwanie na płatność Przelewy24\')');
                }

                define('NPS_P24_ORDER_STATE_1', $stateid);
                Configuration::updateValue('NPS_P24_ORDER_STATE_1', $stateid);
            }
        }

        if (!defined('NPS_P24_ORDER_STATE_2')) {
            $rq = Db::getInstance()->getRow('SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state_lang` WHERE id_lang = \''.pSQL('1').'\' AND  name = \''.pSQL('Płatność Przelewy24 zatwierdzona').'\'');
            if ($rq && isset($rq['id_order_state']) && intval($rq['id_order_state']) > 0) {
                define('NPS_P24_ORDER_STATE_2', $rq['id_order_state']);
                Configuration::updateValue('NPS_P24_ORDER_STATE_2', $rq['id_order_state']);
            } else {
                Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state` (`unremovable`, `color`) VALUES(1, \'lightblue\')');
                $stateid = Db::getInstance()->Insert_ID();
                foreach ($result as $row) {
                    Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'order_state_lang` (`id_order_state`, `id_lang`, `name`) VALUES('.intval($stateid).', '.intval($row['id_lang']).', \'Płatność Przelewy24 przyjęta\')');
                }
                define('NPS_P24_ORDER_STATE_2', $stateid);
                Configuration::updateValue('NPS_P24_ORDER_STATE_2', $stateid);
            }
        }
        if (!$this->registerHook('payment')
            || !$this->registerHook('displayOrderDetail')
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->createAmountTable())
            return false;
        return true;
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit')) {
            Configuration::updateValue('NPS_P24_MERCHANT_ID', Tools::getValue('NPS_P24_MERCHANT_ID'));
            Configuration::updateValue('NPS_P24_UNIQUE_KEY', Tools::getValue('NPS_P24_UNIQUE_KEY'));
            Configuration::updateValue('NPS_P24_SANDBOX_MODE', Tools::getValue('NPS_P24_SANDBOX_MODE'));
            Configuration::updateValue('NPS_P24_INSTALLMENT_SHOW', Tools::getValue('NPS_P24_INSTALLMENT_SHOW'));
            $output .= $this->displayConfirmation($this->l('Settings updated sucessfully'));
        }
        return $output.$this->displayForm();
    }

    /**
     * @return bool
     */
    private function createAmountTable() {
        Db::getInstance()->Execute('CREATE TABLE `'._DB_PREFIX_.'przelewy24_amount` (
            `i_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `s_sid` char(32) NOT NULL,
            `i_id_order` INT UNSIGNED NOT NULL ,
            `i_amount` INT UNSIGNED NOT NULL
            ) ENGINE=MYISAM;');

        return true;
    }

    private function displayForm() {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0] = $this->configurationForm();
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
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
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
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Installment Settings'),
                        'name' => 'NPS_P24_INSTALLMENT_SHOW',
                        'values' => array(
                            array(
                                'id' => 'is_two',
                                'value' => 2,
                                'label' => $this->l('button (payment page) and information (product page)')
                            ),
                            array(
                                'id' => 'is_one',
                                'value' => 1,
                                'label' => $this->l('button (payment page) only')
                            ),
                            array(
                                'id' => 'is_zero',
                                'value' => 0,
                                'label' => $this->l('hide all')
                            ),
                        )
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

    public function getConfigFieldsValues() {
        return array(
            'NPS_P24_MERCHANT_ID' => Tools::getValue('NPS_P24_MERCHANT_ID', Configuration::get('NPS_P24_MERCHANT_ID')),
            'NPS_P24_UNIQUE_KEY' => Tools::getValue('NPS_PRODUCT_GUIDE_URL', Configuration::get('NPS_P24_UNIQUE_KEY')),
            'NPS_P24_SANDBOX_MODE' => Tools::getValue('NPS_SELLER_GUIDE_URL', Configuration::get('NPS_P24_SANDBOX_MODE')),
            'NPS_P24_INSTALLMENT_SHOW' => Tools::getValue('NPS_P24_INSTALLMENT_SHOW', Configuration::get('NPS_P24_INSTALLMENT_SHOW')),
        );
    }

    public function hookPayment() {
        global $smarty, $cart;
        $amount = $cart->getOrderTotal(true, Cart::BOTH);
        $protocol = NpsPrzelewy24::getHttpProtocol();

        $smarty->assign('p24_url', $protocol.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__);
        $smarty->assign('p24_url_payment', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation'));
        #TODO Payment method??
        $smarty->assign('p24_url_installment', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation', array('payment_method' => '129')));
        if($amount >= 300){
            $smarty->assign('p24_installment_show', Configuration::get('NPS_P24_INSTALLMENT_SHOW'));
        }
        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookDisplayRightColumnProduct(){
        global $smarty, $cookie;
        if(Configuration::get('NPS_P24_INSTALLMENT_SHOW') == 2){
            $productID = Tools::getValue('id_product');

            if (isset($productID) && $productID != '')
            {
                $product = new Product((int)$productID, true, $cookie->id_lang);
                $amount = $product->getPrice(true,null,2,null,false,true,1);
                if($amount >= 300){
                    $amountGrosh = $amount*100;

                    $isCurl=function_exists('curl_init')&&
                        function_exists('curl_setopt')&&
                        function_exists('curl_exec')&&
                        function_exists('curl_close');

                    if($isCurl){
                        $userAgent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
                        $url = "https://secure.przelewy24.pl/kalkulator_raty.php?ammount=".$amountGrosh;

                        $curlConnection = curl_init();
                        curl_setopt($curlConnection, CURLOPT_URL,$url);
                        curl_setopt($curlConnection, CURLOPT_SSL_VERIFYHOST, 2);
                        curl_setopt($curlConnection, CURLOPT_USERAGENT, $userAgent);
                        curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($curlConnection, CURLOPT_SSL_VERIFYPEER, FALSE);
                        $result = curl_exec ($curlConnection);

                    }

                    $result_tab = explode('<br>', $result);

                    $smarty->assign(array(
                        'part_count' => $result_tab[0],
                        'part_cost' => $result_tab[1],
                        'product_ammount' => $amount,
                    ));

                    return $this->display(__FILE__, 'installmentRightColumnProduct.tpl');
                }
            }
        }
    }

    public function hookDisplayOrderDetail(){
        global $smarty;

        $orderID = $_GET['id_order'];
        $result = Db::getInstance()->getRow('SELECT module, current_state FROM `'._DB_PREFIX_.'orders` WHERE `id_order`="'.$orderID.'"');

        if(count($result) && $result['module'] == 'przelewy24' && $result['current_state'] == Configuration::get('NPS_P24_ORDER_STATE_1')) {
            $smarty->assign('p24_retryPaymentUrl', $this->context->link->getModuleLink('npsprzelewy24', 'paymentConfirmation', array('order_id'=> $orderID)));
            return $this->display(__FILE__, 'renewPaymentOrderDetail.tpl');
        }
    }

    public static function getHttpProtocol() {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            return 'https://';
        } else {
            return 'http://';
        }
    }
}