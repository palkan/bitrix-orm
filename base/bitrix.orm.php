<?php
/**
 * User: palkan
 * Date: 18.04.13
 * Time: 16:35
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/i.serializable.php');
require_once(dirname(__FILE__).'/../utils/utils.php');
require_once(dirname(__FILE__).'/../utils/file.php');

abstract class BitrixORM implements tSerializable{


    private static $maps = array();


    /**
     *
     * Reference to the shared map within instance.
     *
     * @var
     */

    public $mapref;

    /**
     *
     * We use static storage (<i>cache</i>) to prevent from excess loading of objects by id (e.g. when using <i>quiz()</i> function of Question).
     *
     * You can't load the same object twice using <i>find_by_id()</i> but by using <i>find(filter())</i>.
     *
     * However if you loaded object with <i>find()</i> method it is stored in cache too.
     *
     * @see find()
     * @see find_by_id()
     *
     * @var array
     *
     */

    protected static $__storage = array();

    private $_changes = array();

    protected $_created = false;

    function __construct(){

        $this->mapref = self::$maps[get_class($this)];

    }


    /**
     * @param $map
     * @param $classname
     * @throws \Exception
     */

    public static function registerMapClass(BitrixORMMap $map, $classname){

        if(isset(self::$maps[$classname])) throw new \Exception('Class has been already registered: '.$classname);

        self::$maps[$classname] = $map;

    }

    /**
     * @return string
     */

    public static function className(){
        return get_called_class();
    }


    /**
     *
     * Find element by ID.
     *
     * Shorthand for <code>reset(find(filter()->by_id($id)))</code>
     *
     * @see find
     *
     * @param int $id
     * @return bool|mixed
     */

    public static function find_by_id($id){

        if(!intval($id)) return false;

        if(static::fromCache($id)) return static::fromCache($id);

        $res = static::find(filter()->by_id($id));

        if(count($res)) return static::cache(reset($res));
        else return false;

    }


    /**
     * @param BitrixORM $object
     * @return BitrixORM
     */


    private static function cache(BitrixORM $object){
        static::$__storage[$object->id] = $object;
        return $object;
    }


    /**
     * @param $id
     * @return bool
     */

    private static function fromCache($id){
        return isset(static::$__storage[$id]) ? static::$__storage[$id] : false;
    }



    protected function __Load($arFilter,$arSort,$arNav,$arSelect){}


    /**
     * @param BFilter $filter
     * @param BNav $navigation
     * @param int $flags
     * @return mixed
     */


    public static function find(BFilter $filter = null, BNav &$navigation = null, $flags = 0){

        $instance = new static();

        $arFilter = $filter ? $filter->toArray($instance->mapref) : array();

        $arSelect = $instance->mapref->GetSelectFields();

        $arNav = $navigation ? $navigation->toArray(): false;

        $arSort = $navigation ? $navigation->sortArray($instance->mapref) : false;

        $resArr = $instance->__Load($arFilter,$arSort,$arNav,$arSelect);

        $results = array();

        while($arElement = $resArr->Fetch())
        {
           // Logger::print_debug($arElement);

            $el = new static();
            $el->fromBitrixData($arElement);
            $el->_created = true;
            $el->_flush();

            if($instance->mapref->has_id){
                $el->_id = intval($arElement[$instance->mapref->GetBitrixKey('id')]);

                $results[$el->_id] = static::cache($el);
            }else
                $results[] = $el;
        }

        if($navigation){
            $navigation->total_pages = $resArr->NavPageCount;
            $navigation->total_records = $resArr->NavRecordCount;
        }

        return $results;
    }

    /**
     * Return all elements
     * @return mixed
     */

    public static function all(){
        return static::find();
    }


    /**
     * @return bool
     */

    public function delete(){
      return true;
    }


    /**
     * @return $this
     */

    public function save(){
        $res = false;
        if($this->_created) $res =  $this->_update();
        else $res = $this->_create();

        if(!$res) return false;

        $this->_flush();
        $this->_created = true;
        return $this;
    }


    protected function _update(){
        return $this;
    }


    protected function _create(){
        return $this;
    }

    public function jsonData(){
        return $this->mapref->jsonObject($this);
    }


    /**
     * Initialize object with bitrix data.
     *
     * @param $data
     * @return $this
     */


    public function fromBitrixData($data){
        $this->mapref->initialize($this,$data);
        return $this;
    }


    public function changes(){
        return array_keys($this->_changes);
    }

    protected function _commit($field,$val){
        $priv_field = '_'.$field;
        if(!is_null($val)){
            $this->_changes[$field] = true;
            $this->$priv_field = $val;
        }

        return $this->$priv_field;
    }


    protected function _flush(){
        $this->_changes = array();
    }


    //--- MAGIC! ---//

 /*
    public function __get($name){
        $_name = $name;
        return $this->$_name;
    }


    public function __set($name,$value){
        return $this->$name($value);
    }
   */

}



