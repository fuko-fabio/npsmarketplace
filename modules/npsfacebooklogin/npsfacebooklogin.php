<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

class NpsFacebookLogin extends Module {

    public function __construct() {
        $this->name = 'npsfacebooklogin';
        $this->tab = 'search_filter';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Log in with facebook' );
        $this->description = $this->l('Displays log in with facebook button on authentication page');
    }

    public function install() {
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayLoginSource');
    }

    public function uninstall() {
        return parent::uninstall()
            && $this->unregisterHook('header')
            && $this->unregisterHook('displayLoginSource');
    }

    public function hookHeader() {
        $this->context->controller->addCss(($this->_path).'npsfacebooklogin.css');
        $this->context->smarty->assign(array(
            'nps_lang_code' => $this->context->language->language_code,
            'nps_fb_app_id' => Configuration::get('NPS_FB_APP_ID'),
            'nps_fb_controller' => $this->context->link->getModuleLink('npsfacebooklogin', 'Auth', array('facebookAuth' => true, 'ajax' => true))
        ));
        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    public function hookDisplayLoginSource() {
        $this->context->controller->addJS(($this->_path).'js/npsfacebooklogin.js');
        return $this->display(__FILE__, 'views/templates/hook/authentication.tpl');
    }

    private function configurationForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Log in with facebook settings'),
                ),
                'input' => array(
                     array(
                        'type' => 'text',
                        'label' => $this->l('Facebook app ID'),
                        'name' => 'NPS_FB_APP_ID',
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submit',
                )
            )
        );
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit')) {
            Configuration::updateValue('NPS_FB_APP_ID', Tools::getValue('NPS_FB_APP_ID'));
            $output .= $this->displayConfirmation($this->l('Settings updated successfuly'));
        }
        return $output.$this->displayForm();
    }

    public function getConfigFieldsValues() {
        return array(
            'NPS_FB_APP_ID' => Tools::getValue('NPS_FB_APP_ID', Configuration::get('NPS_FB_APP_ID')),
        );
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
        $helper->submit_action = 'submit';
        $helper->toolbar_btn = array(
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value = $this->getConfigFieldsValues();
        return $helper->generateForm($fields_form);
    }
}