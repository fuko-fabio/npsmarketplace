<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24DispatchHistory extends ObjectModel {
    public $id_payment;
    public $order_id;
    public $date;
    public $merchan_amount;
    public $sellers_amount;

    public function __construct($id_dispatch_history = null, $id_payment = null) {
        if (empty($id_dispatch_history) && !empty($id_payment)) {
            $query = new DbQuery();
            $query
                -> select('`id_dispatch_history`')
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
        'primary' => 'id_dispatch_history',
        'fields' => array(
            'id_payment' =>     array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'date' =>           array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isDateFormat'),
            'merchan_amount' => array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'sellers_amount' => array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
        ),
    );
}