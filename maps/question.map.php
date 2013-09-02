<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../base/iblock.orm.php');

class QuestionMap extends IBlockORMMap{

    public $iblock_id = 32;

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'SCORE', 'name' => 'score', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TIME', 'name' => 'time', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'ANSWER', 'name' => 'answer', 'type' => BitrixORMDataTypes::OBJECT),
        array('bname' => 'OPTIONS', 'name' => 'options', 'type' => BitrixORMDataTypes::OBJECT),
        array('bname' => 'MEDIA', 'name' => 'ques_media', 'type' => BitrixORMDataTypes::OBJECT),
        array('bname' => 'QUIZ_ID', 'name' => 'quiz_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TYPE', 'name' =>'type', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
                array(
                   array('value' => 'short', 'bvalue' => 'SHORT', 'enum_id' => 61),
                    array('value' => 'single_choice', 'bvalue' => 'SINGLE_CHOICE', 'enum_id' => 62),
                    array('value' => 'multiple_choice', 'bvalue' => 'MULTIPLE_CHOICE', 'enum_id' => 63),
                    array('value' => 'order', 'bvalue' => 'ORDER', 'enum_id' => 65),
                    array('value' => 'match', 'bvalue' => 'MATCH', 'enum_id' => 64),
                   array('value' => 'open', 'bvalue' => 'OPEN', 'enum_id' => 70)
                )
        ))

    );


}