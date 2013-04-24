<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 12:58
 */

require_once(dirname(__FILE__).'/../base/iblock.orm.php');
require_once(dirname(__FILE__).'/../base/user.orm.php');
require_once(dirname(__FILE__).'/../base/custom.orm.php');

class Test extends IBlockORM{

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


class TestCustom extends CustomORM{


    function __construct(){
        parent::__construct(new CustomORMMap());
    }


}


class TestCustomORMMap extends CustomORMMap{

    public $table = 'test';
    public $fields = array(
        array('bname' => 'user_id', 'name' => 'user_id', 'type' => 'int'),
        array('bname' => 'partner_id', 'name' => 'partner_id', 'type' => 'int'),
        array('bname' => 'name', 'name' => 'name', 'type' => 'string'),
        array('bname' => 'active', 'name' => 'active', 'type' => 'bool'),
        array('bname' => 'date', 'name' => 'date', 'type' => 'datetime', 'data' => 'Y-m-d H:i:s')
    );

}



class TestORMMap extends IBlockORMMap{

    public $iblock_id = 666;


    public $props = array(
        array('bname' => 'PROPERTY_PARTNER_ID', 'name' => 'partner_id', 'type' => 'int'),
        array('bname' => 'PROPERTY_TYPE', 'name' =>'type', 'type' => 'enum', 'data' => array(
            'type' => 'string',
            'list' =>
                array(
                   array('value' => 'open', 'bvalue' => 'OPEN', 'enum_id' => 11),
                   array('value' => 'closed', 'bvalue' => 'CLOSED', 'enum_id' => 12),
                   array('value' => 'undef', 'bvalue' => 'UNDEFINED', 'enum_id' => 13)
                )
        )),
        array('bname' => 'PROPERTY_IS_PUBLIC', 'name' => 'is_public', 'type' => 'enum', 'data' => array(
            'type' => 'bool',
            'list' =>
            array(
                array('value' => true, 'bvalue' => 'Y', 'enum_id' => 21),
                array('value' => false, 'bvalue' => 'N', 'enum_id' => 22)
            )
        )),
    );


}