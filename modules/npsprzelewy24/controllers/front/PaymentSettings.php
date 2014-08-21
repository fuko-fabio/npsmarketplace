<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/iban/php-iban.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerSettings.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');

class NpsPrzelewy24PaymentSettingsModuleFrontController extends ModuleFrontController {

    public function setMedia() {
        parent::setMedia();
        $this -> addJS(_PS_JS_DIR_.'validate.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsprzelewy24/js/iban.js');
    }

    public function postProcess() {
        if (Tools::isSubmit('submitCompany')) {

            $nps_instance = new NpsPrzelewy24();
            $seller = new Seller(null, $this->context->customer->id);
            $settings = new P24SellerSettings(null, $seller->id);
            if ($settings->id != null && !empty($settings->spid)) {
                Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentSettings'));
                return;
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

            if (empty($regon))
                $this -> errors[] = $nps_instance->l('REGON number is required');
            else if (empty($regon) && !Validate::isRegon($regon))
                $this -> errors[] = $nps_instance->l('Invalid REGON number');

            if(!verify_iban($iban))
                $this -> errors[] = $nps_instance->l('Invalid IBAN number');

            if (!$acceptance)
                $this -> errors[] = $nps_instance->l('Acceptance of the Przelewy24 regulations is required');

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
                    $this->persistPaymentSettings($res->result, $seller);
                    Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'paymentSettings'));
                }
            }
        } else if (Tools::isSubmit('submitSpid')) {
            $nps_instance = new NpsPrzelewy24();
            $spid = trim(Tools::getValue('spid'));
            if (empty($spid))
                $this -> errors[] = $nps_instance->l('Seller Przelewy24 ID is required');
            else if (!Validate::isUnsignedInt($spid))
                $this -> errors[] = $nps_instance->l('Invalid seller Przelewy24 ID');

            if(empty($this->errors)) {
                $seller = new Seller(null, $this->context->customer->id);
                $this->persistPaymentSettings(array('spid' => $spid, 'link' => ''), $seller);
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
            return;
        } else if ($seller->locked) {
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'));
            return;
        }
        $settings = new P24SellerSettings(null, $seller->id);
        if ($settings->id != null && !empty($settings->spid)) {
            $this->context->smarty->assign(array(
                    'date' => $settings->registration_date,
                    'spid' => $settings->spid,
                    'link' => $settings->register_link,
                ));
            $this->setTemplate('payent_company_registered.tpl');
            return;
        }
        $nps_instance = new NpsPrzelewy24();
        $res = P24::checkNIP($seller->nip);
        if ($res->error->errorCode) {
            $this -> errors[] = $nps_instance->l('Unable to check company existence in Przelewy24 payment service.')
                    .' '.P24ErrorMessage::get($res->error->errorCode).' '.$nps_instance->l('Please contact with customer service');
        } else if ($res->result) {
            $this->setTemplate('payment_company_registered.tpl');
        } else {
            $this->context->smarty->assign(array(
                'company' => $this->getRegisterCompanyData(),
                'p24_agreement_url' => Configuration::get('NPS_P24_REGULATIONS_URL')
            ));
            $this->setTemplate('payent_register_company.tpl');
        }
    }

    private function persistPaymentSettings($result, $seller){
        $s = new P24SellerSettings(null, $seller->id);
        if ($s->id == null) {
            $s->active = true;
            $s->id_seller = $seller->id;
            $s->registration_date = date("Y-m-d H:i:s");
            $s->register_link = $result['link'];
        }
        $s->spid = $result['spid'];
        $s->save();
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