<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24DispatchHistoryDetail extends ObjectModel {

    public $id_p24_dispatch_history;
    public $id_seller;
    public $session_id;
    public $spid;
    public $amount;
    public $status;
    public $error;
    public $merchant;

    public function __construct($id_p24_dispatch_history_detail = null, $id_p24_dispatch_history = null) {
        if (empty($id_p24_dispatch_history_detail) && !empty($id_p24_dispatch_history)) {
            $query = new DbQuery();
            $query
                -> select('`id_p24_dispatch_history_detail`')
                -> from('p24_dispatch_history_detail')
                -> where('`id_p24_dispatch_history` = '.$id_p24_dispatch_history);
            $id_p24_dispatch_history_detail = Db::getInstance() -> getValue($query);
        }
        parent::__construct($id_p24_dispatch_history_detail);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_dispatch_history_detail',
        'primary' => 'id_p24_dispatch_history_detail',
        'fields' => array(
            'id_p24_dispatch_history' =>   array('type' => self::TYPE_INT,  'required' => true, 'validate' => 'isUnsignedId'),
            'id_seller' =>  array('type' => self::TYPE_INT),
            'session_id' => array('type' => self::TYPE_STRING, 'required' => true),
            'spid' =>       array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'amount' =>     array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'status' =>     array('type' => self::TYPE_BOOL,   'required' => true),
            'merchant' =>   array('type' => self::TYPE_BOOL,   'required' => true),
            'error' =>      array('type' => self::TYPE_STRING),
        ),
    );
}