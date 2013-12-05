<?php
/**
 * User: palkan
 * Date: 24.04.13
 * Time: 16:58
 */

namespace ru\teachbase;

require_once(__DIR__.'/../base/custom.orm.php');

class QuestionResultMap extends CustomORMMap{

    public $table = 't_quiz_user_answers';

    public $has_id = true;

    public $fields = array(
        array('bname' => 'id', 'name' => 'id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'quiz_id', 'name' => 'quiz_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'score', 'name' => 'score', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'time', 'name' => 'time', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'answer', 'name' => 'answer', 'type' => BitrixORMDataTypes::OBJECT),
        array('bname' => 'question_id', 'name' => 'question_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'user_id', 'name' => 'user_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'success', 'name' => 'success', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'complete', 'name' =>'complete', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'checked', 'name' =>'checked', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'attempt', 'name' => 'attempt', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'updated_at', 'name' =>'updated_at', 'type' => BitrixORMDataTypes::DATETIME, 'data'=>'Y-m-d H:i:s'),

    );


}