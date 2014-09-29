<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductAttributeExpiryDate.php');

class Product extends ProductCore
{
    public function delete()
    {
        if(parent::delete())
            return $this->deleteSellersAssociations();
        else
            return false;
    }

    public function deleteSellersAssociations() {
        return Db::getInstance()->delete('seller_product', 'id_product = '.(int)$this->id);
    }

    public function newEventCombination($date, $time, $quantity, $expiry_date, $id_shop = null) {
        $d = array();
        $t = array();
        foreach (Language::getLanguages() as $key => $lang) {
            $d[$lang['id_lang']] = $date;
            $t[$lang['id_lang']] = $time;
        }
        $lang_id = (int)Configuration::get('PS_LANG_DEFAULT');
        $date_attr_group_id = Configuration::get('NPS_ATTRIBUTE_DATE_ID');
        $time_attr_group_id = Configuration::get('NPS_ATTRIBUTE_TIME_ID');

        $res = $this->getAttributeId($date_attr_group_id, $date, $lang_id);
        if(!$res)
            $date_attr_id = null;
        else
            $date_attr_id = $res['id_attribute'];
        $date_attr = new Attribute($date_attr_id);
        if ($date_attr_id == null) {
            $date_attr->name = $d;
            $date_attr->id_attribute_group = $date_attr_group_id;
            $date_attr->position = -1;
            $date_attr->save();
        }

        $res = $this->getAttributeId($time_attr_group_id, $time, $lang_id);
        if(!$res)
            $time_attr_id = null;
        else
            $time_attr_id = $res['id_attribute'];
        $time_attr = new Attribute($time_attr_id);
        if ($time_attr_id == null) {
            $time_attr->name = $t;
            $time_attr->id_attribute_group = $time_attr_group_id;
            $time_attr->position = -1;
            $time_attr->save();
        }

        $id_product_attribute = $this->addAttribute(
            0,//$price,
            null,//$weight,
            null,//$unit_impact,
            null,//$ecotax,
            null,//$id_images,
            null,//$reference,
            null,//$ean13,
            true,//$default
            null,//$location
            null,//$upc 
            1,//$minimal_quantity
            array(),//$id_shop_list
            null);//$available_date

        $combination = new Combination((int)$id_product_attribute);
        $combination->setAttributes(array($date_attr->id, $time_attr->id));
        StockAvailable::setQuantity((int)$this->id, (int)$id_product_attribute, $quantity, $id_shop);
        $this->saveExpiryDate($id_product_attribute, $expiry_date);
        Search::indexation(false, $this->id);
    }

    private function saveExpiryDate($id_product_attribute, $expiry_date) {
        $e_d = new ProductAttributeExpiryDate();
        $e_d->expiry_date = $expiry_date;
        $e_d->id_product_attribute = $id_product_attribute;
        $e_d->save();
    }

    private function getAttributeId($id_attribute_group, $name, $id_lang) {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'attribute_group` ag
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
            LEFT JOIN `'._DB_PREFIX_.'attribute` a
                ON a.`id_attribute_group` = ag.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
                ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
            '.Shop::addSqlAssociation('attribute_group', 'ag').'
            '.Shop::addSqlAssociation('attribute', 'a').'
            WHERE al.`name` = \''.pSQL($name).'\' AND ag.`id_attribute_group` = '.(int)$id_attribute_group.'
            ORDER BY agl.`name` ASC, a.`position` ASC
        ');

        return $result ? $result : null;
    }
}
?>