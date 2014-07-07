<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/

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
                if ($this->postImage())
                    Tools::redirect('index.php?controller=my-account');
                else
                    $this -> errors[] = Tools::displayError('Unable to save seller logo');
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
        if ($this->_seller->id) {
            if($type)
                $uri_path = _THEME_SEL_DIR_.$this->_seller->id.'-'.$type.'.jpg';
            else
                $uri_path = _THEME_SEL_DIR_.$this->_seller->id.($type ? '-'.$type : '').'.jpg';
            return $this->context->link->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
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