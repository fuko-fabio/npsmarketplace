<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/iban/php-iban.php');

class NpsPrzelewy24PaymentSettingsModuleFrontController extends ModuleFrontController {

    public function postProcess() {
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('city')
            && Tools::isSubmit('street')
            && Tools::isSubmit('post_code')
            && Tools::isSubmit('email')
            && Tools::isSubmit('nip')
            && Tools::isSubmit('person')
            && Tools::isSubmit('regon')
            && Tools::isSubmit('iban')) {

            $nps_instance = new NpsMarketplace();

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

            if(!verify_iban($iban_to_verify))
                $this -> errors[] = $nps_instance->l('Invalid IBAN number');

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
                $this->registerCompany($company);
            }
        }
    }

    public function initContent() {
        parent::initContent();
        $p24_id = Configuration::get('NPS_P24_MERCHANT_ID');
        $p24_key = Configuration::get('NPS_P24_UNIQUE_KEY');
        $url = Configuration::get('NPS_P24_WEB_SERVICE_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_WEB_SERVICE_URL');
        }
        $seller = new Seller(null, $this->context->customer->id);
        $res = $this->checkCompanyNip($url, $p24_id, $p24_key, $seller);
        if ($res->error->errorCode) {
            d('Something went wrong: ' . $res->error->errorMessage);
            $this->setTemplate('payment_settings_error.tpl');
        } else {
            if ($res->result) {
                $this->setTemplate('company_registered.tpl');
            } else {
                $res = $this->registerCompany($url, $p24_id, $p24_key, $seller, $this->context->customer);
                if ($res->error->errorCode) {
                    d('Something went wrong: ' . $res->error->errorMessage);
                } else {
                    d("Company created: " . $res->result->spid . ", click to finish: ".$res->result->link);
                }
                $this->getRegisterCompanyFormData();
                $this->setTemplate('payent_settings.tpl');
            }
        }
    }

    private function getRegisterCompanyFormData() {
        $city = '';
        $postcode = '';
        $address = '';
        $addresses = $customer->getAddresses();
        if (!empty($addresses)) {
            $city = $addresses[0]['city'];
            $postcode = $addresses[0]['postcode'];
            $address = $addresses[0]['address1'].' '.$addresses[0]['address2'];
        }
    }

    private function checkCompanyNip($url, $p24_id, $p24_key, $seller) {
        $soap = new SoapClient($url);
        return $soap->CheckNIP($p24_id, $p24_key, $nip);
    }

    private function registerCompany($url, $p24_id, $p24_key, $company) {
        $soap = new SoapClient($url);
        return $soap->CompanyRegister($p24_id, $p24_key, $company);
    }
}
?>