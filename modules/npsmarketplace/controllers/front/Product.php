<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class NpsMarketplaceProductModuleFrontController extends ModuleFrontController {

    const MAX_IMAGES = 4;
    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    /**
     * @var _product Current product
     */
    protected $_product;

    /**
     * @var _seller Current product owner
     */
    protected $_seller;

    public function setMedia() {
        parent::setMedia();
        $this->addjQueryPlugin('autosize');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/dropzone.js');
        $this->addJS ("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");
        $this->addJS(_PS_JS_DIR_.'validate.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/edit_map.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/dropzone_init.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/datetime_init.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/tinymce/tinymce.min.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/tinymce_init.js');

        $this->addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/dropzone.css');
        $this->addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
        $this->addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
    }

    public function postProcess() {
        if (Tools::isSubmit('saveProduct')) {
            $nps_instance = new NpsMarketplace();
            $current_id_product = trim(Tools::getValue('id_product'));

            $name = $_POST['name'];
            $description_short = $_POST['description_short'];
            $description = $_POST['description'];
            $price = trim(Tools::getValue('price'));
            $quantity = trim(Tools::getValue('quantity'));
            $date = trim(Tools::getValue('date'));
            $time = trim(Tools::getValue('time'));
            $expiry_date = trim(Tools::getValue('expiry_date'));
            $town = trim(Tools::getValue('town'));
            $district = trim(Tools::getValue('district'));
            $address = trim(Tools::getValue('address'));
            $video_url = trim(Tools::getValue('video_url'));
            $lat = trim(Tools::getValue('lat'));
            $lng = trim(Tools::getValue('lng'));
            $reference = trim(Tools::getValue('reference'));
            $categories = array();
            $link_rewrite = array();
            $images = array();
            $removed_images = array();
            if (isset($_POST['category']))
                $categories = $_POST['category'];

            if(isset($_POST['images']))
                $images = $_POST['images'];

            if(isset($_POST['removed_images']))
                $removed_images = $_POST['removed_images'];

            if(Tools::getValue('form_token') != $this->context->cookie->__get('form_token')) {
                $this -> errors[] = $nps_instance->l('This form has been already saved. Go to your profile page and check saved products.');
                return;
            }

            if (empty($name[(int)$this->context->language->id]))
                $this -> errors[] = $nps_instance->l('Product name is required');
            if (empty($address))
                $this -> errors[] = $nps_instance->l('Product address is required');
            if (empty($district))
                $this -> errors[] = $nps_instance->l('Product district is required');
            if (empty($town))
                $this -> errors[] = $nps_instance->l('Product town is required');
            if (!isset($price))
                $this -> errors[] = $nps_instance->l('Product price is required');
            if (!Validate::isFloat($price))
                $this -> errors[] = $nps_instance->l('Invalid product price');
            if (!Validate::isMessage($reference))
                $this -> errors[] = $nps_instance->l('Invalid product reference');
            if (empty($categories))
                $this -> errors[] = $nps_instance->l('At least one category must be selected');

            if(empty($current_id_product)) {
                if (empty($expiry_date))
                    $this -> errors[] = $nps_instance->l('Product expiry date is required');
                else if (!Validate::isDateFormat($expiry_date))
                    $this -> errors[] = $nps_instance->l('Invalid expiry date format');
    
                if (empty($date))
                    $this -> errors[] = $nps_instance->l('Product date is required');
                else if (!Validate::isDateFormat($date))
                    $this -> errors[] = $nps_instance->l('Invalid date format');
    
                if (empty($time))
                    $this -> errors[] = $nps_instance->l('Product time is required');
                else if (!Validate::isTime($time))
                    $this -> errors[] = $nps_instance->l('Invalid date format');
    
                if (empty($quantity))
                    $this -> errors[] = $nps_instance->l('Product quantity is required');
                else if (!Validate::isInt($quantity))
                    $this -> errors[] = $nps_instance->l('Invalid product quantity format');
                if (empty($images))
                    $this -> errors[] = $nps_instance->l('At least one picture is required');
                else if (count($images) > self::MAX_IMAGES)
                    $this -> errors[] = $nps_instance->l('You can upload max 4 pictures');
            } else {
                $current_images = Image::getImages($this->context->language->id, $current_id_product);
                $img_sum = count($current_images) - count($removed_images) + count($images);
                if ($img_sum < 1)
                    $this -> errors[] = $nps_instance->l('At least one picture is required');
                else if ($img_sum > self::MAX_IMAGES)
                    $this -> errors[] = $nps_instance->l('You can upload max 4 pictures');
            }

            foreach (Language::getLanguages() as $key => $lang) {
                if(empty($name[$lang['id_lang']])) {
                    $name[$lang['id_lang']] = $name[(int)$this->context->language->id];
                }
                if(empty($description_short[$lang['id_lang']])) {
                    $description_short[$lang['id_lang']] = $description_short[(int)$this->context->language->id];
                }
                if(empty($description[$lang['id_lang']])) {
                    $description[$lang['id_lang']] = $description[(int)$this->context->language->id];
                }
                $p_name = $name[$lang['id_lang']];
                if (!Validate::isGenericName($p_name))
                    $this -> errors[] = $nps_instance->l('Invalid product name');
                if (!Validate::isCleanHtml($description_short[$lang['id_lang']]))
                    $this -> errors[] = $nps_instance->l('Invalid product short description');
                if (!Validate::isCleanHtml($description[$lang['id_lang']]))
                    $this -> errors[] = $nps_instance->l('Invalid product description');

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($p_name);
            }

            if (empty($this -> errors)) {
                $this->_product -> price = $price;
                $this->_product -> name = $name;
                $this->_product -> description = $description;
                $this->_product -> description_short = $description_short;
                $this->_product -> link_rewrite = $link_rewrite;
                $this->_product -> reference = $reference;
                $this->_product -> id_category_default = $categories[0];
                if(empty($current_id_product)) {
                    $this->_product -> is_virtual = true;
                    $this->_product -> indexed = 1;
                    $this->_product -> id_tax_rules_group = 0;
                    $this->_product -> active = $this->isSellerAllowedToPublish();
                }
                if (!$this->_product->save()) {
                    $this->errors[] = $nps_instance->l('Unable to save product.');
                } else {
                    $this->context->cookie->__unset('form_token');
                    $this->_product->persistVideoUrl($video_url);
                    StockAvailable::setProductOutOfStock($this->_product->id, 0);
                    $this->saveFeatures($town, $district, $address);
                    $this -> _product->updateCategories($categories);
                    if(empty($current_id_product)) {
                        $this -> _product->newEventCombination($date, $time, (int)$quantity, $expiry_date, $this->context->shop->id);
                        $this->_seller->assignProduct($this->_product->id);
                    }
                    $this->saveProductImages($images);
                    $this->removeProductImages($removed_images, $current_id_product);
                    Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList'));
                }
            }
        }
    }

    public function isSellerAllowedToPublish() {
        if ($this->_seller->active) {
            $settings = new P24SellerCompany(null, $this->_seller->id);
            return $settings->id != null ? true : false;
        }
        return false;
    }
    /**
     * Initialize product controller
     * @see FrontController::init()
     */
    public function init() {
        parent::init();

        $this->_seller = new Seller(null, $this->context->customer->id);
        if ($this->_seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

        $id_product = (int)Tools::getValue('id_product', 0);
        if($id_product != 0) {
            $products = $this->_seller->getSellerProducts($this->_seller->id);

            if (!in_array($id_product, $products))
                Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'productsList'));
        }

        $nps_instance = new NpsMarketplace();
        $this->_product = new Product($id_product);

        if ($id_product) {
            if (Validate::isLoadedObject($this->_product) && Validate::isLoadedObject($this->_seller) && Seller::sellerHasProduct($this->_seller->id, $id_product)) {
                if (Tools::isSubmit('delete')) {
                    if ($this->_product->delete())
                        Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'productsList'));
                    $this->errors[] = $nps_instance->l('This product cannot be deleted.');
                }
            }
            elseif ($this->ajax)
                exit;
            else
                Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'productsList'));
        }
    }

    private function getFeatureValue($features, $name) {
        foreach ($features as $featre) {
            if ($featre['id_feature'] == Configuration::get('NPS_FEATURE_'.strtoupper($name).'_ID')) {
                $f = new FeatureValue($featre['id_feature_value']);
                return $f->value[$this->context->language->id];
            }
        }
    }

    public function initContent() {
        parent::initContent();

        $tpl_product = array('categories' => array(), 'town' => null, 'images' => array());
        if ($this->_product->id) {
            $features = $this->_product->getFeatures();
            $images = Image::getImages($this->context->language->id, $this->_product->id);
            foreach ($images as $k => $image)
                $images[$k] = array(
                    'url' => $this->context->link->getImageLink($this->_product->link_rewrite[$this->context->language->id], $image['id_image'], 'medium_default'),
                    'id_image' => $image['id_image']
                );
                
            $tpl_product = array(
                'id' => $this->_product->id,
                'name' => $this->_product->name,
                'description_short' => $this->_product->description_short,
                'description' => $this->_product->description,
                'price' => $this->_product->getPrice(),
                'quantity' => Product::getQuantity($this->_product->id),
                'reference' => $this->_product->reference,
                'town' => $this->getFeatureValue($features, 'town'),
                'address' => $this->getFeatureValue($features, 'address'),
                'district' => $this->getFeatureValue($features, 'district'),
                'categories' => $this->_product->getCategories(),
                'images' => $images,
                'video_url' => Product::getVideoUrl($this->_product->id)
            );
        }
        $towns = $this->getActiveTowns((int)$this->context->language->id);
        $districts = $this->getDistricts();
        $categoriesList = new CategoriesList($this->context);
        $form_token = uniqid();
        $this->context->cookie->__set('form_token', $form_token);
        $iso = $this->context->language->iso_code;

        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'user_agreement_url' =>'#',
            'categories_tree' => $categoriesList -> getTree(),
            'category_partial_tpl_path' =>_PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl',
            'product_fieldset_tpl_path'=> _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/product_fieldset.tpl',
            'product' => $tpl_product,
            'edit_product' => array_key_exists('id', $tpl_product),
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
            'towns' => $towns,
            'districts' => $districts,
            'form_token' => $form_token,
            'max_images' => self::MAX_IMAGES,
            'max_image_size' => (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 /1024, 
            'new_tem_link' => $this->context->link->getModuleLink('npsmarketplace', 'ProductCombination', array('id_product' => $this->_product->id)),
            'iso' => file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en',
            'path_css' => _THEME_CSS_DIR_,
            'tinymce' => true,
            'dropzone_url' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/npsmarketplace/dropzone.php?token='.$form_token,
            'vide_how_to_url' => Configuration::get('NPS_EVENT_VIDEO_GUIDE_URL')
        ));

        $this->setTemplate('product.tpl');
    }

    private function getActiveTowns($lang_id) {
        $sql = 'SELECT `name` from `'._DB_PREFIX_.'town` t
                LEFT JOIN `'._DB_PREFIX_.'town_lang` tl ON (tl.`id_town` = t.`id_town`)
                WHERE tl.`id_lang` = '.(int)$lang_id;
        return Db::getInstance()->ExecuteS($sql);
    }

    private function getDistricts() {
        return Db::getInstance()->ExecuteS('SELECT `name` from '._DB_PREFIX_.'district');
    }

    private function saveFeatures($town, $district, $address) {
        $feature_id = Configuration::get('NPS_FEATURE_TOWN_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $town);
        Product::addFeatureProductImport($this->_product->id, $feature_id, $feature_value_id);

        $feature_id = Configuration::get('NPS_FEATURE_DISTRICT_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $district);
        Product::addFeatureProductImport($this->_product->id, $feature_id, $feature_value_id);

        $feature_id = Configuration::get('NPS_FEATURE_ADDRESS_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $address);
        Product::addFeatureProductImport($this->_product->id, $feature_id, $feature_value_id);

        return true;
    }

    private function removeProductImages($images_ids, $id_product) {
        if(empty($images_ids))
            return true;
        $res = true;
        foreach ($images_ids as $id_image) {
            $image = new Image($id_image);
            $res &= $image->delete();
            if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg'))
                $res &= @unlink(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg');
            if (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'_'.$this->context->shop->id.'.jpg'))
                $res &= @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'_'.$this->context->shop->id.'.jpg');
        }
        if (!Image::getCover($id_product)) {
            $res &= Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'image_shop` image_shop, '._DB_PREFIX_.'image i
            SET image_shop.`cover` = 1,
            i.cover = 1
            WHERE image_shop.`id_image` = (SELECT id_image FROM
                                                        (SELECT image_shop.id_image
                                                            FROM '._DB_PREFIX_.'image i'.
                                                            Shop::addSqlAssociation('image', 'i').'
                                                            WHERE i.id_product ='.(int)$id_product.' LIMIT 1
                                                        ) tmpImage)
            AND id_shop='.(int)$this->context->shop->id.'
            AND i.id_image = image_shop.id_image
            ');
        }
        return $res;
    }

    private function saveProductImages($files) {
        foreach ($files as $file) {
            $image = new Image();
            $image -> id_product = (int)($this -> _product -> id);
            $image -> position = Image::getHighestPosition($this -> _product -> id) + 1;

            if (!Image::getCover($image -> id_product))
                $image -> cover = 1;
            else
                $image -> cover = 0;

            if (($validate = $image -> validateFieldsLang(false, true)) !== true)
                $this -> errors[] = Tools::displayError($validate);

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0))
                continue;

            if (!$image -> add())
                $this -> errors[] = Tools::displayError('Error while creating additional image');
            else {
                if (!$new_path = $image -> getPathForCreation()) {
                    $this -> errors[] = Tools::displayError('An error occurred during new folder creation');
                    continue;
                }

                $error = 0;

                if (!ImageManager::resize(_PS_UPLOAD_DIR_.$file['save_path'], $new_path . '.' . $image -> image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST :
                            $this -> errors[] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
                            break;

                        case ImageManager::ERROR_FILE_WIDTH :
                            $this -> errors[] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
                            break;

                        case ImageManager::ERROR_MEMORY_LIMIT :
                            $this -> errors[] = Tools::displayError('An error occurred while copying image, check your memory limit.');
                            break;

                        default :
                            $this -> errors[] = Tools::displayError('An error occurred while copying image.');
                            break;
                    }
                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize(_PS_UPLOAD_DIR_.$file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '.' . $image -> image_format, $imageType['width'], $imageType['height'], $image -> image_format)) {
                            $this -> errors[] = Tools::displayError('An error occurred while copying image:') . ' ' . stripslashes($imageType['name']);
                            continue;
                        }
                    }
                }

                unlink(_PS_UPLOAD_DIR_.$file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', array('id_image' => $image -> id, 'id_product' => $this -> _product -> id));

                if (!$image -> update()) {
                    $this -> errors[] = Tools::displayError('Error while updating status');
                    continue;
                }

                // Associate image to shop from context
                $shops = Shop::getContextListShopID();
                $image -> associateTo($shops);
                $json_shops = array();

                foreach ($shops as $id_shop)
                    $json_shops[$id_shop] = true;

                $file['status'] = 'ok';
                $file['id'] = $image -> id;
                $file['position'] = $image -> position;
                $file['cover'] = $image -> cover;
                $file['legend'] = $image -> legend;
                $file['path'] = $image -> getExistingImgPath();
                $file['shops'] = $json_shops;

                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int)$this -> _product -> id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$this -> _product -> id . '_' . $this -> context -> shop -> id . '.jpg');
            }
        }
    }
}
?>
