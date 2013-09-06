<?php
/**
 * Created by IntelliJ IDEA.
 * User: palkan
 * Date: 9/2/13
 * Time: 3:15 PM
 * To change this template use File | Settings | File Templates.
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../base/i.serializable.php');


class File implements  tSerializable{

    private $_path='';
    private $_name='';
    private $_origName='';
    private $_size = 0;
    private $_mime = '';
    private $_ts = 0;

    private $_width = 0;
    private $_height = 0;

    function __construct(){

    }


    /**
     *
     * Fill properties from bitrix-style array <code>CFile::GetFileArray($id)</code>.
     *
     * @param $data
     * @return $this
     */


    public function fromBitrixData($data){


        $this->_name = $data['FILE_NAME'];
        $this->_mime = $data['CONTENT_TYPE'];
        $this->_origName = $data['ORIGINAL_NAME'];
        $this->_path = $data['SRC'];
        $this->_size = intval($data['FILE_SIZE']);

        $this->_ts = strtotime($data['TIMESTAMP_X']);

        $this->_width = intval($data['WIDTH']);
        $this->_height = intval($data['HEIGHT']);

        return $this;

    }

    /**
     * Return file array (web-server style): name, size, tmp_name, type.
     *
     * @return array
     */

    public function toFileArray(){
        return array(
            "name" => $this->_origName,
            "size" => $this->_size,
            "tmp_name" => $this->_path,
            "type" => $this->_mime
        );
    }

    public function jsonData(){

        $data = new \stdClass();

        foreach(get_object_vars($this) as $prop => $val){
            $propName = substr($prop,1);
            $data->$propName = $val;
        }

        return $data;

    }



    public function __get($name){

        if(in_array("_".$name, get_object_vars($this))){
            $prop = "_".$name;
            return $this->$prop;
        }

    }


}