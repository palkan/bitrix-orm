<?php
namespace ru\teachbase;

require_once(dirname(__FILE__).'/../base/iblock.orm.php');

class TariffMap extends IBlockORMMap{

    public $iblock_id = 25;

    public $props = array(
        array('bname' => 'LIMIT_USER', 'name' => 'user_limit', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'LIMIT_SPACE', 'name' => 'disk_limit', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'CLASS', 'name' => 'code', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'PRICE', 'name' => 'price', 'type' => BitrixORMDataTypes::INT)
    );


}