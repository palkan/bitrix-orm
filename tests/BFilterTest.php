<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 14:26
 */

require_once(dirname(__FILE__).'/test.orm.php');

class BFilterTest extends PHPUnit_Framework_TestCase {


    /**
     * @var BitrixORMMap
     */

    protected  static $map;


    public static function setUpBeforeClass()
    {

        self::$map = new TestORMMap();

    }


    public function testSimpleFilter(){
        $exp = array(
            'ID' => array(1,2),
            'NAME' => '%vasya'
        );

        $arr = filter()->by_id(1,2)->by_name('%vasya')->toArray(self::$map);

        $this->assertEquals($exp,$arr);
    }


    public function testSimpleFilter3(){
        $exp = array(
            'ID' => array(1,2),
            '>DATE_CREATE' => "01.01.2013 00:00:00"
        );

        $arr = filter()->by_id(1,2)->by_created_at_gt(1356998400)->toArray(self::$map);

        $this->assertEquals($exp,$arr);
    }

    public function testSimpleFilter2(){

        $exp = array(
            'ID' => array(1,2),
            'NAME' => '%vasya%',
            '><DATE_CREATE' => array("01.01.2013 00:00:00","01.01.2013 00:00:00"),
            'PROPERTY_TYPE' => array(array('ENUM_ID'=>11),array('ENUM_ID'=>12)),
            'ACTIVE' => 'Y'
        );



        $arr = filter()->by_id(1,2)->by_name('%vasya%')->by_created_at_between(1356998400,1356998400)->by_type('open','closed')->by_active(true)->toArray(self::$map);


        $this->assertEquals($exp,$arr);

    }


    public function testLogicFilter(){

        $exp = array(
            'ID' => 1,
            array(
                'LOGIC' => 'OR',
                array('NAME' => '%vasya%'),
                array('><DATE_CREATE' => array("01.01.2013 00:00:00","01.01.2013 00:00:00"))
            )
        );


        $arr = filter()->by_id(1)->_or(filter()->by_name('%vasya%'),filter()->by_created_at_between(1356998400,1356998400))->toArray(self::$map);



        $this->assertEquals($exp,$arr);

    }


    public function testLogicFilter2(){

        $exp = array(
            array(
                'LOGIC' => 'OR',
                array(
                    'LOGIC' => 'AND',
                    array('NAME' => '%vasya%'),
                    array('><DATE_CREATE' => array("01.01.2013 00:00:00","01.01.2013 00:00:00"))
                ),
                array('ACTIVE' => 'Y')
            ),
            'ID' => 1
        );



        $arr = filter()->by_id(1)->_or(
            filter()->_and(
                filter()->by_name('%vasya%'),
                filter()->by_created_at_between(1356998400,1356998400)
            ),
            filter()->by_active(true)
        )->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }



    public function testLogicFilter3(){

        $exp = array(
            'ID' => 1,
            array(
                'LOGIC' => 'OR',
                array(
                    'LOGIC' => 'AND',
                    array('NAME' => '%vasya%'),
                    array('><DATE_CREATE' => array("01.01.2013 00:00:00","01.01.2013 00:00:00"))
                ),
                array(
                    'LOGIC' => 'AND',
                    array('ACTIVE' => 'Y'),
                    array('PROPERTY_IS_PUBLIC' => array('ENUM_ID' => 21))
                )
            )
        );



        $arr = filter()->by_id(1)->_or(
            filter()->_and(
                filter()->by_name('%vasya%'),
                filter()->by_created_at_between(1356998400,1356998400)
            ),
            filter()->_and(
                filter()->by_active(true),
                filter()->by_is_public(true)
            )
        )->toArray(self::$map);


        $this->assertEquals($exp,$arr);

    }

}
