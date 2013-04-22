<?php
/**
 * User: palkan
 * Date: 19.04.13
 * Time: 16:35
 */


require_once(dirname(__FILE__).'/bitrix.orm.php');

class BitrixUserORM implements tSerializable{

    /**
     * @var BitrixORMMapUser
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


    /**
     *
     * Find objects using filter and navigation settings.
     *
     * @param BFilter $filter
     * @param BNav $navigation
     * @param int $flags
     * @return mixed
     */

    public static function find(BFilter $filter = null, BNav &$navigation = null, $flags = 0){

        $instance = new static();

        if(defined('LOGGER')) Logger::print_debug($filter,false);

        $arFilter = $filter ? $filter->toArray($instance->map,true) : array();

        $arSelect = $instance->map->GetSelectFields();

        $arNav = $navigation ? $navigation->toArray(): false;

        $arSort = $navigation ? $navigation->sortArray($instance->map) : array('ID' => 'DESC');

        $arParams = array(
            'SELECT' => $arSelect
        );

        if($arNav) $arParams['NAV_PARAMS'] = $arNav;

        $resArr = CUser::GetList($arSort,$arSort,$arFilter,$arParams);

        $results = array();

        if(defined('LOGGER')) Logger::print_debug($arFilter);

        while($arUser = $resArr->Fetch())
        {
            $usr = new static();
            $usr->fromBitrixData($arUser);

            $results[$usr->id] = $usr;
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



class BitrixORMMapUser extends BitrixORMMap{

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


    public function GetBitrixUserFieldValue($field,$value,$prefix){

        // some fields behave like ibockelement fields

        if(!in_array($field,$this->filter_fields)){

            $data = $this->GetBitrixFieldValue($field,$value);
            return array($prefix.$data->key => $data->value);

        }else{


            // for updated_at and last_login we have to translate to TIMESTAMP_1(_2) and LAST_LOGIN_1(_2) respectively

            if($field === 'updated_at' || $field === 'last_login'){

                $data = $this->GetBitrixFieldValue($field,$value);

                $bname = $this->rules[$field]->bitrixName;

                //check if we have 'between'

                if(count($data->value) === 2){
                     return array(
                         $bname.'_1' => $data->value[0],
                         $bname.'_2' => $data->value[1]
                     );
                }

                // if the only one bound is set then define it

                if(strpos($prefix,'<')!==false)
                    return array($bname.'_2' => $data->value);
                else
                    return array($bname.'_1' => $data->value);
            }



            $bname = '';

            // for login we have to replace 'LOGIN' with 'LOGIN_EQUAL' if we don't have any '%' symbols and trim any of them in the end and in the beginning otherwise

            if($field === 'login'){

                if(!is_array($value)) $value = array($value);

                $equal = true;

                foreach($value as &$val){
                    if(strpos($val,'%')!==false){
                        $equal = false;
                        $val = preg_replace('/^%?(.+[^%])%?$/','$1',$val);
                    }
                }

                $bname = $equal ? 'LOGIN_EQUAL' : 'LOGIN';

            }

            if($field === 'id') $bname = 'ID';
            elseif($field === 'email') $bname = 'EMAIL';
            elseif(!$bname) $bname = 'NAME';



            // check if we have array of values then convert to string with |

           // if(count($value) === 1) $value = $value[0];

            $value = is_array($value) ? '('.implode(' | ',$value).')' : $value;

            // check if we have negation

            if($prefix === '!'){
               $value = '~'.$value;
            }

            return array($bname => $value);


        }


    }

}
