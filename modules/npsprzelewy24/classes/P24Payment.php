<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24Payment extends ObjectModel
{
    public $id;
    public $session_id;
    public $id_cart;
    public $amount;
    public $currency_iso;
    public $timestamp;

    public function __construct($id_payment = null, $id_cart = null) {
        if (empty($id_payment) && !empty($id_cart)) {
            $query = new DbQuery();
            $query
                -> select('`id_payment`')
                -> from('p24_payment')
                -> where('`id_cart` = '.$id_cart);
            $id_payment = Db::getInstance() -> getValue($query);
        }
        parent::__construct($id_payment);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_payment',
        'primary' => 'id_payment',
        'fields' => array(
            'session_id' =>   array('type' => self::TYPE_STRING, 'required' => true, 'size' => 100),
            'id_cart' =>      array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'amount' =>       array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'currency_iso' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isLanguageIsoCode', 'size' => 3),
            'timestamp' =>         array('type' => self::TYPE_INT,    'required' => true),
        ),
    );

    /**
     * getBySessionId Gets P24Payment by session ID
     *
     * @return P24Payment object
     */
    public static function getBySessionId($session_id) {
        $id = Db::getInstance()->getValue('
            SELECT `id_payment`
            FROM `'._DB_PREFIX_.'p24_payment`
            WHERE `session_id` = "'.$session_id.'"');
        return new P24Payment($id);
    }

    /**
     * getByCartId Gets payment summary by cart ID
     *
     * @return 
     */
    public static function getSummaryByCartId($id_cart) {
        $result = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'p24_payment`, `'._DB_PREFIX_.'p24_payment_statement`
            WHERE `'._DB_PREFIX_.'p24_payment`.id_payment = `'._DB_PREFIX_.'p24_payment_statement`.id_payment
            AND `id_cart` = "'.$id_cart.'"');
       return $result != null ? $result[0] : null;
    }
}