class BitrixORMMapType{

    const USER = 'user';
    const IBLOCK = 'iblock';
    const CUSTOM = 'custom';

}


class BitrixORMDataTypes{

    const INT = 'int';
    const STRING = 'string';
    const DATETIME = 'datetime';
    const BOOL = 'bool';
    const ENUM = 'enum';
    const OBJECT = 'object';
    const JSON = 'json';
    const FILE = 'file';


    public static function IsStringType($type){

        return in_array($type,array(self::STRING,self::DATETIME,self::BOOL));

    }

}

//TODO: default values and function default values (e.g. now());

class BitrixORMMap{


    public $type;

    public $has_id = false;


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

    public $fields;



    protected $rules = array();
    protected $bname2name = array();


    protected $prop_prefix = '';
    protected $prop_suffix = '';


    function __construct(){

        foreach($this->fields as $f){

            $r = new BMapRule($f['bname'], $f['name'], $f['type'], false, (isset($f['data']) ? $f['data'] : null));

            $this->rules[$f['name']] = $r;

            $this->bname2name[$f['bname']] = $f['name'];

        }

        if($this->props){
            foreach($this->props as $p){

                $r = new BMapRule($p['bname'], $p['name'], $p['type'], true, (isset($p['data']) ? $p['data'] : null));

                $this->rules[$p['name']] = $r;

                $this->bname2name[$p['bname']] = $p['name'];

            }
        }

        //TODO: add dependencies

    }


    public function GetSelectFields(){
         $prefix = $this->prop_prefix;
         return array_map(function ($rule) use ($prefix){ return $rule->isProperty ? $prefix.$rule->bitrixName : $rule->bitrixName;}, $this->rules);
    }



    public function initialize(BitrixORM &$ormObject, $data){

        foreach($this->rules as $rule){

            $field = $rule->isProperty ? $this->prop_prefix.$rule->bitrixName.$this->prop_suffix : $rule->bitrixName;

            if(isset($data[$field])){
                $ormName = $rule->ormName;
                $ormObject->$ormName($rule->toORM($data[$field]));
            }

        }

    }

    /**
     * @param BitrixORM $ormObject
     * @return UpdateData
     */

    public function fields_to_update(BitrixORM $ormObject){

        $data = new UpdateData();

        foreach($ormObject->changes() as $ormName){

            $rule = $this->rules[$ormName];

            if($rule->isProperty)
                $data->props[$rule->bitrixName] = $rule->fromORM($ormObject->$ormName());
            else
                $data->fields[$rule->bitrixName] = $rule->fromORM($ormObject->$ormName());
        }

        return $data;
    }


    /**
     * @param BitrixORM $ormObject
     * @return UpdateData
     */

    public function fields_to_create(BitrixORM $ormObject){

        $data = new UpdateData();

        foreach($this->rules as $rule){

            $ormName = $rule->ormName;
            $val = $ormObject->$ormName();

            if(is_null($val)) continue;

            if($rule->isProperty)
                $data->props[$rule->bitrixName] = $rule->fromORM($val);
            else
                $data->fields[$rule->bitrixName] = $rule->fromORM($val);
        }

        return $data;
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

        return $this->rules[$key]->isProperty ? $this->prop_prefix.$this->rules[$key]->bitrixName : $this->rules[$key]->bitrixName;

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
     * Return ORM adopted object containing key and value..
     *
     * @param $bfield string Bitrix field name
     * @param $bvalue mixed
     * @return null|\stdClass
     */

    public function GetORMFieldValue($bfield,$bvalue){

        if(!isset($this->bname2name[$bfield])) return null;

        $field = $this->bname2name[$bfield];

        $rule = $this->rules[$field];

        $data = new \stdClass();

        $data->key = $rule->ormName;
        $data->value = $rule->toORM($bvalue);

        return $data;


    }

    /**
     *
     * Return object containing Bitrix key and prepared value.
     *
     * @param $field
     * @param $value
     * @return null|\stdClass
     */


    public function GetBitrixFieldValue($field,$value){

        if(!isset($this->rules[$field])) return null;

        $rule = $this->rules[$field];

        $data = new \stdClass();

        $data->key = $rule->isProperty ? $this->prop_prefix.$rule->bitrixName : $rule->bitrixName;
        $data->value = is_null($value) ? false : $rule->fromORM($value);

        return $data;

    }



    /**
     *
     * Return bitrix-style array: <code> array('BITRIX_FIELD_NAME'=>value)</code>
     *
     * @param BFilterElement
     * @return array|null
     */

    public function PrepareFilterElement(BFilterElement $filter){

        if(!($data = $this->GetBitrixFieldValue($filter->field,$filter->value))) return null;

        return array($filter->prefix.$data->key => $data->value);

    }


    /**
     * @param BFilterGroup $filter
     * @return array
     */

    public function PrepareGroupFilter(BFilterGroup $filter){
        $arr = array('LOGIC' => strtoupper($filter->logic));

        foreach($filter->data as $data){
            array_push($arr,$data->toArray($this));
        }

        return $arr;
    }

    /**
     *
     * Generate object containing all fields of ORM object (for printing or to_json)
     *
     * @param BitrixORM $target
     * @return \stdClass
     */

    public function jsonObject(BitrixORM $target){

        $return = new \stdClass();

        foreach($this->rules as $rule){

            $field = $rule->ormName;

            $return->$field = $target->$field();
        }

        return $return;

    }

}


/**
 *
 * Contains fields and props translated to "bitrix".
 *
 * Use with <i>_update</i> and <i>_create</i>.
 *
 * Class UpdateData
 * @package ru\teachbase
 */

class UpdateData{

