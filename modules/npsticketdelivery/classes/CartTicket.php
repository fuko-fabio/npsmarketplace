<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class CartTicket extends ObjectModel {

    public $id_cart;
    public $id_customer;
    public $id_currency;
    public $email;
    public $persons;
    public $answers;

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
            'id_cart' =>     array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'id_customer' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'id_currency' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'email' =>       array('type' => self::TYPE_STRING, 'required' => true),
            'persons' =>     array('type' => self::TYPE_STRING),
            'answers' =>     array('type' => self::TYPE_STRING),
        ),
    );

    public static function getByCartId($id_cart) {
        if ($id_cart == null)
            return null;
        $query = new DbQuery();
        $query->select('id_cart_ticket')->from('cart_ticket')->where('`id_cart` = '.$id_cart);
        if ($result = Db::getInstance()->getValue($query))
            return new CartTicket($result);
        return null;
    }

    public static function getAllTicketsByCartId($id_cart, $id_seller = null) {
        if (!isset($id_cart))
            return null;

        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('cart_ticket', 'ct')
            ->leftJoin('ticket', 't', 't.id_cart_ticket = ct.id_cart_ticket')
            ->leftJoin('cart', 'c', 'ct.id_cart = c.id_cart')
            ->orderBy('generated DESC');
        if ($id_seller == null)
            $dbquery->where('c.`id_cart` = '.$id_cart.' AND (id_ticket <> "")');
        else
            $dbquery->where('c.`id_cart` = '.$id_cart.' AND t.`id_seller` = '.$id_seller.' AND (id_ticket <> "")');

        return Db::getInstance()->executeS($dbquery);
    }

    public static function getAllTicketsBySellerId($id_seller, $p, $n, $count = false) {
        if (!isset($id_seller))
            return null;

        if ($count)
            return Db::getInstance()->getValue('SELECT count(name) FROM '._DB_PREFIX_.'ticket WHERE id_seller = '.$id_seller);

        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('cart_ticket', 'ct')
            ->leftJoin('ticket', 't', 't.id_cart_ticket = ct.id_cart_ticket')
            ->leftJoin('cart', 'c', 'ct.id_cart = c.id_cart')
            ->where('t.`id_seller` = '.$id_seller.' AND (id_ticket <> "")')
            ->orderBy('generated DESC');
        if ($n > 0)
            $dbquery->limit($n, (((int)$p - 1) * (int)$n));
        return Db::getInstance()->executeS($dbquery);
    }

    public static function getAllTickets($id_cart_ticket) {
        if (!isset($id_cart_ticket))
            return null;

        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('cart_ticket', 'ct')
            ->leftJoin('ticket', 't', 't.id_cart_ticket = ct.id_cart_ticket')
            ->leftJoin('cart', 'c', 'ct.id_cart = c.id_cart')
            ->where('ct.`id_cart_ticket` = '.$id_cart_ticket.' AND (id_ticket <> "")')
            ->orderBy('generated DESC');

        return Db::getInstance()->executeS($dbquery);
    }

    public static function getCustomerTickets($id_customer, $p, $n, $count = false) {
        if ( !isset($id_customer))
            return null;
        if ($count)
            return Db::getInstance()->getValue('SELECT count(t.`name`) FROM `'._DB_PREFIX_.'ticket` t
            LEFT JOIN `'._DB_PREFIX_.'cart_ticket` ct ON (t.`id_cart_ticket` = ct.`id_cart_ticket`)
            WHERE ct.`id_customer` = '.$id_customer);

        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('cart_ticket', 'ct')
            ->leftJoin('ticket', 't', 't.id_cart_ticket = ct.id_cart_ticket')
            ->leftJoin('cart', 'c', 'ct.id_cart = c.id_cart')
            ->where('ct.`id_customer` = '.$id_customer.' AND (id_ticket <> "")')
            ->orderBy('generated DESC');
        if ($n > 0)
            $dbquery->limit($n, (((int)$p - 1) * (int)$n));
        return Db::getInstance()->executeS($dbquery);
    }
}

