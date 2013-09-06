<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../base/iblock.orm.php');

class CoursePageMap extends IBlockORMMap{

    public $iblock_id = 37;

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'THUMB_IMG', 'name' => 'thumb', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'INDEX', 'name' => 'index', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'COURSE_ID', 'name' => 'course_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TYPE', 'name' =>'type', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
            array(
                array('value' => 'image', 'bvalue' => 'IMG', 'enum_id' => 88),
                array('value' => 'video', 'bvalue' => 'VIDEO', 'enum_id' => 89),
                array('value' => 'flash', 'bvalue' => 'FLASH', 'enum_id' => 90),
                array('value' => 'custom', 'bvalue' => 'CUSTOM', 'enum_id' => 91),
                array('value' => 'html', 'bvalue' => 'HTML', 'enum_id' => 92),
            )
        )),
        array('bname' => 'HAS_AUDIO', 'name' => 'has_audio', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::BOOL,
            'list' =>
            array(
                array('value' => true, 'bvalue' => 'Y', 'enum_id' => 93),
                array('value' => false, 'bvalue' => 'N', 'enum_id' => 94)
            )
        )),
        array('bname' => 'AUDIO', 'name' => 'audio', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'HAS_TITLE', 'name' => 'has_title', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::BOOL,
            'list' =>
            array(
                array('value' => true, 'bvalue' => 'Y', 'enum_id' => 95),
                array('value' => false, 'bvalue' => 'N', 'enum_id' => 96)
            )
        )),
        array('bname' => 'TITLE', 'name' => 'title', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'DATA', 'name' => 'data', 'type' => BitrixORMDataTypes::JSON)
    );


}