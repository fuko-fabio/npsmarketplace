<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductAttributeExpiryDate.php');

$token = Tools::getValue('token');
syslog(LOG_INFO, 'Cleaning expired events...');

if ($token != '733acb9920b35800545d7d3e9c2e9e21') {
    syslog(LOG_ERR, 'Invalid token!');
    exit(1);
}

$sql = 'SELECT * FROM `'._DB_PREFIX_.'product_attribute_expiry_date` WHERE `expiry_date` < NOW()';

$rows = Db::getInstance()->executeS($sql);
$outdated_ids = array();
$products_ids = array();
foreach ($rows as $row) {
    $attr_id = $row['id_product_attribute'];
    $p_id = $row['id_product'];
    if (isset($attr_id) && !empty($attr_id)) {
        $outdated_ids[] = (int)$attr_id;
        continue;
    }
    if (isset($p_id) && !empty($p_id))
        $products_ids[] = (int)$p_id;
}

$outdated_ids = array_unique($outdated_ids);
syslog(LOG_INFO, 'Outdated product attributes ids: '.implode('|', $outdated_ids));
foreach ($outdated_ids as $id) {
    $combination = new Combination($id);
    $products_ids[] = $combination->id_product;
    $prod = new Product($combination->id_product);
    $combination->delete();
    ProductAttributeExpiryDate::deleteByProductAttribute($id);
    $prod->checkDefaultAttributes();
    Search::indexation(false, $prod->id);
}

$products_ids = array_unique($products_ids);
syslog(LOG_INFO, 'Outdated products ids: '.implode('|', $products_ids));
$to_disable = array();
foreach ($products_ids as $id) {
    $extras = Product::getExtras($id);
    if ($extras['type'] == 0) {
        $attrs_ids = Product::getProductAttributesIds($id);
        if(empty($attrs_ids))
            $to_disable[] = $id;
    } else 
        $to_disable[] = $id;
}
syslog(LOG_INFO, 'Disabling products: '.implode('|', $to_disable));
foreach ($to_disable as $id) {
    $product = new Product($id);
    if($product->active) {
        $default_product = new Product((int)$id, false, null, (int)$product->id_shop_default);
        $default_product->toggleStatus();
    }
}
exit(0);
