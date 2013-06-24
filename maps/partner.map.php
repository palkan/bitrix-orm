<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../assign/assignable.php');

class PartnerMap extends AssignableMap{

    public $iblock_id = 12;

    public $assign_code = 'PARTNER';

    public $props = array(
        array('bname' => 'TARIFF', 'name' => 'tariff_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'APS_ID', 'name' => 'aps_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'LOGO', 'name' => 'logo', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'SUBDOMAIN', 'name' => 'subdomain', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'INFO_ID', 'name' => 'info_id', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'IS_APS', 'name' =>'is_aps', 'type' => BitrixORMDataTypes::BOOL, 'data' => array(
            'type' => BitrixORMDataTypes::BOOL,
            'list' =>
                array(
                   array('value' => true, 'bvalue' => 'Y', 'enum_id' => 80),
                   array('value' => false, 'bvalue' => 'N', 'enum_id' => 81)
                )
        ))

    );


}