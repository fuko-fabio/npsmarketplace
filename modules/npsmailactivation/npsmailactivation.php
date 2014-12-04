<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

class NpsMailActivation extends Module {

    public function __construct() {
        $this->name = 'npsmailactivation';
        $this->tab = 'administration';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Account activation by e-mail' );
        $this->description = $this->l('This module allows your shop to validate e-mails by sending activation links');
    }

    public function install() {
        return parent::install() &&
            $this->registerHook('createAccount') &&
            Db::getInstance()->Execute('alter table ' . _DB_PREFIX_ . 'customer add activation_token char(32)');
    }

    public function uninstall() {
        return parent::uninstall() &&
            $this->unregisterHook('createAccount') &&
            Db::getInstance()->Execute('alter table ' . _DB_PREFIX_ . 'customer drop activation_token');
    }

    public function hookCreateAccount() {
        global $cookie;
        $customer = new Customer($this->context->customer->id);
        $id_lang = $this->context->language->id;

        if (Tools::getValue('submitGuestAccount'))
            $_GET['display_guest_checkout'] = 1;		

        if (Tools::getValue('display_guest_checkout')) {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
                $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
            else
                $countries = Country::getCountries($this->context->language->id, true);

            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                // get all countries as language (xy) or language-country (wz-XY)
                $array = array();
                preg_match("#(?<=-)\w\w|\w\w(?!-)#",$_SERVER['HTTP_ACCEPT_LANGUAGE'],$array);
                if (!Validate::isLanguageIsoCode($array[0]) || !($sl_country = Country::getByIso($array[0])))
                    $sl_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            }
            else
                $sl_country = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));

            $this->context->smarty->assign(array(
                'inOrderProcess' => true,
                'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                'sl_country' => (int)$sl_country,
                'countries' => $countries
            ));
        }
        else {
            $cookie->logout();
            $cookie->id_lang = $id_lang;
            $cookie->write();
            $token = md5(uniqid(rand(), true));
            $glu = Configuration::get('PS_REWRITING_SETTINGS') ? '?' : '&';
            $link = $this->context->link->getModuleLink($this->name, 'activation').$glu.'token=' . $token;

            Db::getInstance()->update(
                'customer',
                array('active' => 0, 'activation_token' => $token),
                'id_customer = '.$customer->id);

            $data = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => Tools::getValue('passwd'),
                '{link}' => $link
            );

            Mail::Send($id_lang,
                'account_activation',
                $this->l('Welcome!'),
                $data,
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                NULL,
                NULL,
                NULL,
                NULL,
                'modules/npsmailactivation/mails/');
            Tools::redirect($this->context->link->getModuleLink($this->name, 'info'));
        }
    }

    private function isMD5($str) {
        for ($i = 0; $i < 32; $i++)
            if (!(($str[$i] >= 'a' && $str[$i] <= 'z') || ($str[$i] >= '0' && $str[$i] <= '9')))
                return false;
        return true;
    }

    public function execActivation() {
        $token = Tools::getValue('token');
        return $this->isMD5($token) ? $this->activateAccountForValidLink($token) : false;
    }

    private function activateAccountForValidLink($token) {
        $res = Db::getInstance()->update(
            'customer',
            array('active' => 1),
            'activation_token = \''.$token.'\'');
        $res =  $res ? Db::getInstance()->getValue('SELECT `active` FROM `'._DB_PREFIX_.'customer` WHERE `activation_token` = \''.$token.'\'') : false;
        if ($res) {
            $id_user = Db::getInstance()->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'customer` WHERE `activation_token` = \''.$token.'\'');
            $customer = new Customer($id_user);

            $data = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{user_guide_url}' => Configuration::get('NPS_USER_GUIDE_URL'),
                '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
            );
            Mail::Send($this->context->language->id,
                'account',
                $this->l('Welcome!'),
                $data,
                $customer->email,
                $customer->firstname.' '.$customer->lastname
            );
        }
        return $res;
    }
}
