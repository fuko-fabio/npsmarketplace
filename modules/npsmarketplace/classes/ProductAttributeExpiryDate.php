<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class ProductAttributeExpiryDate extends ObjectModel {

    public $id_product;
    public $id_product_attribute;
    public $expiry_date;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_attribute_expiry_date',
        'primary' => 'id_expiry',
        'fields' => array(
            'id_product' =>            array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId'),
            'id_product_attribute' =>  array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId'),
            'expiry_date' =>           array('type' => self::TYPE_STRING, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public static function deleteByProductId($id_product) {
        return Db::getInstance()->delete('product_attribute_expiry_date', 'id_product = '.(int)$id_product);
    }

    public static function deleteByProductAttribute($id_product_attribute) {
        return Db::getInstance()->delete('product_attribute_expiry_date', 'id_product_attribute = '.(int)$id_product_attribute);
    }
}

