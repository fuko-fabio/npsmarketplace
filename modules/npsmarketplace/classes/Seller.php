<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/

class SellerCore extends ObjectModel
{
    /** @var integer id */
    public $id;

    /** @var integer Customer id */
    public $id_customer;

    /** @var string Request date */
    public $request_date;

    /** @var string Name */
    public $name;

    /** @var string e-mail */
    public $email;

    /** @var string phone */
    public $phone;

    /** @var integer NIP */
    public $nip;

    /** @var integer REGON */
    public $regon;

    /** @var string ENUM('requested', 'approved', 'locked', 'none') Seller account state*/
    public $state = 'requested';

    /** @var integer NIP */
    public $commision;

    /** @var string Company name */
    public $company_name;

    /** @var string Company description */
    public $company_description;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'seller',
        'primary' => 'id_seller',
        'multilang' => true,
        'fields' => array(
            'id_customer' =>         array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId',  'required' => true),
            'request_date' =>        array('type' => self::TYPE_STRING, 'validate' => 'isDateFormat'),
            'state' =>               array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'values' => array('requested', 'approved', 'locked'), 'default' => 'requested'),
            'email' =>               array('type' => self::TYPE_STRING, 'validate' => 'isEmail',       'required' => true),
            'phone' =>               array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'required' => true),
            'nip' =>                 array('type' => self::TYPE_INT,    'validate' => 'isNip',         'required' => true),
            'regon' =>               array('type' => self::TYPE_INT,    'validate' => 'isRegon',       'required' => true),
            'commision' =>           array('type' => self::TYPE_INT,    'validate' => 'isUnsignedInt', 'required' => true),

             // Lang fields
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => true, 'size' => 128),
            'company_description' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml',   'required' => true,'lang' => true),
            'company_name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true,'lang' => true),
        ),
        'associations' => array(
            'customer' => array('type' => self::HAS_ONE)
        )
    );

    /**
      * Adds seller to database
      *
      * @return boolean success
      */
    // public function add($autodate = true, $null_values = false)
    // {
        // $sql = 'INSERT INTO '._DB_PREFIX_.'seller(
                // id_customer,
                // state,
                // request_date,
                // company_name,
                // company_description,
                // name,
                // phone,
                // email,
                // nip,
                // regon)
            // VALUES (
                // '.$id_customer.',
                // "'.$state.'",
                // NOW(),
                // "'.$company_name.'",
                // "'.$company_description.'",
                // "'.$name.'",
                // '.$phone.',
                // "'.$emain.'"
                // '.$nip.',
                // '.$regon.',)';
// 
        // Db::getInstance()->execute($sql);
    // }

    /**
      * Updates seller state
      *
      * @param integer $id_seller Seller ID 
      * @param string $state Account state 
      * @return boolean success
      */
    public function updateState()
    {
        Db::getInstance()->update('seller', array(
            'state' => $state,
        ), 'id_seller = '.(int)$id);
    }
}

