<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24SellerCompany extends ObjectModel {

    public $id_seller;
    public $spid;
    public $registration_date;
    public $register_link;
    public $company_name;
    public $city;
    public $street;
    public $post_code;
    public $email;
    public $nip;
    public $person;
    public $regon;
    public $iban;
    public $acceptance;

    public function __construct($id_seller_settings = null, $id_seller = null) {
        if (empty($id_seller_settings) && !empty($id_seller)) {
            $query = new DbQuery();
            $query
                -> select('`id_seller_company`')
                -> from('p24_seller_company')
                -> where('`id_seller` = '.$id_seller);
            $id = Db::getInstance() -> getValue($query);
            if ($id) {
                $id_seller_settings = $id;
            }
        }
        parent::__construct($id_seller_settings);
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'p24_seller_company',
        'primary' => 'id_seller_company',
        'fields' => array(
            'id_seller' =>         array('type' => self::TYPE_INT,    'required' => true),
            'spid' =>              array('type' => self::TYPE_STRING, 'required' => true),
            'registration_date' => array('type' => self::TYPE_DATE,   'required' => true),
            'acceptance' =>        array('type' => self::TYPE_BOOL,   'required' => true),
            'register_link' =>     array('type' => self::TYPE_STRING, 'required' => true),
            'company_name' =>      array('type' => self::TYPE_STRING, 'required' => true),
            'city' =>              array('type' => self::TYPE_STRING, 'required' => true),
            'street' =>            array('type' => self::TYPE_STRING, 'required' => true),
            'post_code' =>         array('type' => self::TYPE_STRING, 'required' => true),
            'email' =>             array('type' => self::TYPE_STRING, 'required' => true),
            'nip' =>               array('type' => self::TYPE_STRING, 'required' => true),
            'person' =>            array('type' => self::TYPE_STRING, 'required' => true),
            'regon' =>             array('type' => self::TYPE_STRING),
            'iban' =>              array('type' => self::TYPE_STRING, 'required' => true),
        ),
    );
}