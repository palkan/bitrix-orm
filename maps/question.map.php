<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/question.config.php');
require_once(__DIR__.'/../base/iblock.orm.php');

class QuestionMap extends IBlockORMMap{

    public $iblock_id = _QuestionIblockId;

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
                   array('value' => 'short', 'bvalue' => 'SHORT', 'enum_id' => _QuestionTypeShort),
                    array('value' => 'single_choice', 'bvalue' => 'SINGLE_CHOICE', 'enum_id' => _QuestionTypeSingleChoice),
                    array('value' => 'multiple_choice', 'bvalue' => 'MULTIPLE_CHOICE', 'enum_id' => _QuestionTypeMultipleChoice),
                    array('value' => 'order', 'bvalue' => 'ORDER', 'enum_id' => _QuestionTypeOrder),
                    array('value' => 'match', 'bvalue' => 'MATCH', 'enum_id' => _QuestionTypeMatch),
                   array('value' => 'open', 'bvalue' => 'OPEN', 'enum_id' => _QuestionTypeOpen)
                )
        ))

    );


}