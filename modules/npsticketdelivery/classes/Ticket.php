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
    public $tax;
    public $date;
    public $address;
    public $town;
    public $type;
    public $entries;
    public $district;
    public $person;
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
            'tax' =>            array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId'),
            'type' =>           array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId'),
            'entries' =>        array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId'),
            'name' =>           array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'address' =>        array('type' => self::TYPE_STRING, 'required' => true),
            'town' =>           array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'district' =>       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'person' =>         array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date' =>           array('type' => self::TYPE_DATE,   'validate' => 'isDateFormat'),
            'from' =>           array('type' => self::TYPE_DATE,   'validate' => 'isDateFormat'),
            'to' =>             array('type' => self::TYPE_DATE,   'validate' => 'isDateFormat'),
            'generated' =>      array('type' => self::TYPE_DATE,   'validate' => 'isDateFormat'),
        ),
    );
    
    public static function getForCustomer($id_customer, $id_ticket) {
        if (!isset($id_ticket) || !isset($id_customer))
            return null;
        $dbquery = new DbQuery();
        $dbquery->select('*');
        $dbquery->from('cart_ticket', 'c');
        $dbquery->leftJoin('ticket', 't', 't.id_cart_ticket = c.id_cart_ticket');
        $dbquery->where('c.`id_customer` = '.$id_customer.' AND t.`id_ticket` = '.$id_ticket);

        return Db::getInstance()->getRow($dbquery);
    }
}

