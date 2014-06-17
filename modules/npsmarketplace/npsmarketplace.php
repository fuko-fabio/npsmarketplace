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
          ->select('`state`')
          ->from('seller')
          ->where('`id_customer` = '.$id_customer)
          ;
        $account_state = 0;
        if ($result = Db::getInstance()->getValue($query))
        {
            $account_state = $result;
        }
        $this->context->smarty->assign(
            array(
                'account_state' => $account_state,
                'seller_request_link' => $this->context->link->getModuleLink('npsmarketplace', 'accountrequest'),
                'add_product_link' => $this->context->link->getModuleLink('npsmarketplace', 'addproduct'),
                'products_list_link' => $this->context->link->getModuleLink('npsmarketplace', 'productslist'),
                'orders_link' => $this->context->link->getModuleLink('npsmarketplace', 'orders'),
                'payment_settings_link' => $this->context->link->getModuleLink('npsmarketplace', 'paymentsettings')
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
        /* define data array for the tab  */
        $data = array(
                      'id_tab' => '', 
                      'id_parent' => 11, 
                      'class_name' => 'AdminSellersAccounts', 
                      'module' => 'npsmarketplace', 
                      'position' => 1, 'active' => 1 
                     );

        /* Insert the data to the tab table*/
        $res = Db::getInstance()->insert('tab', $data);

        //Get last insert id from db which will be the new tab id
        $id_tab = Db::getInstance()->Insert_ID();
    
       //Define tab multi language data
        $data_lang = array(
                         'id_tab' => $id_tab, 
                         'id_lang' => Configuration::get('PS_LANG_DEFAULT'),
                         'name' => 'Sellers'
                         );
    
        // Now insert the tab lang data
        $res &= Db::getInstance()->insert('tab_lang', $data_lang);
    
        return true;
    }

    private function _deleteTab()
    {
        Db::getInstance()->delete('tab', 'module = `npsmarketplace`');
        return true;
    }

    private function _createTables()
    {
        /* Set database */
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'seller` (
                `id_seller` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_customer` int(10) unsigned NOT NULL,
                `state` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `request_date` datetime,
                `company_name` varchar(64) NOT NULL,
                `company_logo` varchar(64),
                `company_description` text,
                `phone` int(14) NOT NULL,
                `email` varchar(128) NOT NULL,
                `name` varchar(128) NOT NULL,
                `commision` int(10),
                PRIMARY KEY (`id_seller`),
                KEY `id_customer` (`id_customer`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        if (!Db::getInstance()->Execute($sql))
            return false;
        return true;
    }
}
?>
