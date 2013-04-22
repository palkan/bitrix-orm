<?php
/**
 * User: palkan
 * Date: 18.04.13
 * Time: 16:35
 */


require_once(dirname(__FILE__).'/i.serializable.php');

if(class_exists('CModule')){
    CModule::IncludeModule('iblock');
}

class BitrixORM implements tSerializable{

    /**
     * @var BitrixORMMap
     */

    protected $map;

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

    function __construct(BitrixORMMap $_map){
        $this->map = $_map;
    }


    /**
     * @param BFilter $filter
     * @param BNav $navigation
     * @param int $flags
     * @return mixed
     */


    public static function find(BFilter $filter = null, BNav &$navigation = null, $flags = 0){

        $instance = new static();

        $arFilter = $filter ? $filter->toArray($instance->map) : array();

        $arFilter['IBLOCK_ID'] = $instance->map->iblock_id;

        $arSelect = $instance->map->GetSelectFields();

        $arNav = $navigation ? $navigation->toArray(): false;

        $arSort = $navigation ? $navigation->sortArray($instance->map) : false;

        $resArr = CIBlockElement::GetList($arSort,$arFilter,false,$arNav,$arSelect);

        $results = array();


        if(defined('LOGGER')) Logger::print_debug($arFilter);

        while($arElement = $resArr->Fetch())
        {
            $el = new static();
            $el->fromBitrixData($arElement);

            $results[$el->id] = $el;
        }

        if($navigation){
            $navigation->total_pages = $resArr->NavPageCount;
            $navigation->total_records = $resArr->NavRecordCount;
        }

        return $results;
    }


    public function delete(){

    }

    public function all(){

    }


    public function save(){

        return $this;
    }


    public function jsonData(){ return $this;}


    /**
     * Initialize object with bitrix data.
     *
     * @param $data
     * @return $this
     */


    public function fromBitrixData($data){
        $this->map->initialize($this,$data);
        return $this;
    }



}



class BitrixORMMap{

    /**
     *
     * Bitrix IBlock ID.
     *
     * @var int
     */

    public $iblock_id = 0;


    /**
     *
     * Array of properties descriptions.
     *
     * @var array|Null
     */

    public $props;


    /**
     *
     * Array of dependencies descriptions.
     *
     * Dependencies are used only when deleting element.
     *
     * @var array|Null
     */


    public $deps;


    /**
     *
     * Main IBlockElement fields.
     *
     * @var array
     */

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



    protected $rules = array();
    protected $bname2name = array();


    function __construct(){

        foreach($this->fields as $f){

            $r = new BMapRule($f['bname'], $f['name'], $f['type']);

            $this->rules[$f['name']] = $r;

            $this->bname2name[$f['bname']] = $f['name'];

        }

        if($this->props){
            foreach($this->props as $p){

                $r = new BMapRule($p['bname'], $p['name'], $p['type'], true, (isset($p['scheme']) ? $p['scheme'] : null));

                $this->rules[$p['name']] = $r;

                $this->bname2name[$p['bname']] = $p['name'];

            }
        }

        //TODO: add dependencies

    }


    public function GetSelectFields(){
         return array_keys($this->bname2name);
    }



    public function initialize(BitrixORM &$ormObject, $data){


        foreach($this->rules as $rule){

            $field = $rule->isProperty ? $rule->bitrixName.'_VALUE' : $rule->bitrixName;

            if(isset($data[$field])){
                $ormName = $rule->ormName;
                $ormObject->$ormName = $rule->toORM($data[$field]);

            }

        }

    }


    /**
     *
     * Return Bitrix key name by ORM key name
     *
     * @param $key
     * @return string|null
     */


    public function GetBitrixKey($key){

        if(!isset($this->rules[$key])) return null;

        return $this->rules[$key]->bitrixName;

    }


    /**
     *
     * Return ORM key name by Bitrix key name
     *
     * @param $key
     * @return string|null
     */


    public function GetORMKey($key){

        if(!isset($this->bname2name[$key])) return null;

        return $this->bname2name[$key];

    }


    /**
     *
     * Return bitrix-style array: <code> array('BITRIX_FIELD_NAME'=>value)</code>
     *
     * @param $field   ORM field name
     * @param $val     value
     * @return stdClass|null
     */

