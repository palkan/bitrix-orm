<?php
/**
 * User: palkan
 * Date: 24.04.13
 * Time: 13:51
 */

require_once(dirname(__FILE__).'/bitrix.orm.php');

/**
 *
 * ORM model for custom tables.
 *
 * Class CustomORM
 */

class CustomORM extends BitrixORM{

    public $id;

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

        Logger::print_debug($sqlQuery);

        return $this->query($sqlQuery);
    }


    /**
     *
     * @return bool|CDBResult
     */


    public function save(){

        if(!$this->id) return $this->Create();

        return $this->Update();
    }


    private function create(){

        $sqlStr = "insert into ".$this->map->table;

        //TODO: get fields|values

        return $this->query($sqlStr);

    }


    private function update(){

        $sqlStr = 'update '.$this->map->table.' set ';

        //TODO: get fields|values

        return $this->query($sqlStr);
    }


    public function delete(){

        $sqlStr = 'delete from '.$this->map->table.' where id='.$this->id;
        return $this->query($sqlStr);

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


    public function PrepareFilterElement(BFilterElement $filter){

        if(!$filter->prefix || empty($filter->prefix)) $filter->prefix = '=';
        if($filter->prefix === '!') $filter->prefix = '<>';

        $result = '';

        $data = $this->GetBitrixFieldValue($filter->field,$filter->value);

        if(!$data) return;

        $result = $data->key;

        $rule = $this->rules[$filter->field];

        // add quotes if needed

        if($rule->type === 'string' || $rule->type === 'datetime' || $rule->type === 'bool'){

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

