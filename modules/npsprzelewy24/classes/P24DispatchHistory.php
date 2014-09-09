<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24DispatchHistory extends ObjectModel {

    public $id_payment;
    public $order_id;
    public $session_id;
    public $spid;
    public $amount;
    public $status;
    public $error;
    public $date;
    public $merchant;

    public function __construct($id_dispatch_history = null, $order_id = null) {
        if (empty($id_dispatch_history) && !empty($order_id)) {
            $query = new DbQuery();
            $query
                -> select('`id_dispatch_history`')
                -> from('p24_dispatch_history')
                -> where('`order_id` = '.$order_id);
            $id_dispatch_history = Db::getInstance() -> getValue($query);
        }
        parent::__construct($id_dispatch_history);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_dispatch_history',
        'primary' => 'id_dispatch_history',
        'fields' => array(
            'id_payment' =>   array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'order_id' =>   array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'session_id' => array('type' => self::TYPE_STRING, 'required' => true),
            'spid' =>       array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'amount' =>     array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'status' =>     array('type' => self::TYPE_BOOL,   'required' => true),
            'merchant' =>   array('type' => self::TYPE_BOOL,   'required' => true),
            'date' =>       array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isDateFormat'),
            'error' =>      array('type' => self::TYPE_STRING),
        ),
    );
}