<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

require_once(dirname(__FILE__).'/../base/bitrix.orm.php');
require_once(dirname(__FILE__).'/../base/user.orm.php');

class Test extends BitrixORM{


    public $partner_id;

    public $is_public;

    public $type;


    function __construct(){

        parent::__construct(new TestORMMap());

    }


}



class TestUser extends BitrixUserORM{


    function __construct(){
        parent::__construct(new TestUserORMMap());
    }


}


class TestUserORMMap extends BitrixORMMapUser{

}


class TestORMMap extends BitrixORMMap{

    public $iblock_id = 666;


    public $props = array(
        array('bname' => 'PROPERTY_PARTNER_ID', 'name' => 'partner_id', 'type' => 'int'),
        array('bname' => 'PROPERTY_TYPE', 'name' =>'type', 'type' => 'enum', 'scheme' => array(
            'type' => 'string',
            'list' =>
                array(
                   array('value' => 'open', 'bvalue' => 'OPEN', 'enum_id' => 11),
                   array('value' => 'closed', 'bvalue' => 'CLOSED', 'enum_id' => 12),
                   array('value' => 'undef', 'bvalue' => 'UNDEFINED', 'enum_id' => 13)
                )
        )),
        array('bname' => 'PROPERTY_IS_PUBLIC', 'name' => 'is_public', 'type' => 'enum', 'scheme' => array(
            'type' => 'bool',
            'list' =>
            array(
                array('value' => true, 'bvalue' => 'Y', 'enum_id' => 21),
                array('value' => false, 'bvalue' => 'N', 'enum_id' => 22)
            )
        )),
    );


}