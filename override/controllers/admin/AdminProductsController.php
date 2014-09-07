<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace//npsmarketplace.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24SellerCompany.php');

class AdminProductsController extends AdminProductsControllerCore {

    public function initFormAttributes($product) {
        $data = $this -> createTemplate($this -> tpl_form);
        if (!Combination::isFeatureActive())
            $this -> displayWarning($this -> l('This feature has been disabled. ') . ' <a href="index.php?tab=AdminPerformance&token=' . Tools::getAdminTokenLite('AdminPerformance') . '#featuresDetachables">' . $this -> l('Performances') . '</a>');
        else if (Validate::isLoadedObject($product)) {
            if ($this -> product_exists_in_shop) {
                $attribute_js = array();
                $attributes = Attribute::getAttributes($this -> context -> language -> id, true);
                foreach ($attributes as $k => $attribute)
                    $attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
                $currency = $this -> context -> currency;
                $data -> assign('attributeJs', $attribute_js);
                $data -> assign('attributes_groups', AttributeGroup::getAttributesGroups($this -> context -> language -> id));

                $data -> assign('currency', $currency);

                $images = Image::getImages($this -> context -> language -> id, $product -> id);

                $data -> assign('tax_exclude_option', Tax::excludeTaxeOption());
                $data -> assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));

                $data -> assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
                $data -> assign('field_value_unity', $this -> getFieldValue($product, 'unity'));

                $data -> assign('reasons', $reasons = StockMvtReason::getStockMvtReasons($this -> context -> language -> id));
                $data -> assign('ps_stock_mvt_reason_default', $ps_stock_mvt_reason_default = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT'));
                $data -> assign('minimal_quantity', $this -> getFieldValue($product, 'minimal_quantity') ? $this -> getFieldValue($product, 'minimal_quantity') : 1);
                $data -> assign('available_date', ($this -> getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities($this -> getFieldValue($product, 'available_date'), $this -> context -> language -> id)) : '0000-00-00');

                $i = 0;
                $type = ImageType::getByNameNType('%', 'products', 'height');
                if (isset($type['name']))
                    $data -> assign('imageType', $type['name']);
                else
                    $data -> assign('imageType', 'small_default');
                $data -> assign('imageWidth', (isset($image_type['width']) ? (int)($image_type['width']) : 64) + 25);
                foreach ($images as $k => $image) {
                    $images[$k]['obj'] = new Image($image['id_image']);
                    ++$i;
                }
                $data -> assign('images', $images);

                $data -> assign($this -> tpl_form_vars);
                $data -> assign(array('list' => $this -> renderListAttributes($product, $currency), 'product' => $product, 'id_category' => $product -> getDefaultCategory(), 'token_generator' => Tools::getAdminTokenLite('AdminAttributeGenerator'), 'combination_exists' => (Shop::isFeatureActive() && (Shop::getContextShopGroup() -> share_stock) && count(AttributeGroup::getAttributesGroups($this -> context -> language -> id)) > 0 && $product -> hasAttributes())));
            } else
                $this -> displayWarning($this -> l('You must save the product in this shop before adding combinations.'));
        } else {
            $data -> assign('product', $product);
            $this -> displayWarning($this -> l('You must save this product before adding combinations.'));
        }

        $this -> tpl_form_vars['custom_form'] = $data -> fetch();
    }

    public function processStatus() {
        $this->loadObject(true);
        if (!Validate::isLoadedObject($this->object))
            return false;
        if (($error = $this->object->validateFields(false, true)) !== true)
            $this->errors[] = $error;
        if (($error = $this->object->validateFieldsLang(false, true)) !== true)
            $this->errors[] = $error;

        if (!$this->object->active) {
            $nps_instance = new NpsMarketplace();
            $seller = new Seller(Seller::getSellerByProduct($this->object->id));
            if(!$seller->active)
                $this->errors[] = $nps_instance->l('Seller of this product has not activated account');
            if($seller->locked)
                $this->errors[] = $nps_instance->l('Seller of this product has locked account');

            $payment_config = new P24SellerCompany(null, $seller->id);
            if ($payment_config->id == null)
                $this->errors[] = $nps_instance->l('Seller of this product has not configured payment account');
        }

        if (count($this->errors))
            return false;

        $res = parent::processStatus();
        Hook::exec('actionProductUpdate', array('product' => $this->object));
        
        return $res;
    }

}
