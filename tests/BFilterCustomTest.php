<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 14:26
 */

require_once(dirname(__FILE__).'/test.orm.php');

class BFilterCustomTest extends PHPUnit_Framework_TestCase {


    /**
     * @var BitrixORMMap
     */

    protected  static $map;


    public static function setUpBeforeClass()
    {

        self::$map = new TestCustomORMMap();

    }


    public function testSimpleFilter(){
        $exp = '(user_id = 2)';

        $arr = filter()->by_user_id(2)->toArray(self::$map);

        $this->assertEquals($exp,$arr);
    }




    public function testSimpleFilter2(){

        $exp = array(
            '(date > \'2013-01-01 00:00:00\')',
            '(partner_id <> 3)',
            '(user_id not in (1,2))'
        );



        $arr = filter()->by_user_id_not(1,2)->by_partner_id_not(3)->by_date_gt(1356998400)->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }


    public function testSimpleFilter3(){

        $exp = array(
            '(partner_id <> 3)',
            '(active = \'Y\')',
            '((name like \'%vas%\') or (name like \'%vov%\'))'
        );



        $arr = filter()->by_name_like('%vas%','%vov%')->by_active(true)->by_partner_id_not(3)->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }


    public function testLogicFilter(){

        $exp = array(
            '((date > \'2013-01-01 00:00:00\') or (date < \'2013-01-01 00:00:00\'))',
            '(user_id = 1)'
        );


        $arr = filter()->by_user_id(1)->_or(filter()->by_date_gt(1356998400),filter()->by_date_lt(1356998400))->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }


    public function testLogicFilter2(){

        $exp = '(((partner_id = 1) or (name = \'Коля\')) and ((partner_id = 3) or (name = \'Сидоров\')))';



        $arr = filter()->_and(
            filter()->_or(
                filter()->by_partner_id(1),
                filter()->by_name('Коля')
            ),
            filter()->_or(
                filter()->by_partner_id(3),
                filter()->by_name('Сидоров')
            )
        )->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }



}
