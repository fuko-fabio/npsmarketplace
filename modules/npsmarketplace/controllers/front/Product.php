<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsMarketplaceProductModuleFrontController extends ModuleFrontController
{

    /**
     * @var _product Current product
     */
    protected $_product;

    /**
     * @var _seller Current product owner
     */
    protected $_seller;

    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS ("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");
        $this -> addJS(_PS_JS_DIR_.'validate.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/product_map.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/datetime_init.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('product_name')
            && Tools::isSubmit('product_price')
            && Tools::isSubmit('product_amount')
            && Tools::isSubmit('product_town')
            && Tools::isSubmit('product_address')
            && Tools::isSubmit('product_district'))
        {
            $product_name = $_POST['product_name'];
            $product_short_description = $_POST['product_short_description'];
            $product_description = $_POST['product_description'];
            $product_price = trim(Tools::getValue('product_price'));
            $product_amount = trim(Tools::getValue('product_amount'));
            $product_date_time = trim(Tools::getValue('product_date_time'));
            $product_town = trim(Tools::getValue('product_town'));
            $product_district = trim(Tools::getValue('product_district'));
            $product_address = trim(Tools::getValue('product_address'));
            $product_lat = trim(Tools::getValue('product_lat'));
            $product_lng = trim(Tools::getValue('product_lng'));
            $product_code = trim(Tools::getValue('product_code'));
            $categories = $_POST['category'];
            $link_rewrite = array();

            if (empty($product_address))
                $this -> errors[] = $this->l('Product address is required');
            if (empty($product_district))
                $this -> errors[] = $this->l('Product district is required');
            if (empty($product_town))
                $this -> errors[] = $this->l('Product town is required');
            if (empty($product_date_time))
                $this -> errors[] = $this->l('Product date and time is required');
            if (empty($product_price))
                $this -> errors[] = $this->l('Product price is required');
            if (!Validate::isFloat($product_price))
                $this -> errors[] = $this->l('Invalid product price');
            if (!Validate::isInt($product_amount))
                $this -> errors[] = $this->l('Invalid product amount');
            if (empty($product_amount))
                $this -> errors[] = $this->l('Product amount is required');
            if (!Validate::isMessage($product_code))
                $this -> errors[] = $this->l('Invalid product code');
            if (empty($categories))
                $this -> errors[] = $this->l('At least one category must be set');

            foreach (Language::getLanguages() as $key => $lang) {
                $p_name = $product_name[$lang['id_lang']];
                if (!Validate::isGenericName($p_name))
                    $this -> errors[] = $this->l('Invalid %s product name');
                if (!Validate::isCleanHtml($product_short_description[$lang['id_lang']]))
                    $this -> errors[] = $this->l('Invalid %s product short description');
                if (!Validate::isCleanHtml($product_description[$lang['id_lang']]))
                    $this -> errors[] = $this->l('Invalid %s product description');

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($p_name);
            }

            $product = new Product((int)Tools::getValue('id_product', null));
            if (empty($this -> errors)) {
                $product -> price = $product_price;
                $product -> name = $product_name;
                $product -> active = $active;
                $product -> description = $product_description;
                $product -> description_short = $product_short_description;
                $product -> link_rewrite = $link_rewrite;
                $product -> is_virtual = true;
                $product -> indexed = 1;
                $product -> id_tax_rules_group = 0;
                $product -> reference = $product_code;
                #$product -> location_lat = $product_lat;
                #$product -> location_lng = $product_lng;
                $product -> id_category_default = $categories[0];
                if (!$product->save())
                    $this->errors[] = $this->l('Unable to save product.');
                else 
                    StockAvailable::setQuantity($product->id, null, (int)$product_amount, $this->context->shop->id);
                    $this->saveFeatures($product, $product_town, $product_district, $product_address);
                    $this->saveAttribute($product, $product_date_time, (int)$product_amount);
                    $product->updateCategories($categories);
                    $this->saveProductImages($product);
                    $_seller->assignProduct($product->id);
                    Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
            }
        }
    }

    /**
     * Initialize product controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        
        $id_product = (int)Tools::getValue('id_product', 0);
        // Initialize product
        if ($id_product)
        {
            $this->_product = new Product($id_product);
            $_seller = new Seller(null, $this->context->customer->id);
            if (Validate::isLoadedObject($this->_product) && Validate::isLoadedObject($_seller) && Seller::sellerHasProduct($_seller->id, $id_product))
            {
                if (Tools::isSubmit('delete'))
                {
                    if ($this->_product->delete())
                        Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
                    $this->errors[] = $this->l('This product cannot be deleted.');
                }
            }
            elseif ($this->ajax)
                exit;
            else
                Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
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

    public function initContent()
    {
        parent::initContent();

        $tpl_product = array('categories' => array(), 'town' => null);
        if (isset($this->_product->id)) {
            $features = $this->_product->getFeatures();
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
            );
        }
        // TODO Read towns
        $towns = $this->getActiveTowns((int)$this->context->language->id);
        $districts = $this->getDistricts();

        $categoriesList = new CategoriesList($this->context);
        $this -> context -> smarty -> assign(array(
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
        ));

        $this->setTemplate('product.tpl');
    }

    private function getActiveTowns($lang_id) {
        $sql = 'SELECT t.`name` from `'._DB_PREFIX_.'town`
                LEFT JOIN `'._DB_PREFIX_.'town_lang` tl ON (tl.`id_town` = t.`id_town`)
                WHERE tl.`id_lang` = '.(int)$lang_id;
        return Db::getInstance()->execute($sql);
    }

    private function getDistricts() {
        return Db::getInstance()->execute('SELECT `name` from '._DB_PREFIX_.'district');
    }

    private function saveFeatures($product, $town, $district, $address) {
        if (!Feature::isFeatureActive())
            return;
        $feature_id = Configuration::get('NPS_FEATURE_TOWN_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $town, $product->id);
        Product::addFeatureProductImport($product->id, $feature_id, $feature_value_id);

        $feature_id = Configuration::get('NPS_FEATURE_DISTRICT_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $district, $product->id);
        Product::addFeatureProductImport($product->id, $feature_id, $feature_value_id);

        $feature_id = Configuration::get('NPS_FEATURE_ADDRESS_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $address, $product->id);
        Product::addFeatureProductImport($product->id, $feature_id, $feature_value_id);

        return true;
    }

    private function saveAttribute($product, $date_time, $quantity) {
        if (!Combination::isFeatureActive() || empty($date_time))
            return;
        $attr = new Attribute();
        $attr->name[$this->context->language->id] = $date_time;
        $attr->id_attribute_group = Configuration::get('NPS_ATTRIBUTE_DT_ID');
        $attr->position = -1;
        $attr->save();

        $id_combination = $product->addAttribute(
            0,//$price,
            null,//$weight,
            $quantity,//$unit_impact,
            null,//$ecotax,
            null,//$id_images,
            null,//$reference,
            null,//$ean13,
            $default);
       $combination = new Combination($id_combination);
       $combination->setAttributes(array($attr->id));
    }

    private function saveProductImages($product) {
        $image_uploader = new HelperImageUploader('product');
        $image_uploader -> setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg')) -> setMaxSize((int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'));
        $files = $image_uploader -> process();

        foreach ($files as &$file) {
            $image = new Image();
            $image -> id_product = (int)($product -> id);
            $image -> position = Image::getHighestPosition($product -> id) + 1;

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

                if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image -> image_format, null, null, 'jpg', false, $error)) {
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
                        if (!ImageManager::resize($file['save_path'], $new_path . '-' . stripslashes($imageType['name']) . '.' . $image -> image_format, $imageType['width'], $imageType['height'], $image -> image_format)) {
                            $this -> errors[] = Tools::displayError('An error occurred while copying image:') . ' ' . stripslashes($imageType['name']);
                            continue;
                        }
                    }
                }

                unlink($file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', array('id_image' => $image -> id, 'id_product' => $product -> id));

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

                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int)$product -> id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$product -> id . '_' . $this -> context -> shop -> id . '.jpg');
            }
        }
    }
}
?>