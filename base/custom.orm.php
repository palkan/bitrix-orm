<?php
/**
 * User: palkan
 * Date: 24.04.13
 * Time: 13:51
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/bitrix.orm.php');

/**
 *
 * ORM model for custom tables.
 *
 * Class CustomORM
 */

class CustomORM extends BitrixORM{


    /**
     * Array of fields that are unique in the table
     *
     * Used while deleting
     *
     * @var
     */

    protected $_unique = array();

    /**
     * @var CustomORMMap
     */

    protected $map;

    function __construct(CustomORMMap $map){
        parent::__construct($map);
    }


    protected function __Load($arFilter,$arSort,$arNav,$arSelect){

        $sqlQuery = "select ".implode(',',$arSelect)." from ".$this->map->table;

        if(is_array($arFilter) && count($arFilter)>0){
            $sqlQuery.=" where ".implode(' and ',$arFilter);
        }elseif(!empty($arFilter))  $sqlQuery.=" where ".$arFilter;


        if(!empty($arSort)){
            foreach($arSort as $key => $sort){
                $sqlQuery.=' order by '.$key.' '.$sort;
            }
        }

        if(!empty($arNav)){
            if(isset($arNav['nTopCount'])){
                $sqlQuery.=' limit '.$arNav['nTopCount'];
            }elseif(isset($arNav['iNumPage'],$arNav['nPageSize'])){
                $sqlQuery.=' limit '.($arNav['nPageSize'] * ($arNav['iNumPage']-1)).', '.$arNav['nPageSize'];
            }
        }

        if(defined('LOGGER')) Logger::print_debug($sqlQuery);

        return $this->query($sqlQuery);
    }



    protected function _save(){

        $sqlStr = "insert into ".$this->map->table;

        $data = $this->map->fields_to_create($this);

        $fields = array_keys($data->fields);
        $values = array_values($data->fields);

        $sqlStr.=' ('.implode(',',$fields).') values ('.implode(',',$values).')';

        if(!$this->query($sqlStr)) return false;

        return $this;
    }


    protected function _update(){

        $sqlStr = 'update '.$this->map->table.' set ';

        $data = $this->map->fields_to_update($this);

        $values = array();

        foreach($data->fields as $key => $val){
            $values[] = $key.' = '.$val;
        }

        $sqlStr.=implode(',',$values).' '.$this->_where_id();

        if(!$this->query($sqlStr)) return false;

        return $this;
    }


    public function delete(){

        $sqlStr = 'delete from '.$this->map->table.' '.$this->_where_id();
        return !!$this->query($sqlStr);

    }



    protected function _where_id(){

        if(static::HAS_ID) return 'where id = '.$this->_id;

        else{

            $str = 'where ';

            $fields = array();

            foreach($this->_unique as $key){

                $field = '_'.$key;

                $fields[] = $key.' = '.$this->$field;

            }

            $str.=implode(' and ',$fields);

            return $str;
        }
    }

    /**
     *
     * Run SQL query (check if DB available too).
     *
     * @param string $sqlString
     * @return bool
     */

    private function query($sqlString){

        global $DB;

        if(!is_object($DB)) return false;

        return $DB->Query($sqlString,false);

    }

}


class CustomORMMap extends BitrixORMMap{

    public $type = BitrixORMMapType::CUSTOM;

    /**
     * Table name.
     *
     * @var
     */

    public $table;


    /**
     * @param CustomORM $ormObject
     * @return UpdateData
     */

    public function fields_to_update(CustomORM $ormObject){

        $data = new UpdateData();

        foreach($ormObject->changes() as $ormName){

            $rule = $this->rules[$ormName];

            $data->fields[$rule->bitrixName] = $rule->fromORM($ormObject->$ormName());

            if(BitrixORMDataTypes::IsStringType($rule->type)) $data->fields[$rule->bitrixName] ='\''.$data->fields[$rule->bitrixName].'\'';

        }

        return $data;
    }


    /**
     * @param CustomORM $ormObject
     * @return UpdateData
     */

    public function fields_to_create(CustomORM $ormObject){

        $data = new UpdateData();

        foreach($this->rules as $rule){

            $ormName = $rule->ormName;

            $data->fields[$rule->bitrixName] = $rule->fromORM($ormObject->$ormName());

            if(BitrixORMDataTypes::IsStringType($rule->type)) $data->fields[$rule->bitrixName] ='\''.$data->fields[$rule->bitrixName].'\'';
        }

        return $data;
    }


    public function PrepareFilterElement(BFilterElement $filter){

        if(!$filter->prefix || empty($filter->prefix)) $filter->prefix = '=';
        if($filter->prefix === '!') $filter->prefix = '<>';

        $result = '';

        $data = $this->GetBitrixFieldValue($filter->field,$filter->value);

        if(!$data) return;

        $result = $data->key;

        $rule = $this->rules[$filter->field];

        // add quotes if needed

        if(BitrixORMDataTypes::IsStringType($rule->type)){

            $quotes = function(&$str){ $str = '\''.$str.'\'';};

            if(is_array($data->value)) array_walk($data->value,$quotes);
            else $quotes($data->value);

        }

        //check if we have 'between'

        if($filter->operator === 'between'){

            $result.= ' between '.$data->value[0].' and '.$data->value[1];

        }else{

            $_array = is_array($data->value);
            $_not = $filter->operator === 'not' || $filter->operator === 'not_like';
            $_like = $filter->operator === 'like' || $filter->operator === 'not_like';

            if($_like){
                if(!$_array){
                    $result.=($_not ? ' not' : '').' like '.$data->value;
                }else{
                    $res_arr = array();
                    foreach($data->value as $val){
                        $res_arr[] = '('.$data->key.($_not ? ' not' : '').' like '.$val.')';
                    }
                    $result = implode(' or ',$res_arr);
                }
            }else{
                if(!$_array){
                    $result.=' '.$filter->prefix.' '.$data->value;
                }else{
                    $result.=($_not ? ' not' : '').' in ('.implode(',',$data->value).')';
                }
            }
        }


        return '('.$result.')';
    }


    /**
     * @param BFilterGroup $filter
     * @return array
     */

    public function PrepareGroupFilter(BFilterGroup $filter){

        $arr = array();

        foreach($filter->data as $data){
            array_push($arr,$data->toArray($this));
        }

        return '('.implode(' '.$filter->logic.' ',$arr).')';
    }

}

