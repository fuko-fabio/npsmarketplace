<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24PaymentStatement extends ObjectModel
{
    public $id_payment;
    public $order_id;
    public $payment_method;
    public $statement;

    public function __construct($id_payment_statement = null, $id_payment = null) {
        if (empty($id_payment_statement) && !empty($id_payment)) {
            $query = new DbQuery();
            $query
                -> select('`id_payment_statement`')
                -> from('p24_payment_statement')
                -> where('`id_payment` = '.$id_payment);
            $id_payment_statement = Db::getInstance() -> getValue($query);
        }
        parent::__construct($id_payment_statement);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_payment_statement',
        'primary' => 'id_payment_statement',
        'fields' => array(
            'order_id' =>       array('type' => self::TYPE_INT,    'required' => true,),
            'id_payment' =>     array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedId'),
            'payment_method' => array('type' => self::TYPE_INT,    'required' => true, 'validate' => 'isUnsignedInt'),
            'statement' =>      array('type' => self::TYPE_STRING, 'required' => true, 'size' => 40),
        ),
    );
}