<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 14:26
 */

require_once(dirname(__FILE__).'/test.orm.php');

class BFilterUserTest extends PHPUnit_Framework_TestCase {


    /**
     * @var BitrixORMMap
     */

    protected  static $map;


    public static function setUpBeforeClass()
    {

        self::$map = new TestUserORMMap();

    }


    public function testSimpleFilter(){
        $exp = array(
            'ID' => '(1 | 2)',
            'NAME' => '%vasya'
        );

        $arr = filter()->by_id(1,2)->by_name('%vasya')->toArray(self::$map);

        $this->assertEquals($exp,$arr);
    }




    public function testSimpleFilter2(){

        $exp = array(
            'ID' => '(1 | 2)',
            'NAME' => '%vasya%',
            'LAST_LOGIN_1' => "01.01.2013 00:00:00",
            'LAST_LOGIN_2' => "01.01.2013 00:00:00",
            'ACTIVE' => 'Y'
        );



        $arr = filter()->by_id(1,2)->by_name('%vasya%')->by_last_login_between(1356998400,1356998400)->by_active(true)->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }


    public function testLogicFilter(){

        $exp = array(
            'ID' => 1,
            'NAME' => '(%vasya% | ~Иван)',
        );


        $arr = filter()->by_id(1)->_or(filter()->by_name('%vasya%'),filter()->by_last_name_not('Иван'))->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }


    public function testLogicFilter2(){

        $exp = array(
           'NAME' => '((Вася | Коля) & (Иванов | Сидоров))',
        );



        $arr = filter()->_and(
            filter()->_or(
                filter()->by_name('Вася'),
                filter()->by_name('Коля')
            ),
            filter()->_or(
                filter()->by_last_name('Иванов'),
                filter()->by_last_name('Сидоров')
            )
        )->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }



    public function testLoginFilter(){

        $exp = array(
           'LOGIN_EQUAL' => '(vova@dem.ru | vova@dem.com)'
        );



        $arr = filter()->by_login('vova@dem.ru','vova@dem.com')->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }


    public function testLoginFilter2(){

        $exp = array(
            'LOGIN' => '(vova | vovva)'
        );



        $arr = filter()->by_login('vova%','%vovva%')->toArray(self::$map);

        $this->assertEquals($exp,$arr);

    }

}
