<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*/

class Answer extends ObjectModel {
    public $id_question;
    public $answer;
    public $id_ticket;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'answer',
        'primary' => 'id_answer',
        'multilang' => false,
        'fields' => array(
            'answer' =>      array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'required' => true, 'size' => 1024),
            'id_question' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_ticket' =>   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        )
    );

    public static function getByTicketId($id_ticket) {
        if (!isset($id_ticket) || empty($id_ticket)) {
            return array();
        }
        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('answer')
            ->where('`id_ticket` = '.$id_ticket)
            ->orderBy('answer ASC');
        return Db::getInstance()->ExecuteS($dbquery);
    }

}

