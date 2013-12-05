<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/partner.config.php');
require_once(__DIR__.'/../assign/assignable.php');

class PartnerMap extends AssignableMap{

    public $iblock_id = _PartnerIblockId;

    public $assign_code = 'PARTNER';

    public $props = array(
        array('bname' => 'TARIF', 'name' => 'tariff_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'APS_ID', 'name' => 'aps_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'LOGO', 'name' => 'logo', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'SUBDOMAIN', 'name' => 'subdomain', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'INFO_ID', 'name' => 'info_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'IS_APS', 'name' =>'is_aps', 'type' => BitrixORMDataTypes::BOOL)
    );


}