<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_PS_VERSION_' ) )
	exit;

if ( !defined( '_NPS_SEL_IMG_DIR_' ) )
    define('_NPS_SEL_IMG_DIR_', _PS_IMG_DIR_.'seller/');

if ( !defined( '_THEME_SEL_DIR_' ) )
    define('_THEME_SEL_DIR_', _PS_IMG_.'seller/');

if ( !defined( '_NPS_MAILS_DIR_' ) )
    define('_NPS_MAILS_DIR_', dirname(__FILE__).'/mails/');

require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class NpsMarketplace extends Module {
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct() {
        $this->name = 'npsmarketplace';
        $this->tab = 'market_place';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Marketplace' );
        $this->description = $this->l( 'Allow customers to add and sell products in your store.' );
        $this->confirmUninstall = $this->l('Are you sure you want to delete module ? This will have serious impact on your products and orders. Think twice before you do that.');
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
            || !$this->registerHook('header')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('productTab')
            || !$this->registerHook('productTabContent')
            || !$this->registerHook('productFooter')
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayMyAccountColumn')
            || !$this->registerHook('displayHome')
            || !$this->registerHook('displayNav')
            || !Configuration::updateValue('NPS_GLOBAL_COMMISION', 3)
            || !Configuration::updateValue('NPS_PRODUCT_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_SELLER_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_PAYMENT_SETTINGS_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_SELLER_AGREEMENT_URL', $shop_url)
            || !Configuration::updateValue('NPS_MERCHANT_EMAILS', Configuration::get('PS_SHOP_EMAIL'))
            || !$this->_createTables($sql)
            || !$this->_createTab()
            || !$this->_createFeatures()
            || !$this->_createAttributes()
            || !mkdir(_NPS_SEL_IMG_DIR_))
            return false;
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall()
            || !$this->unregisterHook('header')
            || !$this->unregisterHook('displayCustomerAccount')
            || !$this->unregisterHook('productTab')
            || !$this->unregisterHook('productTabContent')
            || !$this->unregisterHook('productFooter')
            || !$this->unregisterHook('displayRightColumnProduct')
            || !$this->unregisterHook('displayMyAccountColumn')
            || !$this->unregisterHook('displayHome')
            || !$this->unregisterHook('displayNav')
            || !Configuration::deleteByName('NPS_GLOBAL_COMMISION')
            || !Configuration::deleteByName('NPS_PRODUCT_GUIDE_URL')
            || !Configuration::deleteByName('NPS_SELLER_GUIDE_URL')
            || !Configuration::deleteByName('NPS_SELLER_AGREEMENT_URL')
            || !Configuration::deleteByName('NPS_PAYMENT_SETTINGS_GUIDE_URL')
            || !Configuration::deleteByName('NPS_MERCHANT_EMAILS')
            || !Configuration::deleteByName('NPS_FEATURE_TOWN_ID')
            || !Configuration::deleteByName('NPS_FEATURE_DISTRICT_ID')
            || !Configuration::deleteByName('NPS_FEATURE_ADDRESS_ID')
            || !Configuration::deleteByName('NPS_ATTRIBUTE_DATE_ID')
            || !Configuration::deleteByName('NPS_ATTRIBUTE_TIME_ID')
            || !$this->_deleteTab()
            || !$this->_deleteTables()
            || !Tools::deleteDirectory(_NPS_SEL_IMG_DIR_))
            return false;
        return true;
    }

    public function hookDisplayNav() {
        return $this->display(__FILE__, 'views/templates/hook/header_top.tpl');
    }

    public function hookDisplayRightColumnProduct() {
        $id_product = Tools::getValue('id_product');
        $id_seller = (int)Seller::getSellerByProduct($id_product);

        if(isset($id_seller) && $id_seller > 0) {
            $this->context->controller->addJS(_PS_JS_DIR_.'validate.js');
            $product = new Product($id_product, false, $this->context->language->id);
            $seller = new Seller($id_seller);
            $this->context->smarty->assign(array(
                'seller_shop_url' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
                'seller_name' => $seller->name,
                'sts_secure_key' => $this->secure_key,
                'sts_product_id' => $product->id,
                
            ));
            return $this->display(__FILE__, 'views/templates/hook/product_seller_info.tpl');
        }
    }

    private function getProductObject($product) {
        $cover = Product::getCover($product->id);
        $have_image = !empty($cover);
        return array(
            'url' =>  $this->context->link->getProductLink($product),
            'img' => $have_image ? $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $cover['id_image'], 'home_default') : null,
        );
    }

    public function hookProductFooter($params) {
        $product = $params['product'];
        $seller = new Seller(Seller::getSellerByProduct($product->id));
        $products = $seller->getProducts();
        $count = count($products);
        $p1 = null;
        $p2 = null;
        $p3 = null;
        if ($count > 0) {
            $p1 = $this->getProductObject($products[0]);
        }
        if ($count > 1) {
            $p1 = $this->getProductObject($products[1]);
        }
        if ($count > 2) {
            $p1 = $this->getProductObject($products[2]);
        }
        $this->context->smarty->assign(array(
            'seller' => $seller,
            'logo' => Seller::getImageLink($seller->id, 'cart_default', $this->context),
            'seller_shop_url' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
            'p1' => $p1,
            'p2' => $p2,
            'p3' => $p3,
        ));
        return $this->display(__FILE__, 'views/templates/hook/product_footer.tpl');
    }

    public function hookDisplayMyAccountColumn() {
        $has_address = $this->context->customer->getAddresses($this->context->language->id);
        $this->context->smarty->assign(array(
            'has_customer_an_address' => empty($has_address),
            'voucherAllowed' => (int)CartRule::isFeatureActive(),
            'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN'),
            'HOOK_CUSTOMER_ACCOUNT' => Hook::exec('displayCustomerAccount'),
        ));
        return $this->display(__FILE__, 'views/templates/hook/my_account_column.tpl');
    }

    public function hookDisplayCustomerAccount() {
        $seller = new Seller(null, $this->context->customer->id);
        $account_state = 'none';
        if ($seller->requested == 1 && $seller->active == 0 && $seller->locked == 0)
            $account_state = 'requested';
        else if ($seller->requested == 1 && $seller->active == 1 && $seller->locked == 0)
            $account_state = 'active';
        else if ($seller->requested == 1 && $seller->locked == 1)
            $account_state = 'locked';

        $payment_configured = false;
        if ($account_state == 'active') {
            $payment_config = new P24SellerCompany(null, $seller->id);
            if ($payment_config->id != null)
                $payment_configured = true;
        }
        $this->context->smarty->assign(
            array(
                'account_state' => $account_state,
                'payment_configured' => $payment_configured,
                'products_count' => count($seller->getSellerProducts($seller->id)),
                'seller_request_link' => $this->context->link->getModuleLink('npsmarketplace', 'AccountRequest'),
                'add_product_link' => $this->context->link->getModuleLink('npsmarketplace', 'Product'),
                'products_list_link' => $this->context->link->getModuleLink('npsmarketplace', 'ProductsList'),
                'orders_link' => $this->context->link->getModuleLink('npsmarketplace', 'Orders'),
                'unlock_account_link' => $this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'),
                'seller_profile_link' => $this->context->link->getModuleLink('npsmarketplace', 'SellerAccount', array('id_seller' => $seller->id))
            )
        );
        return $this->display(__FILE__, 'npsmarketplace.tpl');
    }

    public function hookIframe() {
        $seller = new Seller(Tools::getValue('id'));
        $products = $seller->getProducts();

        return $products;
    }

    public function hookHeader() {
        $this->context->controller->addCss(($this->_path).'npsmarketplace.css');
    }

    public function hookDisplayHome() {
        $this->context->smarty->assign(array(
            'home_sellers' => $this->getSellersObjects(),
        ));
        return $this->display(__FILE__, 'views/templates/hook/home_sellers.tpl');
    }

    private function getSellersObjects() {
        $sql = 'SELECT `id_seller`, `name` FROM `'._DB_PREFIX_.'seller` WHERE `active` = 1 AND `locked` = 0 ORDER BY RAND() LIMIT 4';
        
        $ret = array();
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($rows)
            foreach ($rows as $row)
                $ret[] = array(
                    'name' => $row['name'],
                    'url' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $row['id_seller'])),
                    'img' => Seller::getImageLink($row['id_seller'], 'home_default', $this->context),
                );
        return $ret;
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submitConfiguration')) {
            Configuration::updateValue('NPS_GLOBAL_COMMISION', Tools::getValue('NPS_GLOBAL_COMMISION'));
            Configuration::updateValue('NPS_MERCHANT_EMAILS', Tools::getValue('NPS_MERCHANT_EMAILS'));
            $output .= $this->displayConfirmation($this->l('General settings updated'));
        } elseif (Tools::isSubmit('submitUrls')) {
            Configuration::updateValue('NPS_SELLER_AGREEMENT_URL', Tools::getValue('NPS_SELLER_AGREEMENT_URL'));
            Configuration::updateValue('NPS_PRODUCT_GUIDE_URL', Tools::getValue('NPS_PRODUCT_GUIDE_URL'));
            Configuration::updateValue('NPS_SELLER_GUIDE_URL', Tools::getValue('NPS_SELLER_GUIDE_URL'));
            Configuration::updateValue('NPS_PAYMENT_SETTINGS_GUIDE_URL', Tools::getValue('NPS_PAYMENT_SETTINGS_GUIDE_URL'));
            $output .= $this->displayConfirmation($this->l('URL\'s settings updated'));
        }
        return $output.$this->displayForm();
    }

    public function hookProductTab() {
        $id_seller = (int)Seller::getSellerByProduct(Tools::getValue('id_product'));
        if(isset($id_seller) && $id_seller > 0) {
            $seller = new Seller($id_seller);
            $this->context->smarty->assign(array(
                'show_regulations' => $seller->regulations_active,
            ));
            return ($this->display(__FILE__, '/tab.tpl'));
        }
    }

    public function hookProductTabContent() {
        $lang_id = (int)$this->context->language->id;
        $id_product = Tools::getValue('id_product');
        $id_seller = (int)Seller::getSellerByProduct($id_product);
        if(isset($id_seller) && $id_seller > 0) {
            $this->context->controller->addJS (array(
                "https://maps.googleapis.com/maps/api/js",
                _PS_MODULE_DIR_.'npsmarketplace/js/view_map.js'
            ));
            $this->context->controller->addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
            $features = Product::getFeaturesStatic((int)$id_product);
            foreach($features as $feature) {
                if ($feature['id_feature'] == Configuration::get('NPS_FEATURE_ADDRESS_ID')) {
                    $address = new FeatureValue($feature['id_feature_value']);
                    break;
                }
            }
            $seller = new Seller(Seller::getSellerByProduct(Tools::getValue('id_product')));
            $this->context->smarty->assign(array(
                'current_id_lang' => $lang_id,
                'regulations' => $seller->regulations,
                'show_regulations' => $seller->regulations_active,
                'product_address' => isset($address) ? $address->value[$lang_id] : '',
            ));
            return ($this->display(__FILE__, '/tab_content.tpl'));
        }
    }

    private function displayForm()
    {
        $this->context->controller->addJqueryPlugin('tagify');
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0] = $this->configurationForm();
        $fields_form[1] = $this->linksForm();
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
                    'title' => $this->l('General Settings'),
                ),
                'input' => array(
                     array(
                        'type' => 'text',
                        'label' => $this->l('Global commision'),
                        'name' => 'NPS_GLOBAL_COMMISION',
                        'class' => 'fixed-width-xs',
                        'suffix' => '%',
                        'required' => true
                    ),
                    array(
                        'type' => 'tags',
                        'label' => $this->l('Merchant emails'),
                        'name' => 'NPS_MERCHANT_EMAILS',
                        'lang' => false,
                        'hint' => $this->l('To add "emails," click in the field, write something, and then press "Enter."'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitConfiguration',
                )
            )
        );
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
                        'label' => $this->l('Seller Agreement  URL'),
                        'name' => 'NPS_SELLER_AGREEMENT_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Seller Guide  URL'),
                        'name' => 'NPS_SELLER_GUIDE_URL',
                        'required' => true
                    ),
                     array(
                        'type' => 'text',
                        'label' => $this->l('Product Guide URL'),
                        'name' => 'NPS_PRODUCT_GUIDE_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Payment Settings Guide URL'),
                        'name' => 'NPS_PAYMENT_SETTINGS_GUIDE_URL',
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

    public function getConfigFieldsValues()
    {
        return array(
            'NPS_GLOBAL_COMMISION' => Tools::getValue('NPS_GLOBAL_COMMISION', Configuration::get('NPS_GLOBAL_COMMISION')),
            'NPS_PRODUCT_GUIDE_URL' => Tools::getValue('NPS_PRODUCT_GUIDE_URL', Configuration::get('NPS_PRODUCT_GUIDE_URL')),
            'NPS_SELLER_GUIDE_URL' => Tools::getValue('NPS_SELLER_GUIDE_URL', Configuration::get('NPS_SELLER_GUIDE_URL')),
            'NPS_SELLER_AGREEMENT_URL' => Tools::getValue('NPS_SELLER_AGREEMENT_URL', Configuration::get('NPS_SELLER_AGREEMENT_URL')),
            'NPS_MERCHANT_EMAILS' => Tools::getValue('NPS_MERCHANT_EMAILS', Configuration::get('NPS_MERCHANT_EMAILS')),
            'NPS_PAYMENT_SETTINGS_GUIDE_URL' => Tools::getValue('NPS_PAYMENT_SETTINGS_GUIDE_URL', Configuration::get('NPS_PAYMENT_SETTINGS_GUIDE_URL')),

        );
    }

    private function _createTab()
    {
        $tab = new Tab();
        $tab->id_parent = 0;
        $tab->position = 1;
        $tab->module = $this->name;
        $tab->class_name = 'AdminSellersAccounts';
        $languages = Language::getLanguages();
        foreach ($languages AS $language)
            $tab->{'name'}[intval($language['id_lang'])] = $this->l('Marketplace');
        $success = $tab->add();

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminSellersAccounts';
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Sellers');
        }
        $success = $success && $sellers_tab->add();

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminTowns';
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Towns');
        }
        $success = $success && $sellers_tab->add();

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminDistricts';
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Districts');
        }
        $success = $success && $sellers_tab->add();
        return $success;
    }

    private function _deleteTab()
    {
        $tabs = Tab::getCollectionFromModule($this->name);
        if (!empty($tabs))
        {
            foreach ($tabs as $tab)
            {
                $tab->delete();
            }
            return true;
        }
        return false;
    }

    /* Set database */
    private function _createTables($sql) {
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;

        return $this->_alterImageTypeTable();
    }

    private function _deleteTables() {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'seller`,
            `'._DB_PREFIX_.'seller_lang`,
            `'._DB_PREFIX_.'seller_product`,
            `'._DB_PREFIX_.'town`,
            `'._DB_PREFIX_.'town_lang`,
            `'._DB_PREFIX_.'district`')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'image_type` DROP `sellers`');
    }

    private function _alterImageTypeTable() {
        $alterImageType = 'ALTER TABLE  `'._DB_PREFIX_.'image_type` ADD  `sellers` TINYINT(1) NOT NULL AFTER  `stores`';

        $updateImageType = "UPDATE `"._DB_PREFIX_."image_type` SET  `sellers` =  1 WHERE
            `"._DB_PREFIX_."image_type`.`name` IN (
                'cart_default',
                'small_default',
                'medium_default',
                'home_default',
                'large_default')";
        $res = Db::getInstance()->Execute($alterImageType);
        return Db::getInstance()->Execute($updateImageType) && $res;
    }
    
    private function _createFeatures() {
        $names = array('Town', 'District', 'Address');
        foreach ($names as $name) {
            $id = Feature::addFeatureImport($name);
            Configuration::updateValue('NPS_FEATURE_'.strtoupper($name).'_ID', $id);
        }
        return true;
    }

    private function _createAttributes() {
        $d = array();
        $t = array();
        foreach (Language::getLanguages() as $key => $lang) {
            $d[$lang['id_lang']] = 'Date';
            $t[$lang['id_lang']] = 'Time';
        }

        $ag = new AttributeGroup();
        $ag->name = $d;
        $ag->public_name = $d;
        $ag->group_type = 'select';
        $ag->position = -1;
        $ag->save();
        Configuration::updateValue('NPS_ATTRIBUTE_DATE_ID', $ag->id);

        $ag = new AttributeGroup();
        $ag->name = $t;
        $ag->public_name = $t;
        $ag->group_type = 'select';
        $ag->position = -1;
        $ag->save();
        Configuration::updateValue('NPS_ATTRIBUTE_TIME_ID', $ag->id);
        return true;
    }
}
?>
