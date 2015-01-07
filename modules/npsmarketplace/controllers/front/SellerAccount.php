<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerHelper.php');

class NpsMarketplaceSellerAccountModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addjQueryPlugin('autosize');
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
            if ($seller->id == null)
                Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'AccountRequest'));

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
                $this -> errors[] = $this->module->l('Seller name is required', 'SellerAccount');
            else if (!Validate::isGenericName($name))
                $this -> errors[] = $this->module->l('Invalid seller name', 'SellerAccount');

            if (!empty($nip) && !Validate::isNip($nip))
                $this -> errors[] = $this->module->l('Invalid NIP number', 'SellerAccount');

            if (!empty($regon) && !Validate::isRegon($regon))
                $this -> errors[] = $this->module->l('Invalid REGON number', 'SellerAccount');

            foreach (Language::getLanguages() as $key => $lang) {
                if (!Validate::isCleanHtml($description[$lang['id_lang']]))
                    $this -> errors[] = $this->module->l('Invalid shop description', 'SellerAccount');
                if (!Validate::isCleanHtml($regulations[$lang['id_lang']]))
                    $this -> errors[] = $this->module->l('Invalid regulations content', 'SellerAccount');

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
            }

            if(empty($this->errors)) {
                $id_address = $this->processSubmitAddress($seller);
                if($id_address && empty($this->errors)) {
                    $seller->description = $description;
                    $seller->name = $name;
                    $seller->nip = $nip;
                    $seller->regon = $regon;
                    $seller->link_rewrite = $link_rewrite;
                    $seller->regulations = $regulations;
                    $seller->id_address = $id_address;
                    $seller->krs = $krs;
                    $seller->krs_reg = $krs_reg;
                    $seller->save();
                    $this->postImage($seller);
                    Tools::redirect('index.php?controller=my-account' );
                }
            }
        }
    }

    public function init() {
        $this->page_name = 'seller-account';
        parent::init();
    }

    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

        $tpl_seller = array();
        if (isset($seller-> id)) {
            $tpl_seller = array(
                'id' => $seller-> id,
                'image' => $this->getSellerImgLink($seller, 'medium_default'),
                'name' => $seller-> name,
                'company_description' => $seller->description,
                'krs' => $seller-> krs,
                'krs_reg' => $seller-> krs_reg,
                'nip' => $seller-> nip,
                'regon' => $seller-> regon,
                'active' => $seller-> active,
                'request_date' => $seller-> request_date,
                'commision' => $seller-> commision,
                'account_state' => $seller->getAccountState(),
                'regulations' => $seller-> regulations,
            );
        }

        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'seller' => $tpl_seller,
            'current_id_lang' => (int)$this -> context -> language -> id,
            'languages' => Language::getLanguages(),
            'my_shop_link' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
            'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
            'address_tpl_path' =>_PS_MODULE_DIR_.'npsmarketplace/views/templates/front/address.tpl',
            
        ));
        $id_address = $seller->id_address;
        $sellerHelper = new SellerHelper($this->context, new Address($id_address), $this->errors);
        $sellerHelper->initAddressContent();
        $this->setTemplate('seller_account.tpl');
    }

    protected function processSubmitAddress($seller) {
        $address = new Address();
        if (isset($_POST['company']) && !empty($_POST['company']) && strlen($_POST['company']) < 32)
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

    public function getSellerImgLink($seller, $type = null) {
        if (file_exists(_NPS_SEL_IMG_DIR_.$seller->id.'.'.$seller->getImgFormat())) {
            if ($seller->id) {
                if($type)
                    $uri_path = _THEME_SEL_DIR_.$seller->id.'-'.$type.'.jpg';
                else
                    $uri_path = _THEME_SEL_DIR_.$seller->id.($type ? '-'.$type : '').'.jpg';
                return $this->context->link->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
            }
        }
    }

    protected function postImage($seller) {
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

    protected function uploadImage($seller) {
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