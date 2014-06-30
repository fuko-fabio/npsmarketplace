<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright  nps software
*  @license    
*/
if ( !defined( '_PS_VERSION_' ) )
	exit;
class NpsMarketplace extends Module
{
    public function __construct()
    {
        $this->name = 'npsmarketplace';
        $this->tab = 'market_place';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l( 'nps Marketplace' );
        $this->description = $this->l( 'Allow customers to add and sell products in your store.' );
        $this->confirmUninstall = $this->l('Are you sure you want to delete mymodule ? This will have serious impact on your products and orders. Think twice before you do that.');
    }

    public function install()
    {
        if (!parent::install() 
            || !$this->registerHook('displayCustomerAccount')
            || !Configuration::updateValue('GLOBAL_COMMISION', 0)
            || !Configuration::updateValue('AUTO_ENABLE_SELLER_ACCOUNT', 0)
            || !Configuration::updateValue('EMAIL_ADDRESS', '')
            || !$this->_createTables()
            || !$this->_createTab())
            return false;
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() 
            || !Configuration::deleteByName('GLOBAL_COMMISION')
            || !Configuration::deleteByName('AUTO_ENABLE_SELLER_ACCOUNT')
            || !Configuration::deleteByName('EMAIL_ADDRESS')
            || !$this->_deleteTab())
            return false;
        return true;
    }

    public function hookDisplayCustomerAccount( $params )
    {
        $id_customer = $this->context->customer->id;
        $query = new DbQuery();
        $query
            -> select('*')
            -> from('seller')
            -> where('`id_customer` = '.$id_customer);
        $account_state = 'none';
        if ($result = Db::getInstance() -> executeS($query))
        {
            $active = $result[0]['active'];
            $locked = $result[0]['locked'];
            $requested = $result[0]['requested'];
            if ($requested == 1 && $active == 0 && $locked == 0)
                $account_state = 'requested';
            else if ($requested == 1 && $active == 1 && $locked == 0)
                $account_state = 'active';
            else if ($requested == 1 && $locked == 1)
                $account_state = 'locked';
        }
        
        $this->context->smarty->assign(
            array(
                'account_state' => $account_state,
                'seller_request_link' => $this->context->link->getModuleLink('npsmarketplace', 'AccountRequest'),
                'add_product_link' => $this->context->link->getModuleLink('npsmarketplace', 'AddProduct'),
                'products_list_link' => $this->context->link->getModuleLink('npsmarketplace', 'ProductsList'),
                'orders_link' => $this->context->link->getModuleLink('npsmarketplace', 'Orders'),
                'payment_settings_link' => $this->context->link->getModuleLink('npsmarketplace', 'PaymentSettings')
            )
        );
        return $this->display(__FILE__, 'npsmarketplace.tpl');
    }

    public function getContent()
    {
        $output = null;
    
        if (Tools::isSubmit('submit'.$this->name))
        {
            $global_commision = Tools::getValue('GLOBAL_COMMISION');
            $auto_enable_seller_accont = Tools::getValue('AUTO_ENABLE_SELLER_ACCOUNT');
            $email = Tools::getValue('EMAIL_ADDRESS');

            if (!$global_commision || !Validate::isInt($global_commision))
            {
                $output .= $this->displayError($this->l('Invalid global commision value. Must be a number'));
            }
            else if ($auto_enable_seller_accont != 0 && $auto_enable_seller_accont != 1)
            {
                $output .= $this->displayError($this->l('Invalid auto enable seller account settings'));
            }
            else if (!$email || !Validate::isEmail($email))
            {
                $output .= $this->displayError($this->l('Invalid email address'));
            }
            else
            {
                Configuration::updateValue('GLOBAL_COMMISION', $global_commision);
                Configuration::updateValue('AUTO_ENABLE_SELLER_ACCOUNT', $auto_enable_seller_accont);
                Configuration::updateValue('EMAIL_ADDRESS', $email);
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    return $output.$this->displayForm();
	}

	private function displayForm()
	{
	    // Get default language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	             array(
	                'type' => 'text',
	                'label' => $this->l('Global commision(%)'),
	                'name' => 'GLOBAL_COMMISION',
	                'size' => 20,
					'required' => true
	            ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Auto enable seller account'),
                    'name' => 'AUTO_ENABLE_SELLER_ACCOUNT',
                    'is_bool' => true,
                    'desc' => $this->l('For all registered users seller account will be enabled by default'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email address'),
                    'name' => 'EMAIL_ADDRESS',
                    'desc' => $this->l('Seller requests account will be sent to this e-mail address.'),
                    'size' => 20,
                    'required' => true
                )
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	     
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

	public function getConfigFieldsValues()
	{
		return array(
			'AUTO_ENABLE_SELLER_ACCOUNT' => Tools::getValue('AUTO_ENABLE_SELLER_ACCOUNT', Configuration::get('AUTO_ENABLE_SELLER_ACCOUNT')),
			'GLOBAL_COMMISION' => Tools::getValue('GLOBAL_COMMISION', Configuration::get('GLOBAL_COMMISION')),
			'EMAIL_ADDRESS' => Tools::getValue('EMAIL_ADDRESS', Configuration::get('EMAIL_ADDRESS')),
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
            $tab->{'name'}[intval($language['id_lang'])] = 'Sellers';
        $success = $tab->add();
        
        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 0;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminSellersAccounts';
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = 'Sellers accounts';
        }
        $success = $success && $sellers_tab->add();
        
        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tab->id;
        $sellers_tab->position = 1;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminSellersProducts';
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = 'Sellers products';
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
    private function _createTables()
    {
        $sellerTable = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seller` (
                `id_seller` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_customer` int(10) unsigned NOT NULL,
                `active` tinyint(1) NOT NULL,
                `locked` tinyint(1) NOT NULL,
                `requested` tinyint(1) NOT NULL,
                `request_date` datetime,
                `phone` varchar(16) NOT NULL,
                `email` varchar(128) NOT NULL,
                `commision` int(10),
                `nip` int(14) NOT NULL,
                `regon` int(14) NOT NULL,
                PRIMARY KEY (`id_seller`),
                KEY `id_customer` (`id_customer`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sellerLangTable = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seller_lang` (
            `id_seller` int(10) unsigned NOT NULL,
            `company_name` varchar(64) NOT NULL,
            `company_description` text,
            `name` varchar(128) NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            KEY `id_seller` (`id_seller`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sellerProductTable = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seller_product` (
            `id_seller` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            KEY `id_seller` (`id_seller`),
            KEY `id_product` (`id_product`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $instance = Db::getInstance();
        if ($instance->Execute($sellerTable) && $instance->Execute($sellerLangTable) && $instance->Execute($sellerProductTable))
            return true;
        else
            return false;
    }
}
?>
