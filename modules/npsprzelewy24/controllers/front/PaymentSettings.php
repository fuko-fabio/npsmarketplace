<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/iban/php-iban.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24ErrorMessage.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsPrzelewy24PaymentSettingsModuleFrontController extends ModuleFrontController {

    public function setMedia() {
        parent::setMedia();
        $this -> addJS (_PS_MODULE_DIR_.'npsprzelewy24/js/iban.js');
        $this -> addJS(_PS_JS_DIR_.'validate.js');
    }

    public function postProcess() {
        if (Tools::isSubmit('submitCompany')) {

            $nps_instance = new NpsPrzelewy24();
            $seller = new Seller(null, $this->context->customer->id);
            $settings = new P24SellerCompany(null, $seller->id);
            if ($settings->id != null && !empty($settings->spid)) {
                Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentSettings'));
            }

            $company_name = trim(Tools::getValue('company_name'));
            $city = trim(Tools::getValue('city'));
            $street = trim(Tools::getValue('street'));
            $email = trim(Tools::getValue('email'));
            $post_code = trim(Tools::getValue('post_code'));
            $nip = trim(Tools::getValue('nip'));
            $person = trim(Tools::getValue('person'));
            $regon = trim(Tools::getValue('regon'));
            $acceptance = Tools::getIsset('acceptance');
            $iban = trim(Tools::getValue('iban'));

            if (empty($company_name))
                $this -> errors[] = $nps_instance->l('Company name is required');
            else if (!Validate::isGenericName($company_name))
                $this -> errors[] = $nps_instance->l('Invalid company name');

            if (empty($city))
                $this -> errors[] = $nps_instance->l('City name is required');
            else if (!Validate::isCityName($city))
                $this -> errors[] = $nps_instance->l('Invalid city name');

            if (empty($street))
                $this -> errors[] = $nps_instance->l('Address is required');
            else if (!Validate::isAddress($street))
                $this -> errors[] = $nps_instance->l('Invalid address');

            if (empty($post_code))
                $this -> errors[] = $nps_instance->l('Post code is required');
            else if (!Validate::isPostCode($post_code))
                $this -> errors[] = $nps_instance->l('Invalid post code');

            if (empty($person))
                $this -> errors[] = $nps_instance->l('Person name is required');
            else if (!Validate::isName($person))
                $this -> errors[] = $nps_instance->l('Invalid person name');

            if (empty($email))
                $this -> errors[] = $nps_instance->l('Buisness email is required');
            else if (!Validate::isEmail($email))
                $this -> errors[] = $nps_instance->l('Invalid email addres');

            if (empty($nip))
                $this -> errors[] = $nps_instance->l('NIP number is required');
            else if (empty($nip) && !Validate::isNip($nip))
                $this -> errors[] = $nps_instance->l('Invalid NIP number');

            if (!empty($regon) && !Validate::isRegon($regon))
                $this -> errors[] = $nps_instance->l('Invalid REGON number');

            if ($acceptance)
                if(empty($iban) || !verify_iban($iban))
                    $this -> errors[] = $nps_instance->l('Invalid IBAN number');

            #if (!$acceptance)
            #    $this -> errors[] = $nps_instance->l('Acceptance of the Przelewy24 regulations is required');

            if(empty($this->errors)) {
                $company = array(
                    "companyName" => $company_name,
                    "city" => $city,
                    "street" => $street,
                    "postCode" => $post_code,
                    "email" => $email,
                    "nip" => $nip,
                    "person" => $person,
                    "regon" => $regon,
                    "IBAN" => $iban,
                    "acceptance" => $acceptance,
                );

                $res = P24::companyRegister($company);
                if ($res->error->errorCode) {
                    $this -> errors[] = $nps_instance->l('Unable register company in Przelewy24 payment service.')
                        .' '.P24ErrorMessage::get($res->error->errorCode).' '.$nps_instance->l('Please contact with customer service');
                } else {
                    $settings->id_seller = $seller->id;
                    $settings->registration_date = date("Y-m-d H:i:s");
                    $settings->register_link = $res->result->link;
                    $settings->spid = $res->result->spid;
                    $settings->company_name = $company_name;
                    $settings->city = $city;
                    $settings->street = $street;
                    $settings->post_code = $post_code;
                    $settings->email= $email;
                    $settings->nip = $nip;
                    $settings->person = $person;
                    $settings->regon = $regon;
                    $settings->iban = $iban;
                    $settings->acceptance = $acceptance;
                    $settings->save();
                    Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'PaymentSettings', array('register_link' => $res->result->link)));
                }
            }
        }
    }

    public function initContent() {
        parent::initContent();
        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password')
            Tools::redirect('index.php?controller=authentication?back=my-account');
        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null || !$seller->active) {
            Tools::redirect('index.php?controller=my-account');
        } else if ($seller->locked) {
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'));
        }
        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn')
        ));
        $settings = new P24SellerCompany(null, $seller->id);
        if ($settings->id != null && !empty($settings->spid)) {
            $this->context->smarty->assign(array(
                'register_link' => Tools::getValue('register_link'),
                'company' => $settings,
                'p24_agreement_url' => Configuration::get('NPS_P24_REGULATIONS_URL')
            ));
            $this->setTemplate('payment_company_registered.tpl');
        } else {
            $nps_instance = new NpsPrzelewy24();
    
            if (!empty($seller->nip)) {
                $res = P24::checkNIP($seller->nip);
            } else {
                $res = (object) array('error' => (object) array('errorCode' => 0), 'result' => 0);
            }
            if ($res->error->errorCode) {
                $this->errors[] = $nps_instance->l('Unable to check company existence in Przelewy24 payment service.')
                        .' '.P24ErrorMessage::get($res->error->errorCode).' '.$nps_instance->l('Please contact with customer service');
                $this->context->smarty->assign(array(
                    'company' => array(),
                    'p24_agreement_url' => Configuration::get('NPS_P24_REGULATIONS_URL'))
                );
                $this->setTemplate('payment_company_registered.tpl');
            } else if ($res->result) {
                $this->errors[] = sprintf($nps_instance->l('Your company with NIP "%s" has been already registered in Przelewy24 service. Please contact with customer service.'), $seller->nip);
                $this->context->smarty->assign(array('company' => array()));
                $this->setTemplate('payment_company_registered.tpl');
            } else {
                $this->context->smarty->assign(array(
                    'company' => $this->getRegisterCompanyData(),
                    'p24_agreement_url' => Configuration::get('NPS_P24_REGULATIONS_URL')
                ));
                $this->setTemplate('payment_register_company.tpl');
            }
        }
    }

    private function getRegisterCompanyData() {
        $city = '';
        $post_code = '';
        $address = '';
        $cust = $this->context->customer;
        $seller = new Seller(null, $cust->id);
        $addresses = $cust->getAddresses($this->context->language->id);
        if (!empty($addresses)) {
            $city = $addresses[0]['city'];
            $post_code = $addresses[0]['postcode'];
            $address = $addresses[0]['address1'].' '.$addresses[0]['address2'];
        }
        return array(
            "company_name" => $seller->company_name,
            "city" => $city,
            "street" => $address,
            "post_code" => $post_code,
            "email" => $seller->email,
            "nip" => $seller->nip,
            "person" => $cust->firstname.' '.$cust->lastname,
            "regon" => $seller->regon,
            "iban" => '',
            "acceptance" => false,
        );
    }
}
?>