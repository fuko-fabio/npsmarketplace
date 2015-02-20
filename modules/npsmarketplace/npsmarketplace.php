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
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Town.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Province.php');
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class NpsMarketplace extends Module {
    const INSTALL_SQL_FILE = 'install.sql';
    const GEO_DATA_FILE_NAME = 'GeoLiteCity.dat';

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
            || !$this->registerHook('extraLogo')
            || !Configuration::updateValue('NPS_GLOBAL_COMMISION', 3)
            || !Configuration::updateValue('NPS_PRODUCT_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_SELLER_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_PAYMENT_SETTINGS_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_SELLER_AGREEMENT_URL', $shop_url)
            || !Configuration::updateValue('NPS_EVENT_VIDEO_GUIDE_URL', $shop_url)
            || !Configuration::updateValue('NPS_MERCHANT_EMAILS', Configuration::get('PS_SHOP_EMAIL'))
            || !$this->_createTables($sql)
            || !$this->_createTab()
            || !$this->_createFeatures()
            || !$this->_createAttributes()
            || !mkdir(_NPS_SEL_IMG_DIR_))
            return false;
        return true;
    }

    public function uninstall() {return $this->initProvinces();
        return $this->registerHook('extraLogo');
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
            || !$this->unregisterHook('extraLogo')
            || !$this->_deleteTab()
            || !$this->_deleteTables()
            || !$this->_deleteFeatures()
            || !$this->_deleteAttributes()
            || !Configuration::deleteByName('NPS_GLOBAL_COMMISION')
            || !Configuration::deleteByName('NPS_PRODUCT_GUIDE_URL')
            || !Configuration::deleteByName('NPS_SELLER_GUIDE_URL')
            || !Configuration::deleteByName('NPS_USER_GUIDE_URL')
            || !Configuration::deleteByName('NPS_SELLER_AGREEMENT_URL')
            || !Configuration::deleteByName('NPS_PAYMENT_SETTINGS_GUIDE_URL')
            || !Configuration::deleteByName('NPS_EVENT_VIDEO_GUIDE_URL')
            || !Configuration::deleteByName('NPS_MERCHANT_EMAILS')
            || !Configuration::deleteByName('NPS_FEATURE_TOWN_ID')
            || !Configuration::deleteByName('NPS_FEATURE_PROVINCE_ID')
            || !Configuration::deleteByName('NPS_FEATURE_DISTRICT_ID')
            || !Configuration::deleteByName('NPS_FEATURE_ADDRESS_ID')
            || !Configuration::deleteByName('NPS_FEATURE_ENTRIES_ID')
            || !Configuration::deleteByName('NPS_FEATURE_FROM_ID')
            || !Configuration::deleteByName('NPS_FEATURE_TO_ID')
            || !Configuration::deleteByName('NPS_ATTRIBUTE_DATE_ID')
            || !Configuration::deleteByName('NPS_ATTRIBUTE_TIME_ID')
            || !Tools::deleteDirectory(_NPS_SEL_IMG_DIR_))
            return false;
        return true;
    }

    public function hookExtraLogo() {
        $id_town = $this->context->cookie->main_town;
        $t = new Town($id_town);
        $this->smarty->assign(array(
            'town_name' => $id_town != 0 ? $t->name[$this->context->language->id] : null,
        ));
        return $this->display(__FILE__, 'views/templates/hook/top.tpl');
    }

    public function hookDisplayNav() { 
	if(!isset($this->context->cookie->main_town) || !isset($this->context->cookie->main_province)) {
            $this->setCurrentLocation();
        }
        $this->context->smarty->assign(array(
            'towns' => Town::getActiveTowns($this->context->language->id),
            'provinces' => Province::getActiveProvinces($this->context->language->id, true, true),
            'nps_ajax_url' => $this->context->link->getModuleLink('npsmarketplace', 'Ajax'),
        ));
        return $this->display(__FILE__, 'views/templates/hook/header_top.tpl');
    }

    private function setCurrentLocation() {
        $georecord = $this->getLocationByIP();
        $id_town = null;
        if ($georecord != null) {
            $id_town = Town::getIdByName($georecord->city);
        }
        if ($id_town == null) {
            $id_town = Town::getDefaultTownId();
        }
        if ($id_town != null) {
            $town = new Town($id_town);
            $this->context->cookie->__set('main_town', $town->id);
            $this->context->cookie->__set('main_province', $town->id_province);
        }
    }

    private function getLocationByIP() {
        if (!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1'))) {
            /* Check if Maxmind Database exists */
            if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat')) {
                include_once(_PS_GEOIP_DIR_.'geoipcity.inc');

                $gi = geoip_open(realpath(_PS_GEOIP_DIR_.GEO_DATA_FILE_NAME), GEOIP_STANDARD);
                return geoip_record_by_addr($gi, Tools::getRemoteAddr());
            }
        }
        return null;
    }

    public static function filterByLocation($products, $id_province = null, $id_town = null) {
        if (!isset($id_province) && !isset($id_town)) {
            $id_province = (int)Context::getContext()->cookie->main_province;
            $id_town = (int)Context::getContext()->cookie->main_town;
        }
        if ($id_town > 0) {
            $result = array();
            $town = new Town($id_town);
            foreach ($products as $product) {
                foreach ($product['features'] as $key => $feature) {
                    $found = false;
                    foreach ($town->name as $key => $name) {
                        if ($feature['value'] == $name) {
                            $result[] = $product;
                            $found = true;
                            break;
                        }
                    }
                    if ($found)
                        break;
                }
            }
            return $result;
        } else if ($id_province > 0) {
            $result = array();
            $province = new Province($id_province);
            foreach ($products as $product) {
                foreach ($product['features'] as $key => $feature) {
                    $found = false;
                    foreach ($province->name as $key => $name) {
                        if ($feature['value'] == $name) {
                            $result[] = $product;
                            $found = true;
                            break;
                        }
                    }
                    if ($found)
                        break;
                }
            }
            return $result;
        }
        return $products;
    }

    public function hookDisplayRightColumnProduct() {
        $id_product = Tools::getValue('id_product');
        if (!Product::showSellerDetails($id_product)) {
            return;
        }
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
                'nps_ajax_url' => $this->context->link->getModuleLink('npsmarketplace', 'Ajax'),
                'date_id_attribute_group' => Configuration::get('NPS_ATTRIBUTE_DATE_ID'),
                'time_id_attribute_group' => Configuration::get('NPS_ATTRIBUTE_TIME_ID')
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
        if (!Product::showSellerDetails($product->id)) {
            return;
        }
        $seller = new Seller(Seller::getSellerByProduct($product->id));
        $products = $seller->getProducts(3, false);
        $p1 = null;
        $p2 = null;
        $p3 = null;
        if (isset($products[0])) {
            $p1 = $this->getProductObject($products[0]);
        }
        if (isset($products[1])) {
            $p2 = $this->getProductObject($products[1]);
        }
        if (isset($products[2])) {
            $p3 = $this->getProductObject($products[2]);
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
                'products_count' => count(Seller::getSellerProducts($seller->id)),
                'seller_request_link' => $this->context->link->getModuleLink('npsmarketplace', 'AccountRequest'),
                'add_product_link' => $this->context->link->getModuleLink('npsmarketplace', 'Product'),
                'products_list_link' => $this->context->link->getModuleLink('npsmarketplace', 'ProductsList'),
                'orders_link' => $this->context->link->getModuleLink('npsmarketplace', 'Orders'),
                'unlock_account_link' => $this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'),
                'seller_profile_link' => $this->context->link->getModuleLink('npsmarketplace', 'SellerAccount', array('id_seller' => $seller->id)),
                'marketing_link' => $this->context->link->getModuleLink('npsmarketplace', 'Marketing')
            )
        );
        return $this->display(__FILE__, 'npsmarketplace.tpl');
    }

    public function getIframeCode() {
        $id_seller = Seller::getSellerIdByCustomer($this->context->customer->id);
        $id_lang = Tools::getValue('id_lang');
        $id_town = Tools::getValue('id_town');
        $in_row = Tools::getValue('in_row');
        $max_events = Tools::getValue('max_events');
        $width = Tools::getValue('width');
        $shop_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        $key = Tools::encrypt($this->name.$id_seller.$id_lang,$id_town);

        $item = $width/$in_row;
        $height = ceil((($item + ($item * 0.25)) * ($max_events / $in_row)) + 40);
        $this->context->smarty->assign(
            array(
                'url' => $shop_url.'modules/npsmarketplace/iframe.php?id_lang='.$id_lang.'&id_town='.$id_town.'&id_seller='.$id_seller.'&max='.$max_events.'&row='.$in_row.'&key='.$key,
                'id_seller' => $id_seller,
                'width' => $width,
                'height' => $height,
            )
        );
        return $this->display(__FILE__, 'views/templates/hook/iframe_template.tpl');
    }

    public function hookIframe() {
        $id_seller = (int)Tools::getValue('id_seller');
        $id_lang = (int)Tools::getValue('id_lang');
        $id_town = (int)Tools::getValue('id_town');
        $max_items = (int)Tools::getValue('max');
        $in_row = (int)Tools::getValue('row');
        $key = Tools::getValue('key');
        $width = Tools::getValue('width');

        if ($key != Tools::encrypt($this->name.$id_seller.$id_lang,$id_town))
            return '';
        $seller = new Seller($id_seller);
        if (isset($id_lang) && !empty($id_lang))
            $lang = new Language($id_lang);
        else
            $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        if (!isset($max_items) || empty($max_items))
            $max_items = 4;
        if (!isset($in_row) || empty($in_row))
            $in_row = 4;
        $ids = Seller::getSellerProducts($seller->id, 0, $max_items, true, true);
        $products = array();
        if(!empty($ids)) {
            $products = Product::getProductsByIds($lang->id, $ids, null, null, false, null, null, $this->context);
            $products = $this->filterByLocation($products, null, $id_town);
        }
        $this->context->smarty->assign(
            array(
                'lang' => $lang->iso_code,
                'title' => $this->l('LabsInTown iframe title.'),
                'description' => $this->l('LabsInTown iframe promotion text.'),
                'max_items' => $max_items,
                'in_row' => $in_row,
                'products' => $products,
                'shop_url' => Tools::getHttpHost(true).__PS_BASE_URI__,
                'css_urls' => array(
                    _THEME_CSS_DIR_.'modules/npsmarketplace/lit.css',
                    _THEME_CSS_DIR_.'global.css'
                ),
            )
        );

        return $this->display(__FILE__, 'views/templates/hook/iframe.tpl');
    }

    public function hookHeader() {
        $this->context->controller->addJS(($this->_path).'js/main_town.js');
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
            Configuration::updateValue('NPS_SPECIAL_CATEGORIES', Tools::getValue('NPS_SPECIAL_CATEGORIES'));
            Configuration::updateValue('NPS_INVISIBLE_CATEGORIES', Tools::getValue('NPS_INVISIBLE_CATEGORIES'));
            Configuration::updateValue('NPS_FREE_CATEGORY_ID', Tools::getValue('NPS_FREE_CATEGORY_ID'));
            $output .= $this->displayConfirmation($this->l('General settings updated'));
        } elseif (Tools::isSubmit('submitUrls')) {
            Configuration::updateValue('NPS_SELLER_AGREEMENT_URL', Tools::getValue('NPS_SELLER_AGREEMENT_URL'));
            Configuration::updateValue('NPS_PRODUCT_GUIDE_URL', Tools::getValue('NPS_PRODUCT_GUIDE_URL'));
            Configuration::updateValue('NPS_SELLER_GUIDE_URL', Tools::getValue('NPS_SELLER_GUIDE_URL'));
            Configuration::updateValue('NPS_PAYMENT_SETTINGS_GUIDE_URL', Tools::getValue('NPS_PAYMENT_SETTINGS_GUIDE_URL'));
            Configuration::updateValue('NPS_EVENT_VIDEO_GUIDE_URL', Tools::getValue('NPS_EVENT_VIDEO_GUIDE_URL'));
            Configuration::updateValue('NPS_EVENT_DESC_GUIDE_URL', Tools::getValue('NPS_EVENT_DESC_GUIDE_URL'));
            Configuration::updateValue('NPS_USER_GUIDE_URL', Tools::getValue('NPS_USER_GUIDE_URL'));
            Configuration::updateValue('NPS_EVENT_IMAGE_GUIDE_URL', Tools::getValue('NPS_EVENT_IMAGE_GUIDE_URL'));
            Configuration::updateValue('NPS_TICKETS_INFO_URL', Tools::getValue('NPS_TICKETS_INFO_URL'));
            $output .= $this->displayConfirmation($this->l('URL\'s settings updated'));
        }
        return $output.$this->displayForm();
    }

    public function hookProductTab($params) {
        $product = $params['product'];
        $id_seller = (int)Seller::getSellerByProduct($product->id);
        if(isset($id_seller) && $id_seller > 0) {
            $seller = new Seller($id_seller);
            $extras = Product::getExtras($product->id, $this->context->language->id);
            $this->context->smarty->assign(array(
                'video_url' => $extras['video'],
                'show_seller_details' => $extras['type'] != 3
            ));
            return ($this->display(__FILE__, '/tab.tpl'));
        }
    }

    public function hookProductTabContent($params) {
        $product = $params['product'];
        $lang_id = (int)$this->context->language->id;
        $id_seller = (int)Seller::getSellerByProduct($product->id);
        if(isset($id_seller) && $id_seller > 0) {
            $this->context->controller->addJS (array(
                "https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places",
                _PS_MODULE_DIR_.'npsmarketplace/js/view_map.js'
            ));
            $this->context->controller->addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
            $features = Product::getFeaturesStatic((int)$product->id);
            foreach($features as $feature) {
                if ($feature['id_feature'] == Configuration::get('NPS_FEATURE_ADDRESS_ID')) {
                    $address = new FeatureValue($feature['id_feature_value']);
                    continue;
                } else if ($feature['id_feature'] == Configuration::get('NPS_FEATURE_TOWN_ID')) {
                    $town = new FeatureValue($feature['id_feature_value']);
                    continue;
                } else if ($feature['id_feature'] == Configuration::get('NPS_FEATURE_DISTRICT_ID')) {
                    $district = new FeatureValue($feature['id_feature_value']);
                    continue;
                }
            }
            $extras = Product::getExtras($product->id, $this->context->language->id);
            $seller = new Seller(Seller::getSellerByProduct(Tools::getValue('id_product')));
            $this->context->smarty->assign(array(
                'current_id_lang' => $lang_id,
                'regulations' => $seller->regulations,
                'product_address' => isset($address) ? $address->value[$lang_id] : '',
                'product_town' => isset($town) ? $town->value[$lang_id] : '',
                'product_district' => isset($district) ? $district->value[$lang_id] : '',
                'video_url' => $extras['video'],
                'show_seller_details' => $extras['type'] != 3
            ));
            return ($this->display(__FILE__, '/tab_content.tpl'));
        }
    }

    private function displayForm() {
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
                    array(
                        'type' => 'tags',
                        'label' => $this->l('Special categories ID\'s'),
                        'name' => 'NPS_SPECIAL_CATEGORIES',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'tags',
                        'label' => $this->l('Invisible categories IS\'s'),
                        'name' => 'NPS_INVISIBLE_CATEGORIES',
                        'lang' => false,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Free category ID'),
                        'name' => 'NPS_FREE_CATEGORY_ID',
                        'required' => false
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
                        'label' => $this->l('User Guide  URL'),
                        'name' => 'NPS_USER_GUIDE_URL',
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
                    array(
                        'type' => 'text',
                        'label' => $this->l('Adding video URL'),
                        'name' => 'NPS_EVENT_VIDEO_GUIDE_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Creating description URL'),
                        'name' => 'NPS_EVENT_DESC_GUIDE_URL',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Creating images URL'),
                        'name' => 'NPS_EVENT_IMAGE_GUIDE_URL',
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

    public function getConfigFieldsValues() {
        return array(
            'NPS_GLOBAL_COMMISION' => Tools::getValue('NPS_GLOBAL_COMMISION', Configuration::get('NPS_GLOBAL_COMMISION')),
            'NPS_PRODUCT_GUIDE_URL' => Tools::getValue('NPS_PRODUCT_GUIDE_URL', Configuration::get('NPS_PRODUCT_GUIDE_URL')),
            'NPS_USER_GUIDE_URL' => Tools::getValue('NPS_USER_GUIDE_URL', Configuration::get('NPS_USER_GUIDE_URL')),
            'NPS_SELLER_GUIDE_URL' => Tools::getValue('NPS_SELLER_GUIDE_URL', Configuration::get('NPS_SELLER_GUIDE_URL')),
            'NPS_SELLER_AGREEMENT_URL' => Tools::getValue('NPS_SELLER_AGREEMENT_URL', Configuration::get('NPS_SELLER_AGREEMENT_URL')),
            'NPS_MERCHANT_EMAILS' => Tools::getValue('NPS_MERCHANT_EMAILS', Configuration::get('NPS_MERCHANT_EMAILS')),
            'NPS_PAYMENT_SETTINGS_GUIDE_URL' => Tools::getValue('NPS_PAYMENT_SETTINGS_GUIDE_URL', Configuration::get('NPS_PAYMENT_SETTINGS_GUIDE_URL')),
            'NPS_EVENT_VIDEO_GUIDE_URL' => Tools::getValue('NPS_EVENT_VIDEO_GUIDE_URL', Configuration::get('NPS_EVENT_VIDEO_GUIDE_URL')),
            'NPS_EVENT_DESC_GUIDE_URL' => Tools::getValue('NPS_EVENT_DESC_GUIDE_URL', Configuration::get('NPS_EVENT_DESC_GUIDE_URL')),
            'NPS_EVENT_IMAGE_GUIDE_URL' => Tools::getValue('NPS_EVENT_IMAGE_GUIDE_URL', Configuration::get('NPS_EVENT_IMAGE_GUIDE_URL')),
            'NPS_SPECIAL_CATEGORIES' => Tools::getValue('NPS_SPECIAL_CATEGORIES', Configuration::get('NPS_SPECIAL_CATEGORIES')),
            'NPS_INVISIBLE_CATEGORIES' => Tools::getValue('NPS_INVISIBLE_CATEGORIES', Configuration::get('NPS_INVISIBLE_CATEGORIES')),
            'NPS_FREE_CATEGORY_ID' => Tools::getValue('NPS_FREE_CATEGORY_ID', Configuration::get('NPS_FREE_CATEGORY_ID')),
        );
    }

    private function _createTab() {
        $tab = new Tab();
        $tab->id_parent = 0;
        $tab->position = 1;
        $tab->module = $this->name;
        $tab->class_name = 'AdminSellersAccounts';
        $languages = Language::getLanguages();
        foreach ($languages AS $language) {
            $tab->{'name'}[intval($language['id_lang'])] = $this->l('Marketplace');
        }
        $success = $tab->add();

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminSellersAccounts';
        foreach ($languages AS $language) {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Sellers');
        }
        $success = $success && $sellers_tab->add();

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminTowns';
        foreach ($languages AS $language) {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Towns');
        }
        $success = $success && $sellers_tab->add();

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminDistricts';
        foreach ($languages AS $language) {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Districts');
        }
        $success = $success && $sellers_tab->add();
        return $success;
    }

    private function _deleteTab() {
        $tabs = Tab::getCollectionFromModule($this->name);
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
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

        return $this->_alterTables();
    }

    private function _deleteTables() {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'seller`,
            `'._DB_PREFIX_.'seller_lang`,
            `'._DB_PREFIX_.'seller_product`,
            `'._DB_PREFIX_.'product_attribute_expiry_date`,
            `'._DB_PREFIX_.'town`,
            `'._DB_PREFIX_.'town_lang`,
            `'._DB_PREFIX_.'province`,
            `'._DB_PREFIX_.'province_lang`,
            `'._DB_PREFIX_.'district`')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'image_type` DROP `sellers`')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `type`')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `video`')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `lat`')
            && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `lng`');
    }

    private function _alterTables() {
        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'image_type` ADD  `sellers` TINYINT(1) NOT NULL AFTER  `stores`';
        $res = Db::getInstance()->Execute($sql);

        $sql = "UPDATE `"._DB_PREFIX_."image_type` SET  `sellers` =  1 WHERE
            `"._DB_PREFIX_."image_type`.`name` IN (
                'cart_default',
                'small_default',
                'medium_default',
                'home_default',
                'large_default')";
        $res = $res && Db::getInstance()->Execute($sql);

        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'product` ADD  `type` TINYINT(1) NOT NULL AFTER  `is_virtual`';
        $res = $res && Db::getInstance()->Execute($sql);

        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'product` ADD  `video` text NULL AFTER  `type`';
        $res = $res && Db::getInstance()->Execute($sql);
        
        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'product` ADD  `lat` decimal(9,6) NULL AFTER  `video`';
        $res = $res && Db::getInstance()->Execute($sql);

        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'product` ADD  `lng` decimal(9,6) NULL AFTER  `lat`';
        return $res && Db::getInstance()->Execute($sql);
    }

    private function _createFeatures() {
        $names = array('Province', 'Town', 'District', 'Address', 'Entries', 'From', 'To');
        foreach ($names as $name) {
            $id = Feature::addFeatureImport($name);
            Configuration::updateValue('NPS_FEATURE_'.strtoupper($name).'_ID', $id);
        }
        return true;
    }

    private function _deleteFeatures() {
        $ids = array(
            Configuration::get('NPS_FEATURE_PROVINCE_ID'),
            Configuration::get('NPS_FEATURE_TOWN_ID'),
            Configuration::get('NPS_FEATURE_DISTRICT_ID'),
            Configuration::get('NPS_FEATURE_ADDRESS_ID'),
            Configuration::get('NPS_FEATURE_ENTRIES_ID'),
            Configuration::get('NPS_FEATURE_FROM_ID'),
            Configuration::get('NPS_FEATURE_TO_ID'),
        );
        $result = true;
        foreach ($ids as $id) {
            $f = new Feature($id);
            $result = $result && $f->delete();
        }
        return $result;
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

    private function _deleteAttributes() {
        $ad = new AttributeGroup(Configuration::get('NPS_ATTRIBUTE_DATE_ID'));
        $at = new AttributeGroup(Configuration::get('NPS_ATTRIBUTE_TIME_ID'));
        return $ad->delete() && $at->delete();
    }

    private function initProvinces() {
        $sql = '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'province` (
              `id_province` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `id_feature_value` int(10) unsigned NOT NULL,
              `active` tinyint(1) NOT NULL,
              `selectable` tinyint(1) NOT NULL,
              PRIMARY KEY (`id_province`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
            
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'province_lang` (
              `id_lang` int(10) unsigned NOT NULL,
              `id_province` int(10) unsigned NOT NULL,
              `name` varchar(64) NOT NULL,
              KEY `id_province` (`id_province`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'town` ADD  `id_province` int(10) unsigned NOT NULL AFTER  `id_town`';
        Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE  `'._DB_PREFIX_.'town` ADD  `selectable` tinyint(1) NOT NULL DEFAULT 1 AFTER `default`';
        Db::getInstance()->execute($sql);

        $id = Feature::addFeatureImport('Province');
        Configuration::updateValue('NPS_FEATURE_PROVINCE_ID', $id);

        $dbquery = new DbQuery();
        $dbquery->select('id_parent')
            ->from('tab')
            ->where('`class_name` = "AdminTowns"');
        $id = Db::getInstance()->getValue($dbquery);
        $languages = Language::getLanguages();
        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminProvinces';
        foreach ($languages AS $language) {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = $this->l('Provinces');
        }
        return $sellers_tab->add();
    }
}
?>
