<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require_once(__DIR__.'/../base/iblock.orm.php');
require(__DIR__.'/config/group.config.php');


class GroupMap extends IBlockORMMap{

    public $iblock_id = _GroupIblockId;

    public $props = array(
        array('bname' => 'PARTNER', 'name' => 'partner_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'USERS', 'name' =>'user_ids', 'type' => BitrixORMDataTypes::INT)
    );


}