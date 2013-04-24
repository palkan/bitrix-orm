<?php
/**
 * User: palkan
 * Date: 18.04.13
 * Time: 16:35
 */


require_once(dirname(__FILE__).'/bitrix.orm.php');

class IBlockORM extends BitrixORM{

    //---- Begin: Common fields ----//

    /**
     * Element ID
     *
     * @var int
     */

    public $id;

    /**
     *
     * Element activity
     *
     * @var bool
     */

    public $active = true;

    /**
     *
     * Date active from (UTC)
     *
     * @var int
     */

    public $date_active_from;

    /**
     *
     * Date active to (UTC)
     *
     * @var int
     */

    public $date_active_to;

    /**
     * @var string
     */

    public $name;

    /**
     * Element's 'PREVIEW_TEXT'
     *
     * @var string
     */

    public $preview_text;

    /**
     *
     * Elements 'DETAIL_TEXT'
     *
     * @var string
     */

    public $description;


    /**
     * Created by (user id)
     *
     * @var int
     */

    public $created_by;

    /**
     *
     * Modified by (user id)
     *
     * @var int
     */

    public $modified_by;

    /**
     *
     * Created at ('DATE_CREATE') (UTC)
     *
     * @var int
     *
     */

    public $created_at;

    /**
     *
     * Updated at ('TIMESTAMP_X') (UTC)
     *
     * @var  int
     */

    public $updated_at;


    //---- End: Common fields ----//

    function __construct(IBlockORMMap $_map){
        $this->map = $_map;
    }


    protected function __Load($arFilter,$arSort,$arNav,$arSelect){

        $arFilter['IBLOCK_ID'] = $this->map->iblock_id;

        return CIBlockElement::GetList($arSort,$arFilter,false,$arNav,$arSelect);

    }


    public function delete(){

    }

    public function all(){

    }


    public function save(){

        return $this;
    }


    public function jsonData(){ return $this;}



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


    public $fields = array(
        array('bname' => 'ID', 'name' => 'id', 'type' => 'int'),
        array('bname' => 'NAME', 'name' => 'name', 'type' => 'string'),
        array('bname' => 'ACTIVE', 'name' => 'active', 'type' => 'bool'),
        array('bname' => 'DATE_ACTIVE_FROM', 'name' => 'date_active_from', 'type' => 'datetime'),
        array('bname' => 'DATE_ACTIVE_TO','name' => 'date_active_to', 'type' => 'datetime'),
        array('bname' => 'PREVIEW_TEXT', 'name' => 'preview_text', 'type' => 'string'),
        array('bname' => 'DETAIL_TEXT', 'name' => 'description', 'type' => 'string'),
        array('bname' => 'DATE_CREATE', 'name' => 'created_at', 'type' => 'datetime'),
        array('bname' => 'CREATED_BY', 'name' => 'created_by', 'type' => 'int'),
        array('bname' => 'TIMESTAMP_X', 'name' => 'updated_at', 'type' => 'datetime'),
        array('bname' => 'MODIFIED_BY', 'name' => 'modified_by', 'type' => 'int')
    );

}