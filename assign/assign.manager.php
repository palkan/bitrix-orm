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

    public static function find_by_element_id($id, $role = false, &$nav = null){

        $filter = filter()->by_element_id($id);

        if($role) $filter = $filter->by_role($role);

        return make_assoc(Relation::find($filter, $nav),'user_id');

    }

    public static function find_by_user_id($user_id, $role = false, $code = false, &$nav = null){

        $filter = filter()->by_user_id($user_id);

        if($role) $filter = $filter->by_role($role);
        if($code) $filter = $filter->by_code($code);

        return make_assoc(Relation::find($filter,$nav),'element_id');

    }

    /**
     *
     * Return <i>Relation</i> between <i>element_id</i> and <i>user_id</i> or <i>false</i>.
     *
     * @param $user_id
     * @param $element_id
     * @return bool|Relation
     */

    public static function find($user_id, $element_id){
        $rels = Relation::find(filter()->by_user_id($user_id)->by_element_id($element_id));

        if(count($rels)) return reset($rels);

        return false;
    }


    public static function delete_by_user_id($user_id){

        $rel = new Relation();

        return Relation::delete_many_by_conditions($rel->mapref->table, array('user_id' => $user_id));

    }


    public static function delete_by_element_id($element_id){

        $rel = new Relation();

        return Relation::delete_many_by_conditions($rel->mapref->table, array('element_id' => $element_id));

    }


    public static function delete($user_id,$element_id){
        $rel = Relation::find(filter()->by_user_id($user_id)->by_element_id($element_id));

        if(!count($rel)) return true;

        $rel = reset($rel);

        return $rel->delete();
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