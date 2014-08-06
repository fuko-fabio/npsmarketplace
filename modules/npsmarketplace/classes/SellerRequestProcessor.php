<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
 
class SellerRequestProcessor {

    public $errors = array();

    public $context;

    public function __construct(Context $context = null) {
        $this -> context = $context;
    }

    public function processSubmit() {
        $company_name = $_POST['company_name'];
        $name = $_POST['seller_name'];
        $phone = trim(Tools::getValue('seller_phone'));
        $email = trim(Tools::getValue('seller_email'));
        $nip = Tools::getValue('seller_nip');
        $regon = Tools::getValue('seller_regon');
        $company_description = $_POST['company_description'];
        $companyLogo = trim(Tools::getValue('company_logo'));
        $regulations_active = Tools::getIsset('regulations_active');
        $regulations = Tools::getValue('regulations');
        $link_rewrite = array();

        if (!Validate::isPhoneNumber($phone))
            $this -> errors[] = Tools::displayError('Invalid phone number');
        else if (!Validate::isEmail($email))
            $this -> errors[] = Tools::displayError('Invalid email addres');
        else if (!empty($nip) && !Validate::isNip($nip))
            $this -> errors[] = Tools::displayError('Invalid NIP number');
        else if (!empty($regon) && !Validate::isRegon($regon))
            $this -> errors[] = Tools::displayError('Invalid REGON number');
        foreach (Language::getLanguages() as $key => $lang) {
            $n = $name[$lang['id_lang']];
            if (!Validate::isGenericName($n))
                $this -> errors[] = Tools::displayError('Invalid '.$lang->name.' seller name');
            else if (!Validate::isGenericName($company_name[$lang['id_lang']]))
                $this -> errors[] = Tools::displayError('Invalid '.$lang->name.' company name');
            else if (!Validate::isCleanHtml($company_description[$lang['id_lang']]))
                $this -> errors[] = Tools::displayError('Invalid '.$lang->name.' company description');
            else if (!Validate::isCleanHtml($regulations[$lang['id_lang']]))
                $this -> errors[] = Tools::displayError('Invalid '.$lang->name.' regulations content');

            $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($n);
        }

        $seller = new Seller((int)Tools::getValue('id_seller', null));
        $seller -> company_name = $company_name;
        $seller -> company_description = $company_description;
        $seller -> name = $name;
        $seller -> phone = $phone;
        $seller -> email = $email;
        $seller -> nip = $nip;
        $seller -> regon = $regon;
        $seller -> link_rewrite = $link_rewrite;
        $seller -> regulations = $regulations;
        $seller -> regulations_active = $regulations_active;
        if($seller->id == null) {
            $seller -> id_customer = $this -> context -> customer -> id;
            $seller -> commision = Configuration::get('NPS_GLOBAL_COMMISION');
            $seller -> request_date = date("Y-m-d H:i:s");
            $seller -> requested = true;
        }
        if(empty($this->errors))
            $seller->save();
        return $seller;
    }

}
?>