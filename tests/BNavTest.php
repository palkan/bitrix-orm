<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 10:48
 */

require_once(dirname(__FILE__).'/test.orm.php');

class BNavTest extends PHPUnit_Framework_TestCase {

    /**
     * @var BitrixORMMap
     */

    protected  static $map;


    public static function setUpBeforeClass()
    {

        self::$map = new TestORMMap();

    }



    public function testNavParamsEmpty(){

        $navi = navi();

        $this->assertTrue(!$navi->toArray());

    }


    public function testNavParamsLimit(){

        $navi = navi()->limit(10);

        $exp = array('nTopCount' => 10);

        $this->assertEquals($exp,$navi->toArray());

    }


    public function testNavParamsPages(){

        $navi = navi()->limit(10)->page(2);

        $exp = array('nPageSize' => 10, 'iNumPage' => 2);

        $this->assertEquals($exp,$navi->toArray());

    }


    public function testNavParamsSort(){

        $navi = navi()->order_by_partner_id('asc')->order_by_name('desc');

        $exp = array('PROPERTY_PARTNER_ID' => 'asc', 'NAME' => 'desc');

        $this->assertEquals($exp,$navi->sortArray(self::$map));

    }

}
