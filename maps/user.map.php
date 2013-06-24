<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 16:35
 */

namespace ru\teachbase;


require_once(dirname(__FILE__).'/../base/user.orm.php');

class UserMap extends BitrixORMMapUser{

    public $props = array(
        array('bname' => 'HINTS', 'name' => 'hints', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'SHOW_HINTS', 'name' => 'show_hints', 'type' => BitrixORMDataTypes::INT)
    );

}