    public function GetBitrixFieldValue($field,$val){

        if(!isset($this->rules[$field])) return null;

        $rule = $this->rules[$field];

        $data = new stdClass();

        $data->key = $rule->bitrixName;
        $data->value = is_null($val) ? false : $rule->fromORM($val);

        return $data;

    }

    /**
     *
     * Return ORM adopted object containing key and value..
     *
     * @param $bfield  Bitrix field name
     * @param $bvalue
     * @return null|stdClass
     */

    public function GetORMFieldValue($bfield,$bvalue){

        if(!isset($this->bname2name[$bfield])) return null;

        $field = $this->bname2name[$bfield];

        $rule = $this->rules[$field];

        $data = new stdClass();

        $data->key = $rule->ormName;
        $data->value = $rule->toORM($bvalue);

        return $data;


    }

}


class BMapRule{

    public $bitrixName;
    public $ormName;

    public $isProperty;

    /**
     * Data type: int, string, datetime, bool, enum
     *
     * @var
     */

    public $type;

    /**
     *
     * @var BEnumScheme
     */

    public $enum_scheme;

    function __construct($bname, $name, $type, $isProperty = false, $data = null){

        $this->bitrixName = $bname;
        $this->ormName = $name;
        $this->type = $type;
        $this->isProperty = $isProperty;

        if($this->type === 'enum' && $data) $this->enum_scheme = new BEnumScheme($data);


    }


    public function fromORM($val){
       return  $this->convert($val,'from');
    }


    public function toORM($val){
       return  $this->convert($val,'to');
    }

    private function convert($val,$how){

        // if type is not 'enum' then we convert each element independently;
        // 'enum' fields as array present only in filter and we have to handle them separately

        if(is_array($val) && $this->type!=='enum'){
            $arr = array();
            foreach($val as $v){
                $arr[] =  call_user_func(array($this, $how.'_'.$this->type),$v);
            }

            return $arr;
        }else
            return call_user_func(array($this,$how.'_'.$this->type),$val);

    }


    private function to_int($val){  return intval($val);   }
    private function from_int($val){  return intval($val);   }
    private function to_string($val){  return html_entity_decode($val,ENT_NOQUOTES,'UTF-8'); }
    private function from_string($val){  return htmlentities($val,ENT_NOQUOTES,'UTF-8'); }
    private function to_datetime($val){  return strtotime($val);   }
    private function from_datetime($val){  return date('d.m.Y H:i:s',$val);  }
    private function to_bool($val){  return ($val === "Y");   }
    private function from_bool($val){  return $val ? "Y" : "N";   }
    private function to_object($val){  return unserialize($val);   }
    private function from_object($val){  return serialize($val);   }

    private function to_enum($val){
        if(!$this->enum_scheme) return null;
        return $this->enum_scheme->toORM($val);
    }

    private function from_enum($val){
        if(!$this->enum_scheme) return null;
        return $this->enum_scheme->fromORM($val);
    }

}


class BEnumScheme{

    private $orm_enum = array();
    private $bitrix_enum_ids = array();
    private $bitrix_enum = array();

    private $type;


    function __construct(array $data_array){


        $this->type = $data_array['type'];

        foreach($data_array['list'] as $data){

            array_push($this->orm_enum,$data['value']);
            array_push($this->bitrix_enum, $data['bvalue']);
            array_push($this->bitrix_enum_ids, $data['enum_id']);

        }

    }

    /**
     *
     * @param $val Bitrix property value
     * @return mixed|null
     */

    public function toORM($val){

        if(($key = array_search($val,$this->bitrix_enum))!==false)
            return $this->orm_enum[$key];
        else
            return null;
    }

    /**
     * Return Bitrix-style array fo enum: <code> array('ENUM_ID' => id);</code>
     *
     * @param $val ORM field value
     * @return array|null
     */

    public function fromORM($val){

        $ids = array();

        if(!is_array($val)) $val = array($val);

        foreach($val as $v){
            if(($key = array_search($v,$this->orm_enum,true))!==false)
                $ids[] = $this->bitrix_enum_ids[$key];
        }

        if(!count($ids)) return null;

        if(count($ids) === 1){
            return array('ENUM_ID' => $ids[0]);
        }

        return $ids;
    }



}


