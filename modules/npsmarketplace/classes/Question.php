<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*/

class Question extends ObjectModel {
    public $question;
    public $required;
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'question',
        'primary' => 'id_question',
        'multilang' => false,
        'fields' => array(
            'question' =>   array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'required' => true, 'size' => 1024),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'required' =>   array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        )
    );

    public static function getByProductId($id_product, $only_ids = false) {
        if (!isset($id_product) || empty($id_product)) {
            return array();
        }
        $dbquery = new DbQuery();
        if ($only_ids) {
            $dbquery->select('id_question');
        } else {
            $dbquery->select('*');
        }
        $dbquery->from('question')
            ->where('`id_product` = '.$id_product)
            ->orderBy('question ASC');
        $result = Db::getInstance()->ExecuteS($dbquery);
        if (!$only_ids) {
            return $result;
        } else {
            $r = array();
            foreach ($result as $key => $value) {
                $r[] = $value['id_question'];
            }
            return $r;
        }
    }

}

