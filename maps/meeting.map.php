<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/meeting.config.php');
require_once(__DIR__.'/../assign/assignable.php');

class MeetingMap extends AssignableMap{

    public $iblock_id = _MeetingIblockId;

    public $assign_code = 'MEETING';

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TOTAL_DURATION', 'name' => 'duration', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'USERS_NUM', 'name' => 'users_num', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'GUESTS_NUM', 'name' => 'guests_num', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TYPE', 'name' =>'type', 'type' => BitrixORMDataTypes::ENUM, 'data' => array(
            'type' => BitrixORMDataTypes::STRING,
            'list' =>
                array(
                   array('value' => 'public', 'bvalue' => 'OPEN', 'enum_id' => _MeetingTypePublic),
                   array('value' => 'private', 'bvalue' => 'CLOSE', 'enum_id' => _MeetingTypePrivate)
                )
        ))
    );


}