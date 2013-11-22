<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../assign/assignable.php');

class DocumentMap extends AssignableMap{

    public $iblock_id = 38;

    public $assign_code = 'DOCUMENT';

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'FILE', 'name' => 'file', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'THUMB_IMG', 'name' => 'thumb', 'type' => BitrixORMDataTypes::FILE),
        array('bname' => 'SIZE', 'name' => 'size', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'EXTENSION', 'name' => 'extension', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'PARENT_ID', 'name' => 'parent_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'CONTEXT_ID', 'name' => 'context_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'CONTEXT_TYPE', 'name' => 'context_type', 'type' => BitrixORMDataTypes::ENUM,'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
            array(
                array('value' => 'course', 'bvalue' => 'COURSE', 'enum_id' => 98),
                array('value' => 'quiz', 'bvalue' => 'QUIZ', 'enum_id' => 99),
                array('value' => 'meeting', 'bvalue' => 'MEETING', 'enum_id' => 100),
                array('value' => 'none', 'bvalue' => 'NONE', 'enum_id' => 97)
            )
        )),
        array('bname' => 'TYPE', 'name' =>'type', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
            array(
                array('value' => 'image', 'bvalue' => 'IMAGE', 'enum_id' => 103),
                array('value' => 'video', 'bvalue' => 'VIDEO', 'enum_id' => 102),
                array('value' => 'audio', 'bvalue' => 'AUDIO', 'enum_id' => 104),
                array('value' => 'document', 'bvalue' => 'DOC', 'enum_id' => 105),
                array('value' => 'presentation', 'bvalue' => 'PRES', 'enum_id' => 106),
                array('value' => 'table', 'bvalue' => 'TABLE', 'enum_id' => 107),
                array('value' => 'other', 'bvalue' => 'OTHER', 'enum_id' => 101),
                array('value' => 'folder', 'bvalue' => 'FOLDER', 'enum_id' => 108),
                array('value' => 'recording', 'bvalue' => 'RECORDING', 'enum_id' => 109)
            )
        )),
        array('bname' => 'DATA', 'name' => 'data', 'type' => BitrixORMDataTypes::JSON)
    );


}