<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 16:35
 */


require_once(dirname(__FILE__).'/bitrix.orm.php');

class BitrixUserORM extends BitrixORM{

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
     * Last login date (UTC)
     *
     * @var int
     */

    public $last_login;

    /**
     *
     * Last activity date (UTC)
     *
     * @var int
     */

    public $last_activity;

    /**
     * @var string
     */

    public $name;

    /**
     *
     * @var string
     */

    public $last_name;

    /**
     *
     * @var string
     */

    public $second_name;


    /**
     *
     * @var string
     */

    public $email;

    /**
     *
     * Modified by (user id)
     *
     * @var int
     */

    public $modified_by;

    /**
     *
     * Created at ('DATE_REGISTER') (UTC)
     *
     * @var int
     *
     */

    public $registered_at;

    /**
     *
     * Updated at ('TIMESTAMP_X') (UTC)
     *
     * @var  int
     */

    public $updated_at;


    /**
     * Photo file ID
     *
     * @var int
     */

    public $photo;

    /**
     * @var string
     */

    public $phone;

    //---- End: Common fields ----//

    function __construct(BitrixORMMapUser $_map){
        $this->map = $_map;
    }



    protected function __Load($arFilter,$arSort,$arNav,$arSelect){

        $arParams = array(
            'SELECT' => $arSelect
        );

        if($arNav) $arParams['NAV_PARAMS'] = $arNav;

        return CUser::GetList($arSort,$arSort,$arFilter,$arParams);

    }


    public function delete(){

    }


    public function save(){

        return $this;
    }


    public function jsonData(){ return $this;}

}



class BitrixORMMapUser extends BitrixORMMap{


    public $type = BitrixORMMapType::USER;

    /**
     *
     * Main CUser fields.
     *
     * @var array
     */

    public $fields = array(
        array('bname' => 'ID', 'name' => 'id', 'type' => 'int'),
        array('bname' => 'NAME', 'name' => 'name', 'type' => 'string'),
        array('bname' => 'ACTIVE', 'name' => 'active', 'type' => 'bool'),
        array('bname' => 'LAST_LOGIN', 'name' => 'last_login', 'type' => 'datetime'),
        array('bname' => 'LAST_ACTIVITY_DATE','name' => 'last_activity', 'type' => 'datetime'),
        array('bname' => 'LAST_NAME', 'name' => 'last_name', 'type' => 'string'),
        array('bname' => 'SECOND_NAME', 'name' => 'second_name', 'type' => 'string'),
        array('bname' => 'DATE_REGISTER', 'name' => 'registered_at', 'type' => 'datetime'),
        array('bname' => 'EMAIL', 'name' => 'email', 'type' => 'string'),
        array('bname' => 'TIMESTAMP_X', 'name' => 'updated_at', 'type' => 'datetime'),
        array('bname' => 'LOGIN', 'name' => 'login', 'type' => 'string'),
        array('bname' => 'PERSONAL_PHOTO', 'name' => 'photo', 'type' => 'int'),
        array('bname' => 'PERSONAL_PHONE', 'name' => 'phone', 'type' => 'string')
    );


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

    public function initialize(BitrixUserORM &$ormObject, $data){


        foreach($this->rules as $rule){

            if(isset($data[$rule->bitrixName])){
                $ormName = $rule->ormName;
                $ormObject->$ormName = $rule->toORM($data[$rule->bitrixName]);

            }

        }

    }


    public function PrepareFilterElement(BFilterElement $filter){

        // some fields behave like ibockelement fields

        if(!in_array($filter->field,$this->filter_fields)){

            return parent::PrepareFilterElement($filter->field,$filter->value,$filter->prefix);

        }else{


            // for updated_at and last_login we have to translate to TIMESTAMP_1(_2) and LAST_LOGIN_1(_2) respectively

            if($filter->field === 'updated_at' || $filter->field === 'last_login'){

                $data = $this->GetBitrixFieldValue($filter->field,$filter->value);

                $bname = $this->rules[$filter->field]->bitrixName;

                //check if we have 'between'

                if($this->rules[$filter->field]->operator === 'between'){
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

                $equal = true;

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

            if($this->rules[$filter->field]->operator === 'not'){
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
