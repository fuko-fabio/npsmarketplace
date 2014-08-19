<?php
/*
*  @author Norbert Pabian
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceSellerAccountModuleFrontController extends ModuleFrontController {

    /**
     * @var _product Current product
     */
    protected $_seller;

    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS(array(_PS_JS_DIR_.'validate.js'));
    }

    public function postProcess()
    {
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('seller_name')
            && Tools::isSubmit('seller_phone')
            && Tools::isSubmit('seller_email')) {

            $nps_instance = new NpsMarketplace();

            $company_name = trim(Tools::getValue('company_name'));
            $name = trim(Tools::getValue('seller_name'));
            $phone = trim(Tools::getValue('seller_phone'));
            $email = trim(Tools::getValue('seller_email'));
            $nip = Tools::getValue('seller_nip');
            $regon = Tools::getValue('seller_regon');
            $company_description = $_POST['company_description'];
            $companyLogo = trim(Tools::getValue('company_logo'));
            $regulations_active = Tools::getIsset('regulations_active');
            $regulations = Tools::getValue('regulations');
            $link_rewrite = array();

            if (empty($name))
                $this -> errors[] = $nps_instance->l('Seller name is required');
            if (!Validate::isGenericName($name))
                $this -> errors[] = $nps_instance->l('Invalid seller name');

            if (empty($phone))
                $this -> errors[] = $nps_instance->l('Phone number is required');
            if (!Validate::isPhoneNumber($phone))
                $this -> errors[] = $nps_instance->l('Invalid phone number');

            if (empty($email))
                $this -> errors[] = $nps_instance->l('Buisness email is required');
            if (!Validate::isEmail($email))
                $this -> errors[] = $nps_instance->l('Invalid email addres');

            if (empty($company_name))
                $this -> errors[] = $nps_instance->l('Company name is required');
            if (!Validate::isGenericName($company_name))
                $this -> errors[] = $nps_instance->l('Invalid company name');

            if (!empty($nip) && !Validate::isNip($nip))
                $this -> errors[] = $nps_instance->l('Invalid NIP number');

            if (!empty($regon) && !Validate::isRegon($regon))
                $this -> errors[] = $nps_instance->l('Invalid REGON number');

            foreach (Language::getLanguages() as $key => $lang) {
                if (!Validate::isCleanHtml($company_description[$lang['id_lang']]))
                    $this -> errors[] = $nps_instance->l('Invalid company description');
                if (!Validate::isCleanHtml($regulations[$lang['id_lang']]))
                    $this -> errors[] = $nps_instance->l('Invalid regulations content');

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
            }

            if(empty($this->errors)) {
                $this->_seller -> company_name = $company_name;
                $this->_seller -> company_description = $company_description;
                $this->_seller -> name = $name;
                $this->_seller -> phone = $phone;
                $this->_seller -> email = $email;
                $this->_seller -> nip = $nip;
                $this->_seller -> regon = $regon;
                $this->_seller -> link_rewrite = $link_rewrite;
                $this->_seller -> regulations = $regulations;
                $this->_seller -> regulations_active = $regulations_active;
                $this->_seller->save();
                $this->postImage();
                Tools::redirect('index.php?controller=my-account' );
            }
        }
    }

    /**
     * Initialize seller controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        
        $id_seller = (int)Tools::getValue('id_seller', 0);
        // Initialize seller
        if ($id_seller)
            $this->_seller = new Seller($id_seller);
    }

    public function initContent() {
        parent::initContent();
        $tpl_seller = array();
        if (isset($this -> _seller -> id)) {
            $tpl_seller = array(
                'id' => $this -> _seller -> id,
                'image' => $this->getSellerImgLink('medium_default'),
                'name' => $this -> _seller -> name,
                'company_name' => $this -> _seller -> company_name,
                'company_description' => $this -> _seller -> company_description,
                'phone' => $this -> _seller -> phone,
                'email' => $this -> _seller -> email,
                'nip' => $this -> _seller -> nip,
                'regon' => $this -> _seller -> regon,
                'active' => $this -> _seller -> active,
                'request_date' => $this -> _seller -> request_date,
                'commision' => $this -> _seller -> commision,
                'account_state' => $this -> _seller->getAccountState(),
                'regulations' => $this -> _seller -> regulations,
                'regulations_active' => $this -> _seller -> regulations_active,
            );
        }

        $this -> context -> smarty -> assign(array(
            'seller' => $tpl_seller,
            'current_id_lang' => (int)$this -> context -> language -> id,
            'languages' => Language::getLanguages(),
            'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
        ));

        $this -> setTemplate('seller_account.tpl');
    }

    public function getSellerImgLink($type = null)
    {
        if (file_exists(_NPS_SEL_IMG_DIR_.$this->_seller->id.'.'.$this->_seller->getImgFormat())) {
            if ($this->_seller->id) {
                if($type)
                    $uri_path = _THEME_SEL_DIR_.$this->_seller->id.'-'.$type.'.jpg';
                else
                    $uri_path = _THEME_SEL_DIR_.$this->_seller->id.($type ? '-'.$type : '').'.jpg';
                return $this->context->link->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
            }
        }
    }

    protected function postImage()
    {
        $ret = $this->uploadImage();

        if (isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_NPS_SEL_IMG_DIR_.$this->_seller->id.'.'.$this->_seller->getImgFormat()))
        {
            $images_types = ImageType::getImagesTypes('sellers');
            foreach ($images_types as $k => $image_type)
            {
                ImageManager::resize(
                    _NPS_SEL_IMG_DIR_.$this->_seller->id.'.'.$this->_seller->getImgFormat(),
                    _NPS_SEL_IMG_DIR_.$this->_seller->id.'-'.stripslashes($image_type['name']).'.'.$this->_seller->getImgFormat(),
                    (int)$image_type['width'], (int)$image_type['height']
                );
            }
        }
        return $ret;
    }

    protected function uploadImage()
    {
        $name = 'image';
        $dir = 'seller/';
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
        {
            // Delete old image
            $this->_seller->deleteImage();

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
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$this->_seller->id.'.'.$this->_seller->getImgFormat()))
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