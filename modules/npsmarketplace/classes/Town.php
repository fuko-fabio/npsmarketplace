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
    public $id_feature_value;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'town',
        'primary' => 'id_town',
        'multilang' => true,
        'fields' => array(
            'name' =>             array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => true, 'size' => 64),
            'id_feature_value' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'active' =>           array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'default' =>          array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function add($autodate = true, $null_values = false) {
        $feature_id = Configuration::get('NPS_FEATURE_TOWN_ID');
        $feature_value = new FeatureValue();
        $feature_value->id_feature = $feature_id;
        $feature_value->value = $this->name;
        if (!$feature_value->save())
            return false;
        $this->id_feature_value = $feature_value->id;

        return parent::add($autodate, $null_values);
    }

    public function update($autodate = true, $null_values = false) {
        $feature_value = new FeatureValue($this->id_feature_value);
        $feature_value->value = $this->name;
        if (!$feature_value->save())
            return false;

        return parent::update($autodate, $null_values);
    }

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

    public static function getActiveTowns($lang_id) {
        $sql = 'SELECT t.`id_town`, `name`, `id_feature_value` from `'._DB_PREFIX_.'town` t
                LEFT JOIN `'._DB_PREFIX_.'town_lang` tl ON (tl.`id_town` = t.`id_town`)
                WHERE tl.`id_lang` = '.(int)$lang_id;
        return Db::getInstance()->ExecuteS($sql);
    }
}

