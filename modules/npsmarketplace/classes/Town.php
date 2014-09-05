<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class Town extends ObjectModel
{
    public $id;
    public $name;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'town',
        'primary' => 'id_town',
        'multilang' => true,
        'fields' => array(
            'name' =>       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => true, 'size' => 64),
            'active' =>     array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );
}

