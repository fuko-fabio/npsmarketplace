<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerHelper.php');

class NpsMarketplaceAccountRequestModuleFrontController extends ModuleFrontController {

    const __MA_MAIL_DELIMITOR__ = ',';
    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addJS(array(
            _THEME_JS_DIR_.'tools/vatManagement.js',
            _THEME_JS_DIR_.'tools/statesManagement.js',
            _PS_JS_DIR_.'validate.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/tinymce/tinymce.min.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/tinymce_init.js'
        ));
    }

    public function postProcess() {
        if (Tools::isSubmit('submitSeller')) {

            $seller = new Seller(null, $this->context->customer->id);
            if ($seller->id != null) 
                Tools::redirect('index.php?controller=my-account');

            $companyLogo = trim(Tools::getValue('company_logo'));
            $name = trim(Tools::getValue('seller_name'));
            $description = $_POST['company_description'];
            $regulations = Tools::getValue('regulations');
            
            $nip = Tools::getValue('seller_nip');
            $regon = Tools::getValue('seller_regon');
            $krs = Tools::getValue('seller_krs');
            $krs_reg = Tools::getValue('seller_krs_reg');

            $link_rewrite = array();

            if (empty($name))
                $this -> errors[] = $this->module->l('Seller name is required', 'AccountRequest');
            else if (!Validate::isGenericName($name))
                $this -> errors[] = $this->module->l('Invalid seller name', 'AccountRequest');
            else if (Seller::sellerExists($name))
                $this -> errors[] = $this->module->l('Seller name is not unique', 'AccountRequest');

            if (!empty($nip) && !Validate::isNip($nip))
                $this -> errors[] = $this->module->l('Invalid NIP number', 'AccountRequest');

            if (!empty($krs) && !Validate::isKrs($krs))
                $this -> errors[] = $this->module->l('Invalid KRS number', 'AccountRequest');

            if (!empty($krs_reg) && !Validate::isCleanHtml($krs_reg))
                $this -> errors[] = $this->module->l('Invalid KRS registration authority content', 'AccountRequest');

            if (!empty($regon) && !Validate::isRegon($regon))
                $this -> errors[] = $this->module->l('Invalid REGON number', 'AccountRequest');

            foreach (Language::getLanguages() as $key => $lang) {
                if (!Validate::isCleanHtml($description[$lang['id_lang']]))
                    $this -> errors[] = $this->module->l('Invalid company description', 'AccountRequest');
                if (!Validate::isCleanHtml($regulations[$lang['id_lang']]))
                    $this -> errors[] = $this->module->l('Invalid regulations content', 'AccountRequest');

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
            }

            if(empty($this->errors)) {
                $id_address = $this->processSubmitAddress($seller);
                if($id_address && empty($this->errors)) {
                    $seller->description = $description;
                    $seller->name = $name;
                    $seller->krs = $krs;
                    $seller->krs_reg = $krs_reg;
                    $seller->nip = $nip;
                    $seller->regon = $regon;
                    $seller->link_rewrite = $link_rewrite;
                    $seller->regulations = $regulations;
                    $seller->id_customer = $this->context->customer->id;
                    $seller->commision = Configuration::get('NPS_GLOBAL_COMMISION');
                    $seller->request_date = date("Y-m-d H:i:s");
                    $seller->requested = true;
                    $seller->id_address = $id_address;
                    $seller->save();
                    $this->postImage($seller);
                    $this->mailToSeller($seller);
                    $this->mailToAdmin($seller);
                    Tools::redirect('index.php?controller=my-account' );
                }
            }
        }
    }

    protected function processSubmitAddress($seller) {
        $address = new Address();
        if (isset($_POST['company']) && !empty($_POST['company']))
            $_POST['alias'] = $_POST['company'];
        else
            $_POST['alias'] = $this->module->l('Company address');

        $this->errors = $address->validateController();
        $address->id_customer = (int)$this->context->customer->id;

        // Check phone
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !Tools::getValue('phone') && !Tools::getValue('phone_mobile'))
            $this->errors[] = $this->module->l('You must register at least one phone number.');
        if ($address->id_country)
        {
            // Check country
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country))
                throw new PrestaShopException('Country cannot be loaded with address->id_country');

            if ((int)$country->contains_states && !(int)$address->id_state)
                $this->errors[] = $this->module->l('This country requires you to chose a State.');

            if (!$country->active)
                $this->errors[] = Tools::displayError('This country is not active.');

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode))
                $this->errors[] = sprintf($this->module->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
            elseif(empty($postcode) && $country->need_zip_code)
                $this->errors[] = $this->module->l('A Zip/Postal code is required.');
            elseif ($postcode && !Validate::isPostCode($postcode))
                $this->errors[] = $this->module->l('The Zip/Postal code is invalid.');

            // Check country DNI
            if ($country->isNeedDni() && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))))
                $this->errors[] = $this->module->l('The identification number is incorrect or has already been used.');
            else if (!$country->isNeedDni())
                $address->dni = null;
        }

        // Check the requires fields which are settings in the BO
        $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

        // Don't continue this process if we have errors !
        if ($this->errors && !$this->ajax)
            return false;

        // If we edit this address, delete old address and create a new one
        if (Validate::isLoadedObject($country) && !$country->contains_states)
            $address->id_state = 0;
        $address_old = new Address($seller->id_address);
        if ($address_old->id && Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id)) {
            if ($address_old->isUsed())
                $address_old->delete();
            else {
                $address->id = (int)($address_old->id);
                $address->date_add = $address_old->date_add;
            }
        }

        // Save address
        if ($result = $address->save())
        {           
            // Update id address of the current cart if necessary
            if (isset($address_old) && $address_old->isUsed())
                $this->context->cart->updateAddressId($address_old->id, $address->id);
            else // Update cart address
                $this->context->cart->autosetProductAddress();

            $this->context->cart->update();
            return $address->id;
        }
        $this->errors[] = $this->module->l('An error occurred while saving your company address.');
    }

    private function mailToSeller($seller) {
        $customer = $this->context->customer;
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
            '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
            '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
        );
        return Mail::Send($this->context->language->id,
            'account_request',
            $this->module->l('Seller account request', 'AccountRequest'),
            $mail_params,
            $this->context->customer->email,
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
    }

    private function mailToAdmin($seller) {
        $lang_id = intval(Configuration::get('PS_LANG_DEFAULT'));
        $name = strval(Configuration::get('PS_SHOP_NAME'));
        $email = strval(Configuration::get('PS_SHOP_EMAIL'));
        $mail_params = array(
            '{name}' => $seller->name,
            '{description}' => $seller->description[$lang_id],
            '{email}' => $this->context->customer->email,
            '{krs}' => $seller->krs,
            '{krs_reg}' => $seller->krs_reg,
            '{nip}' => $seller->nip,
            '{regon}' => $seller->regon,
            '{admin_link}' => Tools::getHttpHost(true).__PS_BASE_URI__.'backoffice/'.$this->context->link->getAdminLink('AdminSellers'),
        );
        $merchant_emails = Configuration::get('NPS_MERCHANT_EMAILS');
        if (!is_null($merchant_emails) && !empty($merchant_emails)) 
            $emails = $merchant_emails;
        else
            $emails = Configuration::get('PS_SHOP_EMAIL');

        return Mail::Send($lang_id,
            'memberalert',
            $this->module->l('New seller registration!', 'AccountRequest'),
            $mail_params,
            explode(self::__MA_MAIL_DELIMITOR__, $emails),
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
    }

    public function init() {
        $this->page_name = 'seller-request';
        parent::init();
    }

    public function initContent() {
        parent::initContent();
        
        $id_address = (int)Address::getFirstCustomerAddressId($this->context->customer->id);
        $account_state = 'none';
        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id) {
            $id_address = $seller->id_address;
            if ($seller->rerequested == 1 && $seller->active == 0 && $seller->locked == 0)
                $account_state = 'requested';
            else if ($seller->requested == 1 && $seller->active == 1 && $seller->locked == 0)
                $account_state = 'active';
            else if ($seller->requested == 1 && $seller->locked == 1)
                $account_state = 'locked';
        }

        $this -> context -> smarty -> assign(
            array(
                'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
                'seller' => array('image' => ''),
                'account_state' => $account_state,
                'account_request_date' => $seller->request_date,
                'current_id_lang' => (int)$this->context->language->id,
                'languages' => Language::getLanguages(),
                'user_agreement_url' =>  Configuration::get('NPS_SELLER_AGREEMENT_URL'),
                'processing_data_url' => '#',
                'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
                'add_product' => Tools::getValue('not_configured'),
                'address_tpl_path' =>_PS_MODULE_DIR_.'npsmarketplace/views/templates/front/address.tpl',
            )
        );
        
        $sellerHelper = new SellerHelper($this->context, new Address($id_address), $this->errors);
        $sellerHelper->initAddressContent();
        $this -> setTemplate('account_request.tpl');
    }

    protected function postImage($seller)
    {
        $ret = $this->uploadImage($seller);

        if (isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_NPS_SEL_IMG_DIR_.$seller->id.'.'.$seller->getImgFormat()))
        {
            $images_types = ImageType::getImagesTypes('sellers');
            foreach ($images_types as $k => $image_type)
            {
                ImageManager::resize(
                    _NPS_SEL_IMG_DIR_.$seller->id.'.'.$seller->getImgFormat(),
                    _NPS_SEL_IMG_DIR_.$seller->id.'-'.stripslashes($image_type['name']).'.'.$seller->getImgFormat(),
                    (int)$image_type['width'], (int)$image_type['height']
                );
            }
        }
        return $ret;
    }

    protected function uploadImage($seller)
    {
        $name = 'image';
        $dir = 'seller/';
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
        {
            // Delete old image
            $seller->deleteImage();

            // Check image validity
            $max_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size)))
                $this->errors[] = $error;

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name)
                return false;

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name))
                return false;

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name))
                $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$seller->id.'.'.$seller->getImgFormat()))
                $this->errors[] = Tools::displayError('An error occurred while uploading the image.');

            if (count($this->errors))
                return false;
            unlink($tmp_name);
            return true;
        }
        return true;
    }
 }
?>