<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/course.page.config.php');
require_once(__DIR__.'/../base/iblock.orm.php');


class CoursePageMap extends IBlockORMMap{

    public $iblock_id = _CoursePageIblockId;

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'THUMB_IMG', 'name' => 'thumb', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'INDEX', 'name' => 'index', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'COURSE_ID', 'name' => 'course_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TYPE', 'name' =>'type', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
            array(
                array('value' => 'image', 'bvalue' => 'IMG', 'enum_id' => _CoursePageTypeImage),
                array('value' => 'video', 'bvalue' => 'VIDEO', 'enum_id' => _CoursePageTypeVideo),
                array('value' => 'flash', 'bvalue' => 'FLASH', 'enum_id' => _CoursePageTypeFlash),
                array('value' => 'custom', 'bvalue' => 'CUSTOM', 'enum_id' => _CoursePageTypeCustom),
                array('value' => 'html', 'bvalue' => 'HTML', 'enum_id' => _CoursePageTypeHtml),
            )
        )),
        array('bname' => 'HAS_AUDIO', 'name' => 'has_audio', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'AUDIO', 'name' => 'audio', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'HAS_TITLE', 'name' => 'has_title', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'TITLE', 'name' => 'title', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'DATA', 'name' => 'data', 'type' => BitrixORMDataTypes::JSON)
    );


}