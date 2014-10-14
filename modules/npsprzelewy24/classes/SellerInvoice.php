<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class SellerInvoice extends ObjectModel {
    public $id_seller;
    public $start_date;
    public $end_date;
    public $generated_date;
    public $filename;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'seller_invoice',
        'primary' => 'id_seller_invoice',
        'fields' => array(
            'id_seller' =>      array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'start_date' =>     array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'end_date' =>       array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'generated_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'filename' =>       array('type' => self::TYPE_STRING, 'required' => true),
        ),
    );
}

