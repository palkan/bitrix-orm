<?php
/**
 * User: VOVA
 * Date: 06.05.13
 * Time: 17:46
 */


namespace ru\teachbase;
require_once(dirname(__FILE__) . '/../base/custom.orm.php');
require_once(dirname(__FILE__) . '/../maps/assign.relation.map.php');

/**
 *
 * This class is used to work with both many-to-many and element-to-user relationships.
 *
 * Class AssignManager
 */

class AssignManager {

    static function find_by_element_id($id, $role = false){

        $filter = filter()->by_element_id($id);

        if($role) $filter = $filter->by_role($role);

        return make_assoc(Relation::find($filter),'user_id');

    }

    static function find_by_user_id($user_id, $role = false){

        $filter = filter()->by_user_id($user_id);

        if($role) $filter = $filter->by_role($role);

        return make_assoc(Relation::find($filter),'element_id');

    }


    static function find($user_id, $element_id){
        return Relation::find(filter()->by_user_id($user_id)->by_element_id($element_id));
    }


    static function delete_by_user_id($user_id){



    }


    static function delete_by_element_id($element_id){

    }


    static function delete($user_id,$element_id){

    }


    static function add($element_id,$user_id,$role = null){

    }


    static function change_role($element_id,$user_id,$role){

    }

}



class Relation extends CustomORM{

    public function user_id($val = null){return $this->_commit(__FUNCTION__,$val);}
    public function element_id($val = null){return $this->_commit(__FUNCTION__,$val);}
    public function code($val = null){return $this->_commit(__FUNCTION__,$val);}
    public function role($val = null){return $this->_commit(__FUNCTION__,$val);}

    function __construct(){
       parent::__construct();
    }

}

BitrixORM::registerMapClass(new AssignRelationMap(),Relation::className());