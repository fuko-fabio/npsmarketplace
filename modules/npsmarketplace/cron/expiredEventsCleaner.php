<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductAttributeExpiryDate.php');

$token = Tools::getValue('token');

if ($token != '733acb9920b35800545d7d3e9c2e9e21')
    exit(1);

$sql = 'SELECT * FROM `'._DB_PREFIX_.'product_attribute_expiry_date` WHERE `expiry_date` < NOW()';

$rows = Db::getInstance()->executeS($sql);
$outdated_ids = array();
foreach ($rows as $row) {
    $outdated_ids[] = (int)$row['id_product_attribute'];
}

$outdated_ids = array_unique($outdated_ids);
$products_ids = array();
foreach ($outdated_ids as $id) {
    $combination = new Combination($id);
    $products_ids[] = $combination->id_product;
    $combination->delete();
    ProductAttributeExpiryDate::deleteByProductAttribute($id);
    Search::indexation(false, $combination->id_product);
}

$products_ids = array_unique($products_ids);
foreach ($products_ids as $id) {
    $attrs_ids = Product::getProductAttributesIds($id);
    if(empty($attrs_ids)) {
        $product = new Product($id);
        if($product->active) {
            $default_product = new Product((int)$id, false, null, (int)$product->id_shop_default);
            $default_product->toggleStatus();
        }
    }
}
exit(0);
