<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../base/iblock.orm.php');

class QuizTemplateMap extends IBlockORMMap{

    public $iblock_id = 31;

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'MAX_SCORE', 'name' => 'max_score', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'QUES_NUM', 'name' => 'ques_num', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'IS_SURVEY', 'name' =>'is_survey', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::BOOL,
            'list' =>
                array(
                   array('value' => true, 'bvalue' => 'Y', 'enum_id' => 59),
                   array('value' => false, 'bvalue' => 'N', 'enum_id' => 60)
                )
        )),
        array('bname' => 'IS_PUBLIC', 'name' => 'is_public', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::BOOL,
            'list' =>
            array(
                array('value' => true, 'bvalue' => 'Y', 'enum_id' => 77),
                array('value' => false, 'bvalue' => 'N', 'enum_id' => 78)
            )
        )),
    );


}