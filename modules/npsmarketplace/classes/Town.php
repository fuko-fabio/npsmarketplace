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
    public $default;

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
            'default' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );
    
    public static function getDefaultTownId() {
        $dbquery = new DbQuery();
        $dbquery->select('id_town')
            ->from('town')
            ->where('`default` = 1');
        return Db::getInstance()->getValue($dbquery);
    }
    
    public static function getAll($id_lang) {
        if($id_lang == null)
            $id_lang = Context::getContext()->language->id;
        
        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('town', 't')
            ->leftJoin('town_lang', 'tl', 't.id_town = tl.id_town')
            ->where('tl.`id_lang` = '.$id_lang);
        return Db::getInstance()->executeS($dbquery);
    }
}

