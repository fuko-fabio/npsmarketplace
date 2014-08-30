<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24SellerSettings extends ObjectModel
{
    public $id_seller;
    public $spid;
    public $registration_date;
    public $active;
    public $register_link;

    public function __construct($id_seller_settings = null, $id_seller = null) {
        if (empty($id_seller_settings) && !empty($id_seller)) {
            $query = new DbQuery();
            $query
                -> select('`id_seller_settings`')
                -> from('p24_seller_settings')
                -> where('`id_seller` = '.$id_seller);
            $id_seller_settings = Db::getInstance() -> getValue($query);
        }
        parent::__construct($id_seller_settings);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_seller_settings',
        'primary' => 'id_seller_settings',
        'fields' => array(
            'id_seller' =>         array('type' => self::TYPE_INT,    'required' => true),
            'spid' =>              array('type' => self::TYPE_STRING, 'required' => true),
            'registration_date' => array('type' => self::TYPE_DATE,   'required' => true),
            'active' =>            array('type' => self::TYPE_BOOL,   'required' => true),
            'register_link' =>     array('type' => self::TYPE_STRING, 'required' => true),
        ),
    );
}