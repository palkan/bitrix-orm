<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:01
 */

require_once(dirname(__FILE__).'/test.orm.php');

class BitrixORMMapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BitrixORMMap
     */

    protected  static $map;


    public static function setUpBeforeClass()
    {

        self::$map = new TestORMMap();

    }



    public function testGetSelectedFields()
    {

        $exp = array('ID', 'NAME', 'ACTIVE', 'DATE_ACTIVE_FROM',
            'DATE_ACTIVE_TO', 'PREVIEW_TEXT', 'DETAIL_TEXT',
            'DATE_CREATE', 'CREATED_BY', 'MODIFIED_BY','TIMESTAMP_X','PROPERTY_PARTNER_ID', 'PROPERTY_TYPE', 'PROPERTY_IS_PUBLIC');

        $res = self::$map->GetSelectFields();

        sort($exp);
        sort($res);

        $this->assertEquals($exp, $res);


    }

    /**
     * @dataProvider providerGetBitrixFieldValue
     */

    public function testGetBitrixFieldValue($key,$val,$exp)
    {

        $this->assertEquals($exp,self::$map->GetBitrixFieldValue($key,$val));

    }


    public function providerGetBitrixFieldValue()
    {
        return array(
            array('id', '12', (object)array('key'=>"ID",'value' => 12)),
            array('created_at', 1356998400, (object)array('key'=>"DATE_CREATE",'value' => "01.01.2013 00:00:00")),
            array('active', true, (object)array('key'=>'ACTIVE', 'value' => 'Y')),
            array('description', 'DESC', (object)array('key' => 'DETAIL_TEXT', 'value' => 'DESC')),
            array('type', 'open', (object)array('key' => 'PROPERTY_TYPE', 'value' => array('ENUM_ID' => 11))),
            array('is_public', true, (object)array('key' => 'PROPERTY_IS_PUBLIC', 'value' => array('ENUM_ID' => 21))),
            array('partner_id', array('12',13),(object)array('key' => 'PROPERTY_PARTNER_ID', 'value' => array(12,13))),
            array('smth','bla-bla',null)
        );
    }



    /**
     * @dataProvider providerGetORMFieldValue
     */

    public function testGetORMFieldValue($bkey,$bval,$exp)
    {

        $this->assertEquals($exp,self::$map->GetORMFieldValue($bkey,$bval));

    }


    public function providerGetORMFieldValue()
    {
        return array(
            array('ID', '1', (object)array('key'=>"id",'value' => 1)),
            array('DATE_CREATE', "01.01.2013 00:00:00", (object)array('key'=>"created_at",'value' => 1356998400)),
            array('ACTIVE', "N", (object)array('key'=>'active', 'value' => false)),
            array('DETAIL_TEXT', 'DESC', (object)array('key' => 'description', 'value' => 'DESC')),
            array('PROPERTY_TYPE', 'CLOSED', (object)array('key' => 'type', 'value' => 'closed')),
            array('PROPERTY_IS_PUBLIC', 'N', (object)array('key' => 'is_public', 'value' => false)),
            array('PROPERTY_PARTNER_ID', array(12,13),(object)array('key' => 'partner_id', 'value' => array(12,13))),
            array('SMTH', 'bla-bla', null)
        );
    }


    public function testFromBitrixData(){


        $exp = new Test();
        $exp->active = true;
        $exp->id = 1;
        $exp->created_at = 1356998400;
        $exp->date_active_from = 1356998400;
        $exp->date_active_to = 1356998400;
        $exp->created_by = 1;
        $exp->modified_by = 1;
        $exp->description = 'Description';
        $exp->preview_text = '';
        $exp->name = 'Test';
        $exp->updated_at = 1356998400;
        $exp->type = 'open';
        $exp->partner_id = 12;
        $exp->is_public = false;


        $data = array('ID' => 1, 'NAME'=>'Test', 'ACTIVE'=>'Y', 'DATE_ACTIVE_FROM'=>"01.01.2013 00:00:00",
            'DATE_ACTIVE_TO'=>"01.01.2013 00:00:00", 'PREVIEW_TEXT'=>'', 'DETAIL_TEXT'=>'Description',
            'DATE_CREATE'=>"01.01.2013 00:00:00", 'CREATED_BY'=>1, 'MODIFIED_BY'=>'1',
            'TIMESTAMP_X'=>"01.01.2013 00:00:00",'PROPERTY_PARTNER_ID_VALUE'=>12, 'PROPERTY_TYPE_VALUE'=>'OPEN', 'PROPERTY_IS_PUBLIC_ENUM_ID'=>12, 'PROPERTY_IS_PUBLIC_VALUE' => 'N');

        $test = new Test();

        $test->fromBitrixData($data);

        $this->assertEquals($exp,$test);

    }



}
