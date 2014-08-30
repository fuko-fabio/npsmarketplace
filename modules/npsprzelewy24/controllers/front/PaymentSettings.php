<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/iban/php-iban.php');

class NpsPrzelewy24PaymentSettingsModuleFrontController extends ModuleFrontController {

    public function setMedia() {
        parent::setMedia();
        $this -> addJS(_PS_JS_DIR_.'validate.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsprzelewy24/js/iban.js');
    }

    public function postProcess() {
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('person')
            && Tools::isSubmit('street')
            && Tools::isSubmit('post_code')
            && Tools::isSubmit('city')
            && Tools::isSubmit('nip')
            && Tools::isSubmit('regon')
            && Tools::isSubmit('iban')
            && Tools::isSubmit('email')) {

            $nps_instance = new NpsPrzelewy24();
            $seller = new Seller(null, $this->context->customer->id);

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
                d($company);
                $p24_id = Configuration::get('NPS_P24_MERCHANT_ID');
                $p24_key = Configuration::get('NPS_P24_UNIQUE_KEY');
                $url = Configuration::get('NPS_P24_WEB_SERVICE_URL');
                if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
                    $url = Configuration::get('NPS_P24_SANDBOX_WEB_SERVICE_URL');
                }
                $res = $this->registerCompany($url, $p24_id, $p24_key, $company);
                if ($res->error->errorCode) {
                    d('Something went wrong: ' . $res->error->errorMessage);
                } else {
                    $this->persistPaymentSettings($res->result, $seller);
                    d("Company created: " . $res->result->spid . ", click to finish: ".$res->result->link);
                }
            }
        }
    }

    public function initContent() {
        parent::initContent();
        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password')
            Tools::redirect('index.php?controller=authentication?back=my-account');
        $seller = new Seller(null, $this->context->customer->id);
        if (!$seller->active)
            Tools::redirect('index.php?controller=my-account');
        if ($seller->locked)
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'));

        $p24_id = Configuration::get('NPS_P24_MERCHANT_ID');
        $p24_key = Configuration::get('NPS_P24_UNIQUE_KEY');
        $url = Configuration::get('NPS_P24_WEB_SERVICE_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_WEB_SERVICE_URL');
        }
        //$res = array('error'=> array('errorCode' => 0), 'result' => 0);//$this->checkCompanyNip($url, $p24_id, $p24_key, $seller);
        //if ($res->error->errorCode) {
        //    d('Something went wrong: ' . $res->error->errorMessage);
        //    $this->setTemplate('payment_settings_error.tpl');
        //} else {
            //if ($res->result) {
            //    $this->setTemplate('company_registered.tpl');
            //} else {
                //$res = $this->registerCompany($url, $p24_id, $p24_key, $seller, $this->context->customer);
                //if ($res->error->errorCode) {
                //    d('Something went wrong: ' . $res->error->errorMessage);
                //} else {
                //    d("Company created: " . $res->result->spid . ", click to finish: ".$res->result->link);
                //}
                $this->context->smarty->assign(array(
                    'company' => $this->getRegisterCompanyData(),
                    'p24_agreement_url' => Configuration::get('NPS_P24_REGULATIONS_URL')
                ));
                $this->setTemplate('payent_register_company.tpl');
        //    }
        //}
    }

    private function persistPaymentSettings($result, $seller){
        $settings = new P24SellerSettings();
        $settings->active = true;
        $settings->id_seller = $seller->id;
        $settings->register_link = $result->link;
        $settings->registration_date = date("Y-m-d H:i:s");;
        $settings->spid = $result->spid;
        $settings->save();
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

    private function checkCompanyNip($url, $p24_id, $p24_key, $seller) {
        $soap = new SoapClient($url);
        return $soap->CheckNIP($p24_id, $p24_key, $seller->nip);
    }

    private function registerCompany($url, $p24_id, $p24_key, $company) {
        $soap = new SoapClient($url);
        return $soap->CompanyRegister($p24_id, $p24_key, $company);
    }
}
?>