/**
 *
 * Class representing navigation and sorting options.
 *
 * Use magic method <code>order_by_<i>property_name</i></code> for sorting.
 *
 * Class BNav
 */


class BNav{

    private $limit;
    private $page;


    public $total_records;
    public $total_pages;


    private $sort = array();


    /**
     * Set limit (or page size).
     *
     * If limit is set to 0 then no limit.
     *
     * @param int $size
     * @return BNav $this
     */

    public function limit($size){
        $this->limit = $size;
        return $this;
    }


    /**
     * If page number is set to 0 then no pagination.
     *
     * @param int $num  page number
     * @return BNav $this
     */


    public function page($num){
        $this->page = $num;
        return $this;
    }


    public function __call($method,$args){

        $matches = array();

        if(preg_match('/^order_by_([\w\d]+)$/i',$method,$matches)){
           $this->sort[$matches[1]] = $args[0];
        }

        return $this;
    }


    /**
     * Return bitrix-style navigation array
     *
     * @return array|boolean
     */

    public function toArray(){

        $arNav = false;

        if($this->limit && $this->page){
            $arNav = array(
                    "iNumPage" => $this->page,
                    "nPageSize" => $this->limit
                );
        }elseif($this->limit){
            $arNav = array(
                "nTopCount" => $this->limit
            );
        }

        return $arNav;
    }

    /**
     *
     * Return bitrix-style sort array.
     *
     * @param BitrixORMMap $map
     * @return array
     */

    public function sortArray(BitrixORMMap $map){

        $arSort = array();

        foreach($this->sort as $key => $order){

             if(is_null($bkey = $map->GetBitrixKey($key))) continue;

             $arSort[$bkey] = $order;

        }

        return $arSort;

    }

}


/**
 *
 * BFilter class contains rules to use as arFilter with CIBlockElement::GetList.
 *
 * To add a rule to filter use magic calls: <code> $f = new BFilter; <br/> $f->by_#{field_name}(_((b|e)?like|between|not|gt(e)?|lt(e)?))?(#{args});</code>
 *
 * For example:
 * <code>
 *
 * $f = new BFilter;
 *
 * // filter by user id and by name likeness (Bitix: array('USER_ID'=>1212,'NAME'=>'%vasya%'))
 *
 * $f->by_user_id(1212)->by_name_like('vasya');
 *
 * // filter by date period   (Bitix: array('><DATE_CREATE' => array('10.10.2012','01.01.2013')))
 *
 * $f->by_created_at_between('2012-10-10','2013-01-01');
 *
 * // filter with logic (Bitix: array(array('LOGIC'=>'OR', array('CREATED_BY' => 1), array('MODIFIED_BY' => 1)))
 *
 * $f->by_created_by(1)->_or()->by_modified_by(1);
 *
 * // filter with complex logic and grouping  (Bitix: array(array('LOGIC'=>'OR', array('CREATED_BY' => 1, 'MODIFIED_BY' => 1), array('!ACTIVITY'=>'Y'))
 *
 * $f->by_created_by(1)->_and()->by_modified_by(1)->_or()->by_active_not(true);
 *
 * </code>
 *
 *
 * Logic functions priority:
 *
 * <code> _and() > _or() </code>
 *
 * Class BFilter
 */


class BFilter{

    private $data;

    function __construct(){
        $this->data = new SplStack();
    }

    /**
     *
     * Generate arFilter array for GetList.
     *
     * If $user is set to true, then genegate $arFilter for CUser.
     *
     * @param BitrixORMMap $map
     * @param bool $user
     * @return array
     */

    public function toArray(BitrixORMMap $map, $user = false){

        $filter = array();

        if($this->data->count() === 1) return $this->data->pop()->toArray($map, $user);

        while(!$this->data->isEmpty()){

            $el = $this->data->pop();

            if(get_class($el) === 'BFilterGroup' && !$user) array_push($filter,$el->toArray($map,$user));
            else $filter = array_merge($filter,$el->toArray($map,$user));
        }

        return $filter;
    }


