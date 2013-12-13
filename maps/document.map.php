<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/document.config.php');
require_once(__DIR__.'/../assign/assignable.php');

class DocumentMap extends AssignableMap{

    public $iblock_id = _DocumentIblockId;

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
                array('value' => 'course', 'bvalue' => 'COURSE', 'enum_id' => _DocumentContextCourse),
                array('value' => 'quiz', 'bvalue' => 'QUIZ', 'enum_id' => _DocumentContextQuiz),
                array('value' => 'meeting', 'bvalue' => 'MEETING', 'enum_id' => _DocumentContextMeeting),
                array('value' => 'none', 'bvalue' => 'NONE', 'enum_id' => _DocumentContextNone)
            )
        )),
        array('bname' => 'TYPE', 'name' =>'type', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
            array(
                array('value' => 'image', 'bvalue' => 'IMAGE', 'enum_id' => _DocumentTypeImage),
                array('value' => 'video', 'bvalue' => 'VIDEO', 'enum_id' => _DocumentTypeVideo),
                array('value' => 'audio', 'bvalue' => 'AUDIO', 'enum_id' => _DocumentTypeAudio),
                array('value' => 'document', 'bvalue' => 'DOC', 'enum_id' => _DocumentTypeDocument),
                array('value' => 'presentation', 'bvalue' => 'PRES', 'enum_id' => _DocumentTypePresentation),
                array('value' => 'table', 'bvalue' => 'TABLE', 'enum_id' => _DocumentTypeTable),
                array('value' => 'other', 'bvalue' => 'OTHER', 'enum_id' => _DocumentTypeOther),
                array('value' => 'folder', 'bvalue' => 'FOLDER', 'enum_id' => _DocumentTypeFolder),
                array('value' => 'recording', 'bvalue' => 'RECORDING', 'enum_id' => 109)
            )
        )),
        array('bname' => 'DATA', 'name' => 'data', 'type' => BitrixORMDataTypes::JSON)
    );


}