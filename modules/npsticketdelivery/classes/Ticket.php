<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class Ticket extends ObjectModel {

    public $id_cart_ticket;
    public $id_seller;
    public $name;
    public $price;
    public $date;
    public $address;
    public $town;
    public $district;
    public $generated;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ticket',
        'primary' => 'id_ticket',
        'fields' => array(
            'id_cart_ticket' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'id_seller' =>      array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'price' =>          array('type' => self::TYPE_FLOAT,  'validate' => 'isPrice',       'required' => true),
            'name' =>           array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'address' =>        array('type' => self::TYPE_STRING, 'required' => true),
            'town' =>           array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'district' =>       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date' =>           array('type' => self::TYPE_DATE,   'validate' => 'isDateFormat'),
            'generated' =>      array('type' => self::TYPE_DATE,   'validate' => 'isDateFormat'),
        ),
    );
}

