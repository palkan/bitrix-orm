<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/quiz.template.config.php');
require_once(__DIR__.'/../base/iblock.orm.php');

class QuizTemplateMap extends IBlockORMMap{

    public $iblock_id = _QuizTemplateIblockId;

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'MAX_SCORE', 'name' => 'max_score', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'QUES_NUM', 'name' => 'ques_num', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'IS_SURVEY', 'name' =>'is_survey', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'IS_PUBLIC', 'name' => 'is_public', 'type' => BitrixORMDataTypes::BOOL),
    );


}