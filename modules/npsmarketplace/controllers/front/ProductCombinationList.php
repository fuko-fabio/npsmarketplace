<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceProductCombinationListModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryPlugin('footable');
        $this->addJqueryPlugin('footable-sort');
        $this->addJqueryPlugin('scrollTo');
        $this->addJS(_PS_JS_DIR_.'validate.js');
        $this->addJS(_PS_MODULE_DIR_.'npsmarketplace/js/product_list.js');
        $this->addCSS(_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.css');
    }

    public function postProcess() {
        if (Tools::isSubmit('action') && Tools::isSubmit('id_product_attribute') && Tools::isSubmit('id_product')) {
            if (Tools::getValue('action') == 'delete') {
                $id_product = (int)Tools::getValue('id_product', 0);
                $product = new Product($id_product);
                $id_product_attribute = (int)Tools::getValue('id_product_attribute');
                $product->deleteAttributeCombination((int)$id_product_attribute);
                $product->checkDefaultAttributes();
                if (!$product->hasAttributes()) {
                    $product->cache_default_attribute = 0;
                    $product->update();
                    if($product->active)
                        $product->toggleStatus();
                    Search::indexation(false, $product->id);
                    Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
                }
                else
                    Product::updateDefaultAttribute($product->id);
                Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductCombinationList&id_product='.$id_product);
            }
        }
    }

    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

        $comb_array = array();
        $id_product = (int)Tools::getValue('id_product', 0);
        $product = new Product($id_product);
        if ($product->id) { 
            /* Build attributes combinations */
            $combinations = $product->getAttributeCombinations($this->context->language->id);
            $groups = array();
            $currency = $this->context->currency;
            if (is_array($combinations))
            {
                $combination_images = $product->getCombinationImages($this->context->language->id);
                foreach ($combinations as $k => $combination)
                {
                    $price_to_convert = Tools::convertPrice($combination['price'], $currency);
                    $price = Tools::displayPrice($price_to_convert, $currency);

                    $comb_array[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                    $comb_array[$combination['id_product_attribute']]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute']);
                    $comb_array[$combination['id_product_attribute']]['wholesale_price'] = $combination['wholesale_price'];
                    $comb_array[$combination['id_product_attribute']]['price'] = $price;
                    $comb_array[$combination['id_product_attribute']]['weight'] = $combination['weight'].Configuration::get('PS_WEIGHT_UNIT');
                    $comb_array[$combination['id_product_attribute']]['unit_impact'] = $combination['unit_price_impact'];
                    $comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                    $comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
                    $comb_array[$combination['id_product_attribute']]['upc'] = $combination['upc'];
                    $comb_array[$combination['id_product_attribute']]['id_image'] = isset($combination_images[$combination['id_product_attribute']][0]['id_image']) ? $combination_images[$combination['id_product_attribute']][0]['id_image'] : 0;
                    $comb_array[$combination['id_product_attribute']]['available_date'] = strftime($combination['available_date']);
                    $comb_array[$combination['id_product_attribute']]['default_on'] = $combination['default_on'];
                    if ($combination['is_color_group'])
                        $groups[$combination['id_attribute_group']] = $combination['group_name'];
                }
            }

            $irow = 0;
            if (isset($comb_array))
            {
                foreach ($comb_array as $id_product_attribute => $product_attribute)
                {
                    /* In order to keep the same attributes order */
                    asort($product_attribute['attributes']);

                    $comb_array[$id_product_attribute]['image'] = $product_attribute['id_image'] ? new Image($product_attribute['id_image']) : false;
                    $comb_array[$id_product_attribute]['available_date'] = $product_attribute['available_date'] != 0 ? date('Y-m-d', strtotime($product_attribute['available_date'])) : '0000-00-00';
                    $comb_array[$id_product_attribute]['attributes'] = $product_attribute['attributes'];
                    $comb_array[$id_product_attribute]['quantity'] = StockAvailable::getQuantityAvailableByProduct($product->id, $id_product_attribute, $this->context->shop->id);
                    $comb_array[$id_product_attribute]['delete_url'] = $this->context->link->getModuleLink('npsmarketplace',
                                                                                                           'ProductCombinationList',
                                                                                                           array(
                                                                                                                'id_product' => $product->id,
                                                                                                                'id_product_attribute' => $id_product_attribute,
                                                                                                                'action' => 'delete'
                                                                                                                ));

                    if ($product_attribute['default_on'])
                        $comb_array[$id_product_attribute]['class'] = 'highlighted';
                }
            }
        }
        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'comb_array' => $comb_array,
            'name' => $product->name,
            'nps_ajax_url' => $this->context->link->getModuleLink('npsmarketplace', 'Ajax'),
            'new_combination_url' => $this->context->link->getModuleLink('npsmarketplace', 'ProductCombination', array('id_product' => $product->id)),
        ));
        $this->setTemplate('product_combinations_list.tpl');
    }
}
?>