<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24DispatchHistory extends ObjectModel {

    public $id_payment;
    public $date;
    public $sellers_amount;
    public $sellers_number;
    public $merchant_amount;
    public $p24_amount;
    public $total_amount;
    public $status;
    public $error;

    public function __construct($id_dispatch_history = null, $id_payment = null) {
        if (empty($id_dispatch_history) && !empty($id_payment)) {
            $query = new DbQuery();
            $query
                -> select('`id_p24_dispatch_history`')
                -> from('p24_dispatch_history')
                -> where('`id_payment` = '.$id_payment);
            $id_dispatch_history = Db::getInstance() -> getValue($query);
        }
        parent::__construct($id_dispatch_history);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_dispatch_history',
        'primary' => 'id_p24_dispatch_history',
        'fields' => array(
            'id_payment' =>      array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'date' =>            array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isDateFormat'),
            'sellers_amount' =>  array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'sellers_number' =>  array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'merchant_amount' => array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'p24_amount' =>      array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'total_amount' =>    array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'status' =>          array('type' => self::TYPE_BOOL,   'required' => true),
            'error' =>           array('type' => self::TYPE_STRING),
        ),
    );

    public function getDetails() {
        return Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'p24_dispatch_history_detail`
            WHERE `id_p24_dispatch_history` = '.$this->id);
    }
}