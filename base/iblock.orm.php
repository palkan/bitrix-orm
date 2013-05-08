<?php
/**
 * User: palkan
 * Date: 18.04.13
 * Time: 16:35
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/bitrix.orm.php');


if(class_exists('CModule')){
    \CModule::IncludeModule('iblock');
}

class IBlockORM extends BitrixORM{

    const HAS_ID = true;

    //---- Begin: Common fields ----//


    protected $_id;

    protected $_active = true;

    protected $_date_active_from;

    protected $_date_active_to;

    protected $_name;

    protected $_preview_text;

    protected $_description;

    protected $_created_by;

    protected $_modified_by;

    protected $_created_at;

    protected $_updated_at;


    //---- End: Common fields ----//

    function __construct(IBlockORMMap $_map){
        $this->map = $_map;
        $this->created_at = time();
    }


    /**
     * Element ID
     *
     * @return int
     */

    public function id(){ return $this->_id;}

    /**
     * Element activity
     */

    public function active($val = null) {return $this->_commit(__FUNCTION__,$val);}

    /**
     * Date active from (UTC)
     */

    public function date_active_from($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Date active to (UTC)
     */

    public function date_active_to($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function name($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Element's 'PREVIEW_TEXT'
     */

    public function preview_text($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Elements 'DETAIL_TEXT'
     */

    public function description($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     * Created by (user id)
     */

    public function created_by($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Modified by (user id)
     */

    public function modified_by($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Created at ('DATE_CREATE') (UTC)
     */

    public function created_at($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Updated at ('TIMESTAMP_X') (UTC)
     */

    public function updated_at($val = null){return $this->_commit(__FUNCTION__,$val);}



    protected function __Load($arFilter,$arSort,$arNav,$arSelect){

        $arFilter['IBLOCK_ID'] = $this->map->iblock_id;

        return \CIBlockElement::GetList($arSort,$arFilter,false,$arNav,$arSelect);

    }


    public function delete(){

        if(is_null($this->id)) return false;

        if(!\CIBlockElement::Delete($this->id)){
            return false;
        }

        return true;
    }


    protected function _update(){

        $el = new \CIBlockElement();

        $data = $this->map->fields_to_update($this);

        $arFields = $data->fields;

        if(count($data->props))

            $arFields['PROPERTY_VALUES'] = $data->props;

        if($el->Update($this->id, $arFields)) return $this;


        return false;
    }


    protected function _save(){

        $el = new \CIBlockElement();

        $data = $this->map->fields_to_create($this);

        $arFields = $data->fields;
        $arFields['IBLOCK_ID'] = $this->map->iblock_id;
        $arFields['PROPERTY_VALUES'] = $data->props;

        if(defined('LOGGER')) Logger::print_debug($arFields);

        if($ID = $el->Add($arFields)){
            $this->_id = intval($ID);
            $this->_created = true;
            return $this;
        }

        return false;
    }


    public function jsonData(){ return $this;}

    public function changes(){

        $all_props = array_map(function($p){ return $p['name'];}, $this->map->props);

        $changes = parent::changes();

        if(count(array_intersect($all_props,$changes)) > 0) $changes = array_merge($all_props,$changes);

        return $changes;
    }


}



class IBlockORMMap extends BitrixORMMap{

    public $type = BitrixORMMapType::IBLOCK;

    /**
     *
     * Bitrix IBlock ID.
     *
     * @var int
     */

    public $iblock_id = 0;

    public $assign_code = 'NULL';

    protected $prop_prefix = 'PROPERTY_';
    protected $prop_suffix = '_VALUE';

    public $fields = array(
        array('bname' => 'ID', 'name' => 'id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'NAME', 'name' => 'name', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'ACTIVE', 'name' => 'active', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'DATE_ACTIVE_FROM', 'name' => 'date_active_from', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'DATE_ACTIVE_TO','name' => 'date_active_to', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'PREVIEW_TEXT', 'name' => 'preview_text', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'DETAIL_TEXT', 'name' => 'description', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'DATE_CREATE', 'name' => 'created_at', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'CREATED_BY', 'name' => 'created_by', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'TIMESTAMP_X', 'name' => 'updated_at', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'MODIFIED_BY', 'name' => 'modified_by', 'type' => BitrixORMDataTypes::INT)
    );

}