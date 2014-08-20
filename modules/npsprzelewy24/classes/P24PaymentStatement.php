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