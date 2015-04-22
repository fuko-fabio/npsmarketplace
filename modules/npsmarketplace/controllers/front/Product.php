<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Town.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Province.php');
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
        $this->addJqueryUI(array('ui.slider', 'ui.datepicker'));
        $this->addJS(array(
            _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/dropzone.js',
            "https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places",
            _PS_JS_DIR_.'validate.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/edit_map.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/base64.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/dropzone_init.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/tinymce/tinymce.min.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/tinymce_init.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/product.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/CollapsibleLists.compressed.js',
            _PS_MODULE_DIR_.'npsmarketplace/js/tmpl.min.js'
        ));

        $this->addCSS(_PS_MODULE_DIR_.'npsmarketplace/css/dropzone.css');
        $this->addCSS(_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
    }

    public function postProcess() {
        if (Tools::isSubmit('saveProduct')) {

            $current_id_product = trim(Tools::getValue('id_product'));
            $name = $_POST['name'];
            $description_short = $_POST['description_short'];
            $description = $_POST['description'];
            $town = trim(Tools::getValue('town'));
            $province = trim(Tools::getValue('province'));
            $district = trim(Tools::getValue('district'));
            $address = trim(Tools::getValue('address'));
            $video_source = trim(Tools::getValue('video_url'));

            $lat = trim(Tools::getValue('lat'));
            $lng = trim(Tools::getValue('lng'));

            $combinations = array();
            $categories = array();
            $link_rewrite = array();
            $images = array();
            $removed_images = array();
            
            if (isset($_POST['combinations'])) {
                $combinations = $_POST['combinations'];
            }

            if (isset($_POST['category'])) {
                $categs = $_POST['category'];
                foreach ($categs as $key => $value) {
                    if (strpos($value, '|') !== false) {
                        $ids = explode('|', $value);
                        $categories = array_merge($categories, $ids);
                    } else
                        $categories[] = $value;
                }
                $categories = array_unique($categories);
            }

            if(isset($_POST['images']))
                $images = $_POST['images'];

            if(isset($_POST['removed_images']))
                $removed_images = $_POST['removed_images'];

            if(Tools::getValue('form_token') != $this->context->cookie->__get('form_token')) {
                $this -> errors[] = $this->module->l('This form has been already saved. Go to your profile page and check list of events.', 'Product');
                return;
            }

            if (substr($video_source, 0, 7 ) === "<iframe") {
                preg_match('/src="([^"]+)"/', $video_source, $match);
                $video_url = $match[1];
            } else {
                $video_url = $video_source;
            }

            if (empty($name[(int)$this->context->language->id]))
                $this -> errors[] = $this->module->l('Product name is required', 'Product');
            if (empty($address))
                $this -> errors[] = $this->module->l('Product address is required', 'Product');
            if (!Validate::isGenericName($district))
                $this -> errors[] = $this->module->l('Invalid district name', 'Product');
            if (!isset($town))
                $this -> errors[] = $this->module->l('Product town is required', 'Product');
            if (!isset($province))
                $this -> errors[] = $this->module->l('Product province is required', 'Product');
            if (empty($combinations))
                $this -> errors[] = $this->module->l('At least one combination is required', 'Product');
            if (empty($categories))
                $this -> errors[] = $this->module->l('At least one category must be selected', 'Product');

            if(empty($current_id_product)) {
                //    if (strtotime($date.' '.$time) < time())
                //        $this -> errors[] = $this->module->l('You are trying to add event in the past. Check event date and time.', 'Product');
                //    if (strtotime($expiry_date.' '.$expiry_time) > strtotime($date.' '.$time))
                //        $this -> errors[] = $this->module->l('Expiry date and time cannot be later than event date.', 'Product');

                //} if ($type == 1) {
                //    if (isset($date_to) && !empty($date_to) && !Validate::isDateFormat($date_to))
                //        $this -> errors[] = $this->module->l('Invalid carnet \'date to\' format', 'Product');
                //    if (isset($date_from) && !empty($date_from) && !Validate::isDateFormat($date_from))
                //        $this -> errors[] = $this->module->l('Invalid carnet \'date from\' format', 'Product');
                //    if (isset($entries) && !empty($entries) && !Validate::isInt($entries))
                //        $this -> errors[] = $this->module->l('Invalid entries value', 'Product');
                //}
                if (empty($images))
                    $this -> errors[] = $this->module->l('At least one picture is required', 'Product');
                else if (count($images) > self::MAX_IMAGES)
                    $this -> errors[] = $this->module->l('You can upload max 4 pictures', 'Product');
            } else {
                $current_images = Image::getImages($this->context->language->id, $current_id_product);
                $img_sum = count($current_images) - count($removed_images) + count($images);
                if ($img_sum < 1)
                    $this -> errors[] = $this->module->l('At least one picture is required', 'Product');
                else if ($img_sum > self::MAX_IMAGES)
                    $this -> errors[] = $this->module->l('You can upload max 4 pictures', 'Product');
            }

            $d_s_limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($d_s_limit <= 0) $d_s_limit = 400;
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
                    $this -> errors[] = $this->module->l('Invalid product name', 'Product');
                $nl = Tools::strlen($p_name);
                if ($nl > 128)
                    $this->errors[] = sprintf(
                    $this->module->l('Name too long. Max allowed characters: 128, now is: %d characters. Language: %s', 'Product'),
                    $nl,
                    $lang['iso_code']);

                if (!Validate::isCleanHtml($description_short[$lang['id_lang']]))
                    $this -> errors[] = $this->module->l('Invalid product short description', 'Product');
                if (!Validate::isCleanHtml($description[$lang['id_lang']]))
                    $this -> errors[] = $this->module->l('Invalid product description', 'Product');
                // Check description short size without html
                $dsl = Tools::strlen(strip_tags($description_short[$lang['id_lang']]));
                if ($dsl > $d_s_limit)
                    $this->errors[] = sprintf(
                    $this->module->l('Short description too long. Max allowed characters: %d, now is: %d characters. Language: %s', 'Product'),
                    $d_s_limit,
                    $dsl,
                    $lang['iso_code']);

                $link_rewrite[$lang['id_lang']] = Tools::link_rewrite($p_name);
            }

            $free_cat_id = Configuration::get('NPS_FREE_CATEGORY_ID');
            if (isset($free_cat_id) && !empty($free_cat_id)){
                $is_free = true;
                foreach ($combinations as $key => $value) {
                    if (($value['type'] == 'ticket' || $value['type'] == 'carnet') && $value['price'] == 0) {
                        $is_free = true;
                        break;
                    }
                }
                if ($is_free) {
                    $categories[] = $free_cat_id;
                } else {
                    if (in_array($free_cat_id, $categories)) {
                        $categories = array_diff($categories, $free_cat_id);
                    }
                }
            }

            if (empty($this->errors)) {
                $this->_product -> name = $name;
                $this->_product -> description = $description;
                $this->_product -> description_short = $description_short;
                $this->_product -> link_rewrite = $link_rewrite;
                $this->_product -> id_category_default = $categories[0];
                if(empty($current_id_product)) {
                    $this->_product -> is_virtual = true;
                    $this->_product -> indexed = 1;
                    $this->_product -> id_tax_rules_group = 0;
                    $this->_product -> active = $this->isSellerAllowedToPublish();
                }
                if (!$this->_product->save()) {
                    $this->errors[] = $this->module->l('Unable to save product.', 'Product');
                } else {
                    $done = true;
                    try {
                        $this->saveProductImages($images);
                        if (empty($this->errors)) {
                            StockAvailable::setProductOutOfStock($this->_product->id, 0);
                            $this->saveLocationFeatures($province, $town, $district, $address);

                            if(empty($current_id_product)) {
                                $this->_seller->assignProduct($this->_product->id);
                            }
                            $this->updateCategories($categories);
                            $this->_product->persistExtraInfo($lat, $lng, $video_url);
                            $this->removeProductImages($removed_images, $current_id_product);
                            $this->saveCombinations($combinations);
                        } else {
                            $done = false;
                        }
                    } catch(Exception $e) {
                        $done = false;
                        error_log($e);
                        $this->errors[] = $this->module->l('Unable to save product. Unexpected error occured. Please try again or contact with customer support.', 'Product');
                    }
                    if ($done) {
                        $this->context->cookie->__unset('form_token');
                        Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList'));
                    } else if (empty($current_id_product)) {
                        $this->_product->delete();
                        $this->_product = new Product();
                    }
                }
            }
        }
    }

    private function updateCategories($categories) {
        $global_cat_id = 2;
        $currentCategories = $this->_product->getCategories();
        if (!in_array($global_cat_id, $currentCategories)) {
            $categories = array_diff($categories, array($global_cat_id));
        }
        $this->_product->updateCategories($categories);
    }

    public function isSellerAllowedToPublish() {
        if ($this->_seller->active) {
            $settings = new P24SellerCompany(null, $this->_seller->id);
            return $settings->id != null ? true : false;
        }
        return false;
    }

    private function saveCombinations($combinations) {
        $current = $this->_product->getAttributesIds();
        $updated = array();
        foreach ($combinations as $key => $combination) {
            if (isset($combination['id_product_attribute'])) {
                $comb = new Combination($combination['id_product_attribute']);
                $comb->price = $combination['price'];
                $comb->default_on = isset($combination['default']) ? $combination['default'] : false;
                $comb->save();
                StockAvailable::setQuantity((int)$this->_product->id, (int)$combination['id_product_attribute'], $combination['quantity'], $this->context->shop->id);
                $updated[] = $combination['id_product_attribute'];
            } else {
                $dt = new DateTime($combination['expiry_date']);
                if ($combination['type'] != 'carnet' && isset($combination['date']) && $combination['date'] == $combination['expiry_date']) {
                    $dt->modify('-15 min');
                }
                $comb = $this->_product->createCombination($combination, $dt,  $this->context->shop->id);
            }
            // Save new specific price
            $current_specific_prices = array();
            foreach(SpecificPrice::getByProductId($this->_product->id, $comb->id) as $key => $value) {
                $current_specific_prices[] = $value['id_specific_price'];
            }
            $updated_specific_prices = array();
            if (isset($combination['specific_prices'])) {
                foreach ($combination['specific_prices'] as $key => $value) {
                    $from = new DateTime($value['from']);
                    $to = new DateTime($value['to']);
                    if (isset($value['id_specific_price'])) {
                        $sp = new SpecificPriceCore($value['id_specific_price']);
                        $sp->from = $from->format('Y-m-d H:i:s');
                        $sp->to = $to->format('Y-m-d H:i:s');
                        $sp->reduction = $value['reduction'];
                        $sp->save();
                        $updated_specific_prices[] = $sp->id;
                    } else {
                        Product::addSpecialPrice(
                            $this->_product->id,
                            $comb->id,
                            $value['reduction'],
                            $from->format('Y-m-d H:i:s'),
                            $to->format('Y-m-d H:i:s'));
                    }
                }
            }
            $removed_specific_prices = array_diff($current_specific_prices, $updated_specific_prices);
            foreach ($removed_specific_prices as $key => $value) {
                $sp = new SpecificPriceCore($value);
                $sp->delete();
            }
        }
        $removed = array_diff($current, $updated);
        foreach ($removed as $key => $value) {
            $this->_product->deleteAttributeCombination((int)$value);
        }
        if (!$this->_product->hasAttributes()) {
            $this->_product->cache_default_attribute = 0;
            $this->_product->update();
            if($this->_product->active)
                $this->_product->toggleStatus();
        } else {
            if(!$this->_product->active)
                $this->_product->toggleStatus();
        }
        $this->_product->checkDefaultAttributes();
        Product::updateDefaultAttribute($this->_product->id);
    }

    /**
     * Initialize product controller
     * @see FrontController::init()
     */
    public function init() {
        $this->page_name = 'add-product';
        parent::init();

        $this->_seller = new Seller(null, $this->context->customer->id);
        if ($this->_seller->id == null) 
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'AccountRequest', array('not_configured' => 1)));

        $id_product = (int)Tools::getValue('id_product', 0);
        $products = Seller::getSellerProducts($this->_seller->id);

        if($id_product != 0) {
            if (!in_array($id_product, $products))
                Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList', array('not_found' => 1)));
        }

        $state = $this->_seller->getAccountState();
        if ($state == 'requested' && count($products) >= 1 && !$id_product) {
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList', array('not_configured' => 1)));
        } else if ($state == 'locked') {
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'));
        } else if ($state == 'none') {
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'AccountRequest', array('not_configured' => 1)));
        } else if ($state == 'active' && count($products) >= 1 && !$this->isSellerAllowedToPublish()) {
            Tools::redirect($this->context->link->getModuleLink('npsprzelewy24', 'PaymentSettings', array('not_configured' => 1)));
        }

        $this->_product = new Product($id_product);
        if ($id_product) {
            if (Validate::isLoadedObject($this->_product) && Validate::isLoadedObject($this->_seller) && Seller::sellerHasProduct($this->_seller->id, $id_product)) {
                if (Tools::isSubmit('delete')) {
                    if ($this->_product->delete())
                        Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList'));
                    $this->errors[] = $this->module->l('This product cannot be deleted.', 'Product');
                }
            }
            elseif ($this->ajax)
                exit;
            else
                Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList'));
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

    private function getFeatureValueId($features, $name) {
        foreach ($features as $featre) {
            if ($featre['id_feature'] == Configuration::get('NPS_FEATURE_'.strtoupper($name).'_ID')) {
                return $featre['id_feature_value'];
            }
        }
    }

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $loc = NpsMarketplace::getLocation();
        $town = new Town($loc['town']);
        $province = new Province($loc['province']);
        if (!$town->id) {
            $id_province = $loc['province'];
        } else {
            $id_province = $town->id_province;
        }
        if (!$id_province) {
            $sql = 'SELECT id_province FROM '._DB_PREFIX_.'province WHERE active = 1';
            $id_province = Db::getInstance()->getValue($sql);
        }
        
        $tpl_product = array(
            'categories' => array(),
            'province' => Province::getFeatureValueId($id_province),
            'town' => $town->id ? Town::getFeatureValueId($town->id) : 0,
            'images' => array(),
            'combinations' => array()
        );

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
                'province' => $this->getFeatureValueId($features, 'province'),
                'town' => $this->getFeatureValueId($features, 'town'),
                'address' => $this->getFeatureValue($features, 'address'),
                'district' => $this->getFeatureValue($features, 'district'),
                'categories' => $this->_product->getCategories(),
                'images' => $images,
                'combinations' => $this->getCombinations()
            );
            $extras = Product::getExtras($this->_product->id, $this->context->language->id);
            if ($extras) {
                $tpl_product['lat'] = $extras['lat'];
                $tpl_product['lng'] = $extras['lng'];
                $tpl_product['video_url'] = $extras['video'];
            }
        }
        $districts = $this->getDistricts();
        $categoriesList = new CategoriesList($this->context);
        $specialCategoriesIds = explode(',', Configuration::get('NPS_SPECIAL_CATEGORIES'));
        $invisibleCategoriesIds = explode(',', Configuration::get('NPS_INVISIBLE_CATEGORIES'));
        $form_token = uniqid();
        $this->context->cookie->__set('form_token', $form_token);
        $iso = $this->context->language->iso_code;
        $cat = $categoriesList->getTree(array_merge($specialCategoriesIds, $invisibleCategoriesIds));
        $s_cat = $categoriesList->getList($specialCategoriesIds);

        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'user_agreement_url' => Configuration::get('NPS_SELLER_AGREEMENT_URL'),
            'seller' => $this->_seller,
            'categories_tree' => $cat,
            'special_categories_tree' => $s_cat,
            'category_partial_tpl_path' =>_PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl',
            'product_fieldset_tpl_path'=> _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/product_fieldset.tpl',
            'variants_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/variants.tpl',
            'free_category_id' => Configuration::get('NPS_FREE_CATEGORY_ID'),
            'product' => $tpl_product,
            'edit_product' => array_key_exists('id', $tpl_product),
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
            'towns' => Town::getActiveTowns($this->context->language->id, Province::getIdByFeatureValueId($tpl_product['province'])),
            'provinces' => Province::getActiveProvinces($this->context->language->id, true),
            'districts' => $districts,
            'form_token' => $form_token,
            'max_images' => self::MAX_IMAGES,
            'max_image_size' => (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 /1024, 
            'new_tem_link' => $this->context->link->getModuleLink('npsmarketplace', 'ProductCombination', array('id_product' => $this->_product->id)),
            'iso' => file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en',
            'path_css' => _THEME_CSS_DIR_,
            'tinymce' => true,
            'dropzone_url' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/npsmarketplace/dropzone.php?token='.$form_token,
            'vide_how_to_url' => Configuration::get('NPS_EVENT_VIDEO_GUIDE_URL'),
            'description_how_to_url' => Configuration::get('NPS_EVENT_DESC_GUIDE_URL'),
            'images_how_to_url' => Configuration::get('NPS_EVENT_IMAGE_GUIDE_URL'),
            'currency' => $this->context->currency
        ));

        $this->setTemplate('product.tpl');
    }

    private function getDistricts() {
        return Db::getInstance()->ExecuteS('SELECT `name` from '._DB_PREFIX_.'district');
    }

    private function saveLocationFeatures($province_id_feature_value, $town_id_feature_value, $district, $address) {
        $feature_id = Configuration::get('NPS_FEATURE_PROVINCE_ID');
        Product::addFeatureProductImport($this->_product->id, $feature_id, $province_id_feature_value);

        if ($town_id_feature_value > 0) {
            $feature_id = Configuration::get('NPS_FEATURE_TOWN_ID');
            Product::addFeatureProductImport($this->_product->id, $feature_id, $town_id_feature_value);
        }

        if (!empty($district)) {
            $feature_id = Configuration::get('NPS_FEATURE_DISTRICT_ID');
            $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $district, $this->context->language->id, true);
            Product::addFeatureProductImport($this->_product->id, $feature_id, $feature_value_id);
        }

        $feature_id = Configuration::get('NPS_FEATURE_ADDRESS_ID');
        $feature_value_id = FeatureValue::addFeatureValueImport($feature_id, $address, $this->context->language->id, true);
        Product::addFeatureProductImport($this->_product->id, $feature_id, $feature_value_id);

        return true;
    }

    private function getCombinations() {
        $result = array();
        foreach($this->_product->getAttributeCombinations($this->context->language->id) as $key => $comb) {
            $id = $comb['id_product_attribute'];
            $group = $comb['id_attribute_group'];

            if ($group == Configuration::get('NPS_ATTRIBUTE_DATE_ID'))
                $result[$id]['date'] = $comb['attribute_name'];
            else if ($group == Configuration::get('NPS_ATTRIBUTE_TIME_ID'))
                $result[$id]['time'] = $comb['attribute_name'];
            else if ($group == Configuration::get('NPS_ATTRIBUTE_TYPE_ID'))
                $result[$id]['type'] = $comb['attribute_name'];
            else if ($group == Configuration::get('NPS_ATTRIBUTE_NAME_ID')) {
                $result[$id]['id_product_attribute'] = $id;
                $result[$id]['name'] = $comb['attribute_name'];
                $result[$id]['price'] = round($comb['price'], 2);
                $result[$id]['quantity'] = $comb['quantity'];
                $result[$id]['default'] = $comb['default_on'];
                $query = ProductAttributeExpiryDate::getByProductAttribute($id);
                if ($query) {
                    $date_time = new DateTime($query);
                    $result[$id]['expiry_date'] = $date_time->format('Y-m-d H:i');
                }
            }
            $sprices = SpecificPrice::getByProductId($this->_product->id, $id);
            $sp = array();
            if ($sprices) {
                foreach ($sprices as $key => $value) {
                    $value['reduction'] = round($value['reduction'], 2);
                    $value['from'] = date_format(date_create($value['from']), 'Y-m-d H:i');
                    $value['to'] = date_format(date_create($value['to']), 'Y-m-d H:i');
                    $sp[] = $value;
                }
            }
            $result[$id]['specific_prices'] = $sp;
        }
        foreach ($result as $key => $value) {
            if (isset($value['date']) && isset($value['time'])) {
                $date_time = new DateTime($value['date'].' '.$value['time']);
                $result[$key]['date'] = $date_time->format('Y-m-d H:i');
            }
        }
        return $result;
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
        foreach ($files as $save_path => $file) {
            syslog(LOG_DEBUG, 'Save product: '.$this->_product->id.' Adding image, path: '.$save_path.' file info: '.implode(' | ', $file));
            $image = new Image();
            $image -> id_product = (int)($this -> _product -> id);
            $image -> position = Image::getHighestPosition($this -> _product -> id) + 1;

            if (!Image::getCover($image -> id_product))
                $image -> cover = 1;
            else
                $image -> cover = 0;

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                $this->onSaveImageFail($image, $save_path);
                continue;
            }

            if (!$image -> add()) {
                $this->errors[] = $this->module->l('Error while creating image. Try to upload image again.', 'Product');
                syslog(LOG_ERR, 'Save product: '.$this->_product->id.' Error while creating image in database');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $this -> errors[] = $this->module->l('Error while saving image. Try to upload image again.', 'Product');
                    syslog(LOG_ERR, 'Save product: '.$this->_product->id.' An error occurred during new folder creation');
                    $this->onSaveImageFail($image, $save_path);
                    continue;
                }

                $error = 0;

                if (!ImageManager::resize(_PS_UPLOAD_DIR_.$save_path, $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST :
                            $this->errors[] = $this->module->l('Error while saving image. Try to upload image again.', 'Product');
                            syslog(LOG_ERR, 'Save product: '.$this->_product->id.' An error occurred while copying image, the file does not exist anymore.');
                            break;

                        case ImageManager::ERROR_FILE_WIDTH :
                            $this->errors[] = $this->module->l('Error while saving image. Try to upload image again.', 'Product');
                            syslog(LOG_ERR, 'Save product: '.$this->_product->id.' An error occurred while copying image, the file width is 0px.');
                            break;

                        case ImageManager::ERROR_MEMORY_LIMIT :
                            $this->errors[] = $this->module->l('Error while saving image. Try to upload image again.', 'Product');
                            syslog(LOG_ERR, 'Save product: '.$this->_product->id.' An error occurred while copying image, check your memory limit.');
                            break;

                        default :
                            $this->errors[] = $this->module->l('Error while saving image. Try to upload image again.', 'Product');
                            syslog(LOG_ERR, 'Save product: '.$this->_product->id.' An error occurred while copying image.');
                            break;
                    }
                    $this->onSaveImageFail($image, $save_path);
                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize(_PS_UPLOAD_DIR_.$save_path, $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                            syslog(LOG_ERR, 'Save product: '.$this->_product->id.' An error occurred while copying image:') . ' ' . stripslashes($imageType['name']);
                            $this->onSaveImageFail($image, $save_path);
                            continue;
                        }
                    }
                }

                Hook::exec('actionWatermark', array('id_image' => $image -> id, 'id_product' => $this -> _product -> id));

                if (!$image -> update()) {
                    syslog(LOG_ERR, 'Save product: '.$this->_product->id.' Error while updating database status.');
                    $this->errors[] = $this->module->l('Error while saving image. Try to upload image again.', 'Product');
                    $this->onSaveImageFail($image, $save_path);
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

                @unlink(_PS_UPLOAD_DIR_.$save_path);
                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int)$this -> _product -> id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$this -> _product -> id . '_' . $this -> context -> shop -> id . '.jpg');
            }
        }
    }

    private function onSaveImageFail(Image $image, $file_path) {
        $image->delete();
        @unlink(_PS_UPLOAD_DIR_.$file_path);
    }
}
?>
