<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class ShopInvoice extends ObjectModel {

    public $start_date;
    public $end_date;
    public $generated_date;
    public $filename;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'shop_invoice',
        'primary' => 'id_shop_invoice',
        'fields' => array(
            'start_date' =>     array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'end_date' =>       array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'generated_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'filename' =>       array('type' => self::TYPE_STRING, 'required' => true),
        ),
    );
    
    public function isGenerated() {
        if ($this->start_date == null || $this->end_date == null)
            return false;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `id_shop_invoice`
            FROM `'._DB_PREFIX_.'shop_invoice`
            WHERE `start_date` = \''.$this->start_date.'\' AND `end_date` = \''.$this->end_date.'\'');
    }
}

