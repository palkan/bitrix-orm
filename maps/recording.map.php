<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/recording.config.php');
require_once(__DIR__.'/../assign/assignable.php');

class RecordingMap extends AssignableMap{

    public $iblock_id = _RecordingIblockId;

    public $assign_code = 'RECORD';

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'SIZE', 'name' => 'size', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'MEETING_ID', 'name' => 'meeting_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'DURATION', 'name' => 'duration', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'DATA', 'name' => 'data', 'type' => BitrixORMDataTypes::JSON)
    );
}