    public $fields = array();

    public $props = array();

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


    /**
     *
     * Datetime format
     * @var  string
     */


    public $fmt;

    function __construct($bname, $name, $type, $isProperty = false, $data = null){

        $this->bitrixName = $bname;
        $this->ormName = $name;
        $this->type = $type;
        $this->isProperty = $isProperty;

        if($this->type === BitrixORMDataTypes::ENUM && $data) $this->enum_scheme = new BEnumScheme($data);

        if($this->type === BitrixORMDataTypes::DATETIME) $this->fmt = is_null($data) ? 'd.m.Y H:i:s' : $data;


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

        if(is_array($val) && $this->type!==BitrixORMDataTypes::ENUM){
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
    private function from_datetime($val){  return date($this->fmt,$val);  }
    private function to_bool($val){  return ($val === "Y");   }
    private function from_bool($val){  return $val ? "Y" : "N";   }
    private function to_object($val){  return unserialize($val);   }
    private function from_object($val){  return serialize($val);   }
    private function to_json($val){  return json_decode($val,false);   }
    private function from_json($val){  return json_encode($val);   }


    private function to_file($val){
        if(!intval($val) || !class_exists('CFile')) return null;
        $f = new File();
        return  $f->fromBitrixData(CFile::GetFileArray(intval($val)));
    }

    private function from_file($val){
        if(is_null($val)) return null;
        return $val->toFileArray();
    }


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
     * @param $val mixed Bitrix property value
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
     * @param $val mixed ORM field value
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
        $this->data = new \SplStack();
    }

    /**
     *
     * Generate arFilter array for GetList.
     *
     * If $map->type  is BitrixORMMapType::USER, then generate $arFilter for CUser.
     *
     * If $map->type  is BitrixORMMapType::CUSTOM, then array of conditions (strings).
     *
     *
     * @param BitrixORMMap $map
     * @return array
     */

    public function toArray(BitrixORMMap $map){

        $filter = array();

        if($this->data->count() === 1) return $this->data->pop()->toArray($map);

        while(!$this->data->isEmpty()){

            $el = $this->data->pop();

            if(($map->type === BitrixORMMapType::CUSTOM) || (get_class($el) === 'ru\\teachbase\\BFilterGroup' && $map->type !== BitrixORMMapType::USER)) array_push($filter,$el->toArray($map));
            else $filter = array_merge($filter,$el->toArray($map));
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

        if(preg_match('/^by_((?:[a-z\d]|[a-z\d]_)+)(?:_(between|not|like|not_like|gt(?:e)?|lt(?:e)?))?$/i',$_name,$matches)){

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
        return new BFilterElement($field,$args,'><','between');
    }

    private function push_not($field,$args){ return new BFilterElement($field,$args,'!','not');}
    private function push_like($field,$args){ return new BFilterElement($field,$args,'','like');}
    private function push_not_like($field,$args){ return new BFilterElement($field,$args,'','not_like');}
    private function push_gt($field,$args){ return new BFilterElement($field,$args,'>','greater');}
    private function push_gte($field,$args){ return new BFilterElement($field,$args,'>=','greater_or_equal');}
    private function push_lt($field,$args){ return new BFilterElement($field,$args,'<','less');}
    private function push_lte($field,$args){ return new BFilterElement($field,$args,'<=','less_or_equal');}

}


class BFilterElement{

    public $field;
    public $value;

    public $prefix;
    public $operator;

    function __construct($field,$value, $prefix ='', $operator = null){
        $this->field = $field;
        $this->value = $value;
        $this->prefix = $prefix;
        $this->operator = $operator;
    }


    public function toArray(BitrixORMMap $map){

        $result = $map->PrepareFilterElement($this);

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

    public function toArray(BitrixORMMap $map){

        return $map->PrepareGroupFilter($this);

    }

}


//--------- Global functions (helpers) & constants ---------//

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
