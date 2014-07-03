<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/
define('_NPS_SEL_IMG_DIR_', _PS_IMG_DIR_.'seller/');

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerRequestProcessor.php');

class NpsMarketplaceSellerAccountModuleFrontController extends ModuleFrontController {

    /**
     * @var _product Current product
     */
    protected $_seller;

    public function postProcess()
    {
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('seller_name')
            && Tools::isSubmit('seller_phone')
            && Tools::isSubmit('seller_email')
            && Tools::isSubmit('seller_nip')
            && Tools::isSubmit('seller_regon'))
        {
            $sp = new SellerRequestProcessor($this->context);
            $seller = $sp->processSubmit();
            $this->errors = $sp->errors;
            if(empty($this->errors))
            {
                $this->saveAccountImage();
                Tools::redirect('index.php?controller=my-account');
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
            $this->_seller = new SellerCore($id_seller);
    }

    public function initContent() {
        parent::initContent();

        $tpl_seller = array();
        if (isset($this -> _seller -> id))
            $tpl_seller = array(
                'id' => $this -> _seller -> id,
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
            );
        $this -> context -> smarty -> assign(array(
            'seller' => $tpl_seller,
            'current_id_lang' => (int)$this -> context -> language -> id,
            'languages' => Language::getLanguages(),
            'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
        ));

        $this -> setTemplate('seller_account.tpl');
    }

    private function saveAccountImage() {
        $image_uploader = new HelperImageUploader('seller');
        $image_uploader -> setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg')) -> setMaxSize((int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'));
        $files = $image_uploader -> process();
        d($files);
    }

    protected function postImage($id)
    {
        $fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'c'
        );
        $ret = $this->uploadImage($this->_seller->id, $fieldImageSettings['name'], $fieldImageSettings['dir'].'/');

        if (isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_NPS_SEL_IMG_DIR_.$this->_seller->id.'.jpg'))
        {
            $images_types = ImageType::getImagesTypes('categories');
            foreach ($images_types as $k => $image_type)
            {
                ImageManager::resize(
                    _PS_CAT_IMG_DIR_.$id_category.'.jpg',
                    _PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($image_type['name']).'.jpg',
                    (int)$image_type['width'], (int)$image_type['height']
                );
            }
        }

        return $ret;
    }

    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name']))
        {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject()))
                $object->deleteImage();
            else
                return false;

            // Check image validity
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
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
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$id.'.'.$this->imageType, (int)$width, (int)$height, ($ext ? $ext : $this->imageType)))
                $this->errors[] = Tools::displayError('An error occurred while uploading the image.');

            if (count($this->errors))
                return false;
            if ($this->afterImageUpload())
            {
                unlink($tmp_name);
                return true;
            }
            return false;
        }
        return true;
    }
}
?>