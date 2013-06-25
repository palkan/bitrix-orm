<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 16:35
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/bitrix.orm.php');

class BitrixUserORM extends BitrixORM{

    protected $_id;
    protected $_active;
    protected $_login;
    protected $_last_login;
    protected $_last_activity;
    protected $_name;
    protected $_last_name;
    protected $_second_name;
    protected $_email;
    protected $_modified_by;
    protected $_registered_at;
    protected $_updated_at;
    protected $_phone;
    protected $_photo;

    //---- Begin: Common fields ----//

    /**
     * Element ID
     * @return int
     *
     */

    public function id(){return $this->_id;}

    /**
     * Element activity
     */

    public function active($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function login($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Last login date (UTC)
     */

    public function last_login($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Last activity date (UTC)
     */

    public function last_activity($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function name($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function last_name($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function second_name($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function email($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Modified by (user id)
     */

    public function modified_by($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Created at ('DATE_REGISTER') (UTC)
     */

    public function registered_at($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Updated at ('TIMESTAMP_X') (UTC)
     */

    public function updated_at($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     * Photo file ID
     *
     */

    public function photo($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function phone($val = null){return $this->_commit(__FUNCTION__,$val);}

    //---- End: Common fields ----//

    function __construct(BitrixORMMapUser $_map){
        $this->map = $_map;
    }



    protected function __Load($arFilter,$arSort,$arNav,$arSelect){

        $arParams = array(
            'SELECT' => $arSelect
        );

        if($arNav) $arParams['NAV_PARAMS'] = $arNav;

        return \CUser::GetList($arSort,$arSort,$arFilter,$arParams);

    }


    public function delete(){

        if(is_null($this->_id)) return false;

        if(!\CUser::Delete($this->_id)){
            return false;
        }

        return true;

    }


    public static function delete_by_id($id){

        if(!\CIBlockElement::Delete($id)){
            return false;
        }

        return true;
    }


    protected function _create(){

        $usr = new \CUser();

        $data = $this->map->fields_to_create($this);

        $arFields = array_merge($data->fields,$this->prefix_props($data->props));

        if(defined('LOGGER')) Logger::print_debug($arFields);

        if($ID = $usr->Add($arFields)){
            $this->_id = intval($ID);
            $this->_created = true;
            return $this;
        }

        return false;
    }


    protected function _update(){

        $usr = new \CUser();

        $data = $this->map->fields_to_update($this);

        $arFields = array_merge($data->fields,$this->prefix_props($data->props));


        if(defined('LOGGER')) Logger::print_debug($arFields);

        if($usr->Update($this->_id, $arFields)) return $this;

        return false;
    }


    private function prefix_props($props){

        $pr_props = array();

        foreach($props as $key => $val){
            $pr_props['UF_'.$key] = $val;
        }

        return $pr_props;

    }


}



class BitrixORMMapUser extends BitrixORMMap{


    public $type = BitrixORMMapType::USER;

    public $has_id = true;

    /**
     *
     * Main CUser fields.
     *
     * @var array
     */

    public $fields = array(
        array('bname' => 'ID', 'name' => 'id', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'NAME', 'name' => 'name', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'ACTIVE', 'name' => 'active', 'type' => BitrixORMDataTypes::BOOL),
        array('bname' => 'LAST_LOGIN', 'name' => 'last_login', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'LAST_ACTIVITY_DATE','name' => 'last_activity', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'LAST_NAME', 'name' => 'last_name', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'SECOND_NAME', 'name' => 'second_name', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'DATE_REGISTER', 'name' => 'registered_at', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'EMAIL', 'name' => 'email', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'TIMESTAMP_X', 'name' => 'updated_at', 'type' => BitrixORMDataTypes::DATETIME),
        array('bname' => 'LOGIN', 'name' => 'login', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'PERSONAL_PHOTO', 'name' => 'photo', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'PERSONAL_PHONE', 'name' => 'phone', 'type' => BitrixORMDataTypes::STRING)
    );


    protected $prop_prefix = 'UF_';

    private $filter_fields = array(
        'updated_at',
        'last_login',
        'name',
        'last_name',
        'second_name',
        'login',
        'email',
        'id'
    );



    public function PrepareFilterElement(BFilterElement $filter){

        // some fields behave like ibockelement fields

        if(!in_array($filter->field,$this->filter_fields)){

            return parent::PrepareFilterElement($filter);

        }else{


            // for updated_at and last_login we have to translate to TIMESTAMP_1(_2) and LAST_LOGIN_1(_2) respectively

            if($filter->field === 'updated_at' || $filter->field === 'last_login'){

                $data = $this->GetBitrixFieldValue($filter->field,$filter->value);

                $bname = $this->rules[$filter->field]->bitrixName;

                //check if we have 'between'

                if($filter->operator === 'between'){
                     return array(
                         $bname.'_1' => $data->value[0],
                         $bname.'_2' => $data->value[1]
                     );
                }

                // if the only one bound is set then define it

                if(strpos($filter->prefix,'<')!==false)
                    return array($bname.'_2' => $data->value);
                else
                    return array($bname.'_1' => $data->value);
            }



            $bname = '';

            // for login we have to replace 'LOGIN' with 'LOGIN_EQUAL' if we don't have any '%' symbols and trim any of them in the end and in the beginning otherwise

            if($filter->field === 'login'){

                if(!is_array($filter->value)) $filter->value = array($filter->value);

                $equal = true && ($filter->operator != 'like');

                foreach($filter->value as &$val){
                    if(strpos($val,'%')!==false){
                        $equal = false;
                        $val = preg_replace('/^%?(.+[^%])%?$/','$1',$val);
                    }
                }

                $bname = $equal ? 'LOGIN_EQUAL' : 'LOGIN';

            }

            if($filter->field === 'id') $bname = 'ID';
            elseif($filter->field === 'email') $bname = 'EMAIL';
            elseif(!$bname) $bname = 'NAME';



            // check if we have array of values then convert to string with |

           // if(count($value) === 1) $value = $value[0];

            $filter->value = is_array($filter->value) ? '('.implode(' | ',$filter->value).')' : $filter->value;

            // check if we have negation

            if($filter->operator === 'not'){
                $filter->value = '~'.$filter->value;
            }

            return array($bname => $filter->value);


        }


    }


    /**
     * @param BFilterGroup $filter
     * @return array
     */

    public function PrepareGroupFilter(BFilterGroup $filter){

        $arr = array();

        foreach($filter->data as $data){
            $tmp_arr = $data->toArray($this);
            array_push($arr,current($tmp_arr));
        }

        $del = ($filter->logic === 'or') ? ' | ' : ' & ';
        $keys = array_keys($tmp_arr);
        $arr = array($keys[0] => '('.implode($del,$arr).')');

        return $arr;
    }

}
