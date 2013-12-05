<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/course.template.config.php');
require_once(__DIR__.'/../assign/assignable.php');

class CourseTemplateMap extends AssignableMap{

    public $iblock_id = _CourseTemplateIblockId;

    public $assign_code = 'COURSE';

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'COVER_IMG', 'name' => 'cover_img', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'PAGES_NUM', 'name' => 'pages_num', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'EDITABLE', 'name' =>'editable', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'IS_PUBLIC', 'name' => 'is_public', 'type' => BitrixORMDataTypes::BOOL),
    );


}