    /**
     *
     * @param string $type 'or' | 'and'
     * @param array $args filters
     * @return $this
     */


    private function _group($type,$args){

        $group = new BFilterGroup($type);

        foreach($args as $filter){
            $group->push($filter);
        }

        $this->data->push($group);

        return $this;

    }

    /**
     * @return $this
     */


    public function _and(){

        $args = func_get_args();

        return $this->_group('and',$args);

    }


    /**
     * @return $this
     */

    public function _or(){

        $args = func_get_args();

        return $this->_group('or',$args);
    }

    /**
     * @param $_name
     * @param $_args
     * @return $this
     */

    public function __call($_name,$_args){

        $matches = array();

        if(preg_match('/^by_((?:[a-z\d]|[a-z\d]_)+)(?:_(between|not|gt(?:e)?|lt(?:e)?))?$/i',$_name,$matches)){

            $field = $matches[1];

            $el = null;

            if(count($_args) === 1) $_args = $_args[0];

            if(isset($matches[2]) && method_exists($this,'push_'.$matches[2])) $el = call_user_func(array($this, 'push_'.$matches[2]),$field,$_args);
            else $el = new BFilterElement($field,$_args);

            if(!$el) return $this;

            $this->data->push($el);

        }

        return $this;


    }


    private function push_between($field,$args){
        if(count($args)!=2) return null;
        return new BFilterElement($field,$args,'><');
    }

    private function push_not($field,$args){ return new BFilterElement($field,$args,'!');}
    private function push_gt($field,$args){ return new BFilterElement($field,$args,'>');}
    private function push_gte($field,$args){ return new BFilterElement($field,$args,'>=');}
    private function push_lt($field,$args){ return new BFilterElement($field,$args,'<');}
    private function push_lte($field,$args){ return new BFilterElement($field,$args,'<=');}

}


class BFilterElement{

    public $field;
    public $value;

    public $prefix;

    function __construct($field,$value, $prefix =''){
        $this->field = $field;
        $this->value = $value;
        $this->prefix = $prefix;
    }


    public function toArray(BitrixORMMap $map, $user = false){

        $result = array();

        if(!$user){

            $data = $map->GetBitrixFieldValue($this->field,$this->value);
            $result = array($this->prefix.$data->key => $data->value);

        }else{

            $result = $map->GetBitrixUserFieldValue($this->field,$this->value,$this->prefix);

        }



        return $result;

    }

}


/**
 * Class BFilterGroup
 */


class BFilterGroup{

    public $logic;

    public $data = array();


    /**
     *
     * Create new filter group
     *
     * @param string $logic 'or' or 'and'
     * @param mixed|null $first
     */

    function __construct($logic,$first = null){

        $this->logic = $logic;

        if($first) array_push($this->data,$first);

    }

    /**
     *
     * Add element to group
     *
     * @param mixed $el
     */

    public function push($el){

        array_push($this->data,$el);

    }

    public function toArray(BitrixORMMap $map, $user = false){

        if(!$user){

            $arr = array('LOGIC' => strtoupper($this->logic));

            foreach($this->data as $data){
                array_push($arr,$data->toArray($map,$user));
            }

        }else{

            $arr = array();

            foreach($this->data as $data){
                $tmp_arr = $data->toArray($map,$user);
                array_push($arr,current($tmp_arr));
            }

            $del = ($this->logic === 'or') ? ' | ' : ' & ';
            $keys = array_keys($tmp_arr);
            $arr = array($keys[0] => '('.implode($del,$arr).')');

        }

        return $arr;

    }

}


//--------- Global functions (helpers) & constants ---------//



define(B_LOAD_DEPENDENCIES,1);


/**
 *
 * Create new BFilter.
 *
 * @return BFilter
 */

function filter(){

    return new BFilter();

}

/**
 *
 * Create new BNav
 *
 * @return BNav
 */

function navi(){

    return new BNav();

}

/**
 *
 * Return json serialized object's <code>jsonData</code>
 *
 * @param tSerializable $object
 * @return string
 */


function to_json(tSerializable $object){
    return json_encode($object->jsonData());
}
