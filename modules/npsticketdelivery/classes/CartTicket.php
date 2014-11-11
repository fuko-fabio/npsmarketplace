<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class CartTicket extends ObjectModel {

    public $id_customer;
    public $id_cart;
    public $email;
    public $gift;

    public function __construct($id_cart_ticket = null, $id_cart = null) {
        if (empty($id_cart_ticket) && !empty($id_cart))
        {
            $query = new DbQuery();
            $query->select('id_cart_ticket')->from('cart_ticket')->where('`id_cart` = '.$id_cart);
            if ($result = Db::getInstance()->getValue($query))
                $id_cart_ticket = $result;
        }
        parent::__construct($id_cart_ticket);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cart_ticket',
        'primary' => 'id_cart_ticket',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'id_cart' =>     array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'email' =>       array('type' => self::TYPE_STRING, 'validate' => 'isEmail',       'required' => true),
            'gift' =>        array('type' => self::TYPE_BOOL,   'validate' => 'isBool',        'required' => true),
        ),
    );

    public static function getByCartId($id_cart) {
        $query = new DbQuery();
        $query->select('id_cart_ticket')->from('cart_ticket')->where('`id_cart` = '.$id_cart);
        if ($result = Db::getInstance()->getValue($query))
            return new CartTicket($result);
        return null;
    }

    public static function getAllTickets($id_cart_ticket) {
        if (!isset($id_cart_ticket))
            return null;
        $dbquery = new DbQuery();
        $dbquery->select('*');
        $dbquery->from('cart_ticket', 'c');
        $dbquery->leftJoin('ticket', 't', 't.id_cart_ticket = c.id_cart_ticket');
        $dbquery->where('c.`id_cart_ticket` = '.$id_cart_ticket);
        
        return Db::getInstance()->executeS($dbquery);
    }

    public static function getCustomerTickets($id_customer) {
        if ( !isset($id_customer))
            return null;
        $dbquery = new DbQuery();
        $dbquery->select('*');
        $dbquery->from('cart_ticket', 'c');
        $dbquery->leftJoin('ticket', 't', 't.id_cart_ticket = c.id_cart_ticket');
        $dbquery->where('c.`id_customer` = '.$id_customer);
        
        return Db::getInstance()->executeS($dbquery);
    }    
}

