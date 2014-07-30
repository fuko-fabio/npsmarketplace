<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright  nps software
*  @license    
*/

if ( !defined( '_PS_VERSION_' ) )
	exit;

if ( !defined( '_NPS_SEL_IMG_DIR_' ) )
    define('_NPS_SEL_IMG_DIR_', _PS_IMG_DIR_.'seller/');

if ( !defined( '_THEME_SEL_DIR_' ) )
    define('_THEME_SEL_DIR_', _PS_IMG_.'seller/');

include_once(dirname(__FILE__).'/classes/Seller.php');

class NpsMarketplace extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
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
        $this->confirmUninstall = $this->l('Are you sure you want to delete mymodule ? This will have serious impact on your products and orders. Think twice before you do that.');
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

        if (!parent::install()
            || !$this->registerHook('header')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('productTab')
            || !$this->registerHook('productTabContent')
            || !Configuration::updateValue('NPS_GLOBAL_COMMISION', 3)
            || !Configuration::updateValue('NPS_SELLER_COMMENTS_MODERATE', 1)
            || !Configuration::updateValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS', 0)
            || !Configuration::updateValue('NPS_SELLER_COMMENTS_MINIMAL_TIME', 30)
            || !$this->_createTables($sql)
            || !$this->_createTab())
            return false;
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() 
            || !Configuration::deleteByName('NPS_GLOBAL_COMMISION')
            || !Configuration::deleteByName('NPS_SELLER_COMMENTS_MODERATE')
            || !Configuration::deleteByName('NPS_SELLER_COMMENTS_ALLOW_GUESTS')
            || !Configuration::deleteByName('NPS_SELLER_COMMENTS_MINIMAL_TIME')
            || !$this->_deleteTab()
            || !$this->_deleteTables())
            return false;
        return true;
    }

    public function hookDisplayCustomerAccount( $params )
    {
        $seller = new Seller(null, $this->context->customer->id);

        $account_state = 'none';
        if ($seller->requested == 1 && $seller->active == 0 && $seller->locked == 0)
            $account_state = 'requested';
        else if ($seller->requested == 1 && $seller->active == 1 && $seller->locked == 0)
            $account_state = 'active';
        else if ($seller->requested == 1 && $seller->locked == 1)
            $account_state = 'locked';

        $this->context->smarty->assign(
            array(
                'account_state' => $account_state,
                'seller_request_link' => $this->context->link->getModuleLink('npsmarketplace', 'AccountRequest'),
                'add_product_link' => $this->context->link->getModuleLink('npsmarketplace', 'Product'),
                'products_list_link' => $this->context->link->getModuleLink('npsmarketplace', 'ProductsList'),
                'orders_link' => $this->context->link->getModuleLink('npsmarketplace', 'Orders'),
                'payment_settings_link' => $this->context->link->getModuleLink('npsmarketplace', 'PaymentSettings'),
                'unlock_account_link' => $this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'),
                'seller_profile_link' => $this->context->link->getModuleLink('npsmarketplace', 'SellerAccount', array('id_seller' => $seller->id))
            )
        );
        return $this->display(__FILE__, 'npsmarketplace.tpl');
    }

    public function hookIframe()
    {
        $seller = new Seller(Tools::getValue('id'));
        $products = $seller->getProducts();

        return $products;
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'npsmarketplace.css', 'all');
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submitConfiguration')) {
            Configuration::updateValue('NPS_GLOBAL_COMMISION', Tools::getValue('NPS_GLOBAL_COMMISION'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        } elseif (Tools::isSubmit('submitModerate')) {
            Configuration::updateValue('NPS_SELLER_COMMENTS_MODERATE', (int)Tools::getValue('NPS_SELLER_COMMENTS_MODERATE'));
            Configuration::updateValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS', (int)Tools::getValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS'));
            Configuration::updateValue('NPS_SELLER_COMMENTS_MINIMAL_TIME', (int)Tools::getValue('NPS_SELLER_COMMENTS_MINIMAL_TIME'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output.$this->displayForm();
    }

    public function hookProductTab($params)
    {
        require_once(dirname(__FILE__).'/classes/SellerComment.php');
        require_once(dirname(__FILE__).'/classes/SellerCommentCriterion.php');

        $id_seller = (int)Seller::getSellerByProduct(Tools::getValue('id_product'));
        $average = SellerComment::getAverageGrade((int)$id_seller);

        $this->context->smarty->assign(array(
                                            'allow_guests' => (int)Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS'),
                                            'comments' => SellerComment::getBySeller($id_seller),
                                            'criterions' => SellerCommentCriterion::getBySeller($id_seller, $this->context->language->id),
                                            'averageTotal' => round($average['grade']),
                                            'nbComments' => (int)(SellerComment::getCommentNumber($id_seller))
                                       ));

        return ($this->display(__FILE__, '/tab.tpl'));
    }

    public function hookProductTabContent($params)
    {
        $this->context->controller->addJS($this->_path.'js/jquery.rating.pack.js');
        $this->context->controller->addJS($this->_path.'js/jquery.textareaCounter.plugin.js');
        $this->context->controller->addJS($this->_path.'js/sellercomments.js');

        $seller = new Seller(Seller::getSellerByProduct(Tools::getValue('id_product')));

        $id_guest = (!$id_customer = (int)$this->context->cookie->id_customer) ? (int)$this->context->cookie->id_guest : false;
        $customerComment = SellerComment::getByCustomer($seller->id, (int)$this->context->cookie->id_customer, true, (int)$id_guest);

        $averages = SellerComment::getAveragesBySeller($seller->id, $this->context->language->id);
        $averageTotal = 0;
        foreach ($averages as $average)
            $averageTotal += (float)($average);
        $averageTotal = count($averages) ? ($averageTotal / count($averages)) : 0;

        $product = new Product(Tools::getValue('id_product'));

         $this->context->smarty->assign(array(
             'sellercomments_logged' => $this->context->customer->isLogged(true),
             'sellercomments_action_url' => '',
             'seller' => $seller,
             'sellercomments' => SellerComment::getBySeller($seller->id, 1, null, $this->context->cookie->id_customer),
             'sellercomments_criterions' => SellerCommentCriterion::getBySeller($seller->id, $this->context->language->id),
             'sellercomments_averages' => $averages,
             'sellercomments_path' => $this->_path,
             'sellercomments_averageTotal' => $averageTotal,
             'sellercomments_allow_guests' => (int)Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS'),
             'sellercomments_too_early' => ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME')) > time()),
             'sellercomments_delay' => Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME'),
             'id_sellercomments_form' => $seller->id,
             'sellercomments_secure_key' => $this->secure_key,
             'sellercomments_cover' => '',
             'sellercomments_cover_image' => $this->getSellerImgLink($seller),
             'sellercomments_mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
             'sellercomments_nbComments' => (int)SellerComment::getCommentNumber($seller->id),
             'sellercomments_controller_url' => $this->context->link->getModuleLink('npsmarketplace', 'SellerComments'),
             'sellercomments_url_rewriting_activated' => Configuration::get('PS_REWRITING_SETTINGS', 0),
             'sellercomments_moderation_active' => (int)Configuration::get('NPS_SELLER_COMMENTS_MODERATE'),
             'current_id_lang' => (int)$this->context->language->id,
        ));

        $this->context->controller->pagination((int)SellerComment::getCommentNumber($seller->id));

        return ($this->display(__FILE__, '/views/templates/front/sellercomments.tpl'));
    }

    public function getSellerImgLink($seller, $type = null)
    {
        if($type)
            $uri_path = _THEME_SEL_DIR_.$seller->id.'-'.$type.'.jpg';
        else
            $uri_path = _THEME_SEL_DIR_.$seller->id.($type ? '-'.$type : '').'.jpg';
        return $this->context->link->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
    }

    private function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
         
        // Init Fields form array
        $fields_form[0] = $this->configurationForm();
        $fields_form[1] = $this->moderateForm();
         
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
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitConfiguration',
                )
            )
        );
    }

    public function moderateForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Seller Comments Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('All reviews must be validated by an employee'),
                        'name' => 'NPS_SELLER_COMMENTS_MODERATE',
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
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('Allow guest reviews'),
                        'name' => 'NPS_SELLER_COMMENTS_ALLOW_GUESTS',
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
                        'type' => 'text',
                        'label' => $this->l('Minimum time between 2 reviews from the same user'),
                        'name' => 'NPS_SELLER_COMMENTS_MINIMAL_TIME',
                        'class' => 'fixed-width-xs',
                        'suffix' => 'seconds',
                    ),
                ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitModerate',
                )
            )
        );
    }

    public function getConfigFieldsValues()
    {
        return array(
            'NPS_GLOBAL_COMMISION' => Tools::getValue('NPS_GLOBAL_COMMISION', Configuration::get('NPS_GLOBAL_COMMISION')),
            'NPS_SELLER_COMMENTS_MODERATE' => Tools::getValue('NPS_SELLER_COMMENTS_MODERATE', Configuration::get('NPS_SELLER_COMMENTS_MODERATE')),
            'NPS_SELLER_COMMENTS_ALLOW_GUESTS' => Tools::getValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS', Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS')),
            'NPS_SELLER_COMMENTS_MINIMAL_TIME' => Tools::getValue('NPS_SELLER_COMMENTS_MINIMAL_TIME', Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME')),

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
        $sellers_tab->position = 1;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminSellersComments';
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = 'Comments';
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
    private function _createTables($sql)
    {
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;

        return $this->_alterImageTypeTable();
    }

    private function _deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'seller`,
            `'._DB_PREFIX_.'seller_lang`,
            `'._DB_PREFIX_.'seller_product`,
            `'._DB_PREFIX_.'seller_comment`,
            `'._DB_PREFIX_.'seller_comment_criterion`,
            `'._DB_PREFIX_.'seller_comment_criterion_seller`,
            `'._DB_PREFIX_.'seller_comment_criterion_lang`,
            `'._DB_PREFIX_.'seller_comment_grade`,
            `'._DB_PREFIX_.'seller_comment_usefulness`,
            `'._DB_PREFIX_.'seller_comment_report`');
    }

    private function _alterImageTypeTable() {
        $alterImageType = 'ALTER TABLE  `'._DB_PREFIX_.'image_type` ADD  `sellers` TINYINT(1) NOT NULL AFTER  `stores`';

        $updateImageType = "UPDATE `"._DB_PREFIX_."_image_type` SET  `sellers` =  1 WHERE
            `"._DB_PREFIX_."_image_type`.`name` IN (
                'cart_default',
                'small_default',
                'medium_default',
                'home_default',
                 'large_default')";

        $sql = 'SELECT * FROM '._DB_PREFIX_.'image_type';
        $result = Db::getInstance()->ExecuteS($sql);
        if (!isset($result[0]['sellers']))
            return Db::getInstance()->Execute($alterImageType) && Db::getInstance()->Execute($updateImageType);
        else 
          return true;
    }
}
?>
