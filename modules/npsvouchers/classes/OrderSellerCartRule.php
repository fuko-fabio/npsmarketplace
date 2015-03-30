<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*/

class OrderSellerCartRule extends ObjectModel {

    public $id_seller;
    public $id_order_cart_rule;
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_seller_cart_rule',
        'primary' => 'id_order_seller_cart_rule',
        'multilang' => false,
        'fields' => array(
            'id_seller' =>          array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' =>         array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_cart_rule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        ),
    );
}

