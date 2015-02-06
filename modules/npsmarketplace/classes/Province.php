<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Town.php');

class Province extends ObjectModel
{
    public $name;
    public $active;
    public $selectable;
    public $id_feature_value;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'province',
        'primary' => 'id_province',
        'multilang' => true,
        'fields' => array(
            'name' =>             array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'lang' => true, 'size' => 64),
            'id_feature_value' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'active' =>           array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'selectable' =>       array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        )
    );

    public function add($autodate = true, $null_values = false) {
        $feature_id = Configuration::get('NPS_FEATURE_PROVINCE_ID');
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

    public static function getFeatureValueId($id_province) {
        if (!$id_province) {
            return null;
        }
        $dbquery = new DbQuery();
        $dbquery->select('id_feature_value')
            ->from('province')
            ->where('`id_province` = '. $id_province);
        return Db::getInstance()->getValue($dbquery);
    }

    public static function getIdByFeatureValueId($id_feature_value) {
        if (!$id_feature_value) {
            return null;
        }
        $dbquery = new DbQuery();
        $dbquery->select('id_province')
            ->from('province')
            ->where('`id_feature_value` = '. $id_feature_value);
        return Db::getInstance()->getValue($dbquery);
    }

    public static function getAll($id_lang, $add_towns = false) {
        if($id_lang == null)
            $id_lang = Context::getContext()->language->id;
        
        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('province', 'p')
            ->leftJoin('province_lang', 'pl', 'p.id_province = pl.id_province')
            ->where('pl.`id_lang` = '.$id_lang)
            ->orderBy('pl.name ASC');
        $result = Db::getInstance()->ExecuteS($dbquery);

        if ($add_towns) {
            foreach ($result as $key => $value) {
                $result[$key]['towns'] = Town::getAll($id_lang, $value['id_province']);
            }
        }
        return $result;
    }

    public static function getActiveProvinces($id_lang, $add_towns = false, $selectable = null) {
        $dbquery = new DbQuery();
        $dbquery->select('p.`id_province`, `name`, `id_feature_value`')
            ->from('province', 'p')
            ->leftJoin('province_lang', 'pl', 'p.id_province = pl.id_province')
            ->orderBy('pl.name ASC');
        if ($selectable == null) {
            $dbquery->where('pl.`id_lang` = '.$id_lang.' AND p.`active` = 1');
        } else {
            $dbquery->where('pl.`id_lang` = '.$id_lang.' AND p.`active` = 1 AND p.`selectable` = '.$selectable);
        }
        $result = Db::getInstance()->executeS($dbquery);

        if ($add_towns) {
            foreach ($result as $key => $value) {
                $result[$key]['towns'] = Town::getActiveTowns($id_lang, $value['id_province'], $selectable);
            }
        }
        return $result;
    }
}

