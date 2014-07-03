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
        $ret = parent::postImage($id);
        if (($id_category = (int)Tools::getValue('id_category')) &&
            isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_PS_CAT_IMG_DIR_.$id_category.'.jpg'))
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
}
?>