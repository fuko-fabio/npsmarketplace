<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

class NpsCalendar extends Module {

    public function __construct() {
        $this->name = 'npscalendar';
        $this->tab = 'search_filter';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Home page events calendar' );
        $this->description = $this->l('Adds calendar with current events on home page');
    }

    public function install() {
        return parent::install()
            && $this->registerHook('displayTopColumn')
            && $this->registerHook('header');
    }

    public function uninstall() {
        return parent::uninstall()
            && $this->unregisterHook('displayTopColumn')
            && $this->unregisterHook('header');
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit')) {
            Configuration::updateValue('NPS_EVENTS_PER_DAY', (int)Tools::getValue('NPS_EVENTS_PER_DAY'));
            Configuration::updateValue('NPS_EVENTS_SEARCH', (int)Tools::getValue('NPS_EVENTS_SEARCH'));
            $output .= $this->displayConfirmation($this->l('Settings updated successfully'));
        }
        return $output.$this->displayForm();
    }

    public function hookDisplayTopColumn() {
        return $this->display(__FILE__, 'calendar.tpl');
    }

    public function hookHeader() {
        $this->page_name = Dispatcher::getInstance()->getController();
        if ($this->page_name == 'index') {
            $this->context->controller->addCss(($this->_path).'css/npscalendar.css');
            $this->context->controller->addJS(array(
                ($this->_path).'js/underscore-min.js',
                ($this->_path).'js/backbone-min.js',
                ($this->_path).'js/backbone-associations-min.js',
                ($this->_path).'js/calendar/template/calendar.js',
                ($this->_path).'js/calendar/model/event.js',
                ($this->_path).'js/calendar/collection/events.js',
                ($this->_path).'js/calendar/model/day.js',
                ($this->_path).'js/calendar/collection/days.js',
                ($this->_path).'js/calendar/model/calendar.js',
                ($this->_path).'js/calendar/view/calendar.js',
                ($this->_path).'js/calendar/router.js',
            ));
        }
    }

    private function displayForm() {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form[0] = $this->configForm();
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit';
        $helper->fields_value = $this->getConfigFieldsValues();
        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues() {
        return array(
            'NPS_EVENTS_PER_DAY' => Tools::getValue('NPS_EVENTS_PER_DAY', Configuration::get('NPS_EVENTS_PER_DAY')),
            'NPS_EVENTS_SEARCH' => Tools::getValue('NPS_EVENTS_SEARCH', Configuration::get('NPS_EVENTS_SEARCH')),
        );
    }

    private function configForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Events Calendar Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Events number per day'),
                        'name' => 'NPS_EVENTS_PER_DAY',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Max search events'),
                        'name' => 'NPS_EVENTS_SEARCH',
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
}
