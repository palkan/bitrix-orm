<?php
/**
 * User: palkan
 * Date: 24.04.13
 * Time: 16:58
 */

namespace ru\teachbase;

require_once(__DIR__.'/../base/custom.orm.php');

class AssignRelationMap extends CustomORMMap{

    public $table = 't_assign_relations';

    public $unique = array('user_id','element_id');

    public $fields = array(
        array('bname' => 'user_id', 'name' => 'user_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'element_id', 'name' => 'element_id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'role', 'name' => 'role', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'code', 'name' => 'code', 'type' => BitrixORMDataTypes::STRING)
    );


}