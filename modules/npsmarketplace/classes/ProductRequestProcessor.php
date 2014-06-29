<?php
/*
 *  @author Norbert Pabian
 *  @copyright
 *  @license
 */

class ProductRequestProcessor {

    public $errors = array();

    public function processAdd() {
        $product_name = trim(Tools::getValue('product_name'));
        $product_short_description = trim(Tools::getValue('product_short_description'));
        $product_description = trim(Tools::getValue('product_description'));
        $product_price = trim(Tools::getValue('product_price'));
        $product_amount = trim(Tools::getValue('product_amount'));
        $product_date = trim(Tools::getValue('product_date'));
        $product_time = trim(Tools::getValue('product_time'));
        $product_code = trim(Tools::getValue('product_code'));

        if (!Validate::isGenericName($product_name))
            $this -> errors[] = Tools::displayError('Invalid product name');
        else if (!Validate::isMessage($product_short_description))
            $this -> errors[] = Tools::displayError('Invalid product short description');
        else if (!Validate::isMessage($product_description))
            $this -> errors[] = Tools::displayError('Invalid product description');
        else if (!Validate::isPhoneNumber($product_price))
            $this -> errors[] = Tools::displayError('Invalid product price');
        else if (!Validate::isInt($product_amount))
            $this -> errors[] = Tools::displayError('Invalid product amount number');
        else if (!Validate::isDateFormat($product_date))
            $this -> errors[] = Tools::displayError('Invalid product date');
        else if (!Validate::isTime($product_time))
            $this -> errors[] = Tools::displayError('Invalid product time');
        else if (!Validate::isMessage($product_code))
            $this -> errors[] = Tools::displayError('Invalid product code');

        $product = new Product();
        $product -> price = $product_price;
        $product -> name = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $product_name);
        $product -> quantity = $product_amount;
        $product -> active = 0;
        $product -> description = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $product_description);
        $product -> description_short = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $product_short_description);
        $product -> available_date = $product_date;
        $product -> link_rewrite = array((int)(Configuration::get('PS_LANG_DEFAULT')) => Tools::link_rewrite($product_name));
        $product -> is_virtual = true;
        $product -> indexed = 1;
        $product -> id_tax_rules_group = 0;
        $product -> reference = $product_code;

        if (empty($this -> errors)) {
            if (!$product->save())
                $this->errors[] = Tools::displayError('An error occurred while saving product.');
            else 
                if (!$product->addToCategories($_POST['category']))
                    $this->errors[] = Tools::displayError('An error occurred while adding product to categories.');
                else
                    $this->saveProductImages($product);
        }
        return $product;
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