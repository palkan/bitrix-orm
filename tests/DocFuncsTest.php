<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 10:48
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../document/document.php');

class DocFuncsTest extends \PHPUnit_Framework_TestCase {


    public static function setUpBeforeClass()
    {

    }


    public function providerNameExt()
    {
        return array(
            array('example.txt', 'example', 'txt'),
            array('example', 'example', ''),
            array('var/var/example.txt', 'example', 'txt'),
            array('var/var.tb.var/example.txt', 'example', 'txt'),
            array('example.mp4', 'example', 'mp4'),
            array('http://we.er/wee/et/example.txt', 'example', 'txt'),
            array('example.txt', 'example', 'txt'),
            array('example.txt/?wewe/', 'example.txt/?wewe/', '')
        );
    }


    /**
     * @dataProvider providerNameExt
     */


    public function testPathNameExt($path,$name,$ext)
    {

        $exp = new \stdClass();
        $exp->name = $name;
        $exp->ext = $ext;

        $this->assertEquals($exp,Document::path2name_ext($path));

    }



    public function providerExtType()
    {
        return array(
            array('rtf', DocumentType::DOCUMENT),
            array('docx', DocumentType::DOCUMENT),
            array('xls', DocumentType::TABLE),
            array('ppt', DocumentType::PRESENTATION),
            array('mp4', DocumentType::VIDEO),
            array('mp3', DocumentType::AUDIO),
            array('png', DocumentType::IMAGE),
            array('', DocumentType::OTHER),
            array('rtfsw', DocumentType::OTHER)
        );
    }


    /**
     * @dataProvider providerExtType
     */


    public function testExtType($ext,$type)
    {
        $this->assertEquals($type,DocumentType::extension2type($ext));

    }


}
