<?php
/**
 * User: VOVA
 * Date: 08.05.13
 * Time: 14:57
 */

namespace ru\teachbase;

require_once(dirname(__FILE__) . '/../base/iblock.orm.php');
require_once(dirname(__FILE__) . '/assign.manager.php');


class Assignable extends IBlockORM
{


    protected $_users;


    public function delete()
    {

        if (parent::delete()) {

            AssignManager::delete_by_element_id($this->_id);

            return true;
        } else return false;

    }

    public static function delete_by_id($id)
    {

        if (parent::delete_by_id($id)) {

            AssignManager::delete_by_element_id($id);

            return true;
        } else return false;

    }

    /**
     *
     *
     * @param $user_roles  array Associative array [user_id] => [user_role]
     * @return bool
     */

    protected function _assign($user_roles)
    {

        $this->users();

        $to_add = array();

        foreach ($user_roles as $id => $role) {

            if (isset($this->_users[$id])) {

                if ($this->_users[$id]->role() !== $role) {
                    $this->_users[$id]->role($role);
                    $this->_users[$id]->save();
                }

            }else{
                $rel = new Relation();
                $rel->code($this->mapref->assign_code);
                $rel->element_id($this->_id);
                $rel->user_id($id);
                $rel->role($role);

                $to_add[] = $rel;
            }

        }

        if(defined("LOGGER")) Logger::print_debug($user_roles);

        if (!Relation::create_many($to_add)) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Error assigning users; id: " . $this->_id, "error");
            return false;
        }

        $this->_users = null;

        $this->users();

        return true;
    }


    /**
     *
     *  Remove users from assigned
     *
     * @param $user_ids array
     * @return bool
     */


    protected function _unassign($user_ids)
    {

        $this->users();

        $to_remove = array();

        foreach ($user_ids as $id) {

            if (isset($this->_users[$id])) {
                $to_remove[$id] = $this->_users[$id];
            }

        }


        if (!Relation::delete_many($to_remove)) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Error unassigning users; id: " . $this->_id, "error");
            return false;
        }


        $this->_users = null;

        $this->users();

        return true;

    }


    /**
     *  Get all assigned users
     */


    public function users()
    {
        if (is_null($this->_users)) $this->_users = AssignManager::find_by_element_id($this->_id);

        return $this->_users;
    }


    /**
     *
     * Check whether relation with user <i>id</i> exists and return <i>Relation</i>.
     *
     * Otherwise return <i>false</i>.
     *
     * @param $id
     * @return bool|mixed
     */

    public function has_user($id){

        if(!is_null($this->_users)){

            foreach($this->_users as $uid => $rel){
                if($uid == $id) return $rel;
            }

            return false;
        }

        return AssignManager::find($id,$this->_id);

    }


    public function jsonData(){
        $data = parent::jsonData();

        $data->users = array_map(function($el){ return $el->jsonData();},array_values($this->_users));

        return $data;
    }

}


class AssignableMap extends IBlockORMMap
{

    public $assign_code = 'NULL';

}