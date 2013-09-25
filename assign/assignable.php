<?php
/**
 * User: VOVA
 * Date: 08.05.13
 * Time: 14:57
 */

namespace ru\teachbase;

require_once(dirname(__FILE__) . '/../base/iblock.orm.php');
require_once(dirname(__FILE__).'/assign.manager.php');


class Assignable extends IBlockORM{


    protected $_users;


    public function delete(){

        if(parent::delete()){

            AssignManager::delete_by_element_id($this->_id);

            return true;
        }else return false;

    }

    public static function delete_by_id($id){

        if(parent::delete_by_id($id)){

            AssignManager::delete_by_element_id($id);

            return true;
        }else return false;

    }


    protected function _assign($user_rels){

       $this->users();

       $to_add = array();

       foreach($user_rels as $id => $rel){

           if(isset($this->_users[$id])){

               if($this->_users[$id]->role() !== $rel->role()){
                   $this->_users[$id]->role($rel->role());
                   $this->_users[$id]->save();
               }

           }else $to_add[$id] = $rel;

       }


       //TODO: save relations

       $this->_users = array_merge($this->_users,$to_add);

    }


    /**
     *
     *  Remove users from assigned
     *
     */


    protected function _unassign($user_rels){

        $this->users();

        $to_remove = array();

        foreach($user_rels as $id => $rel){

            if(isset($this->_users[$id])){
                $to_remove[$id] = $rel;
            }

        }


        //TODO: remove relations

        $this->_users = array_diff_key($this->_users,$to_remove);

    }


    /**
     *  Get all assigned users
     */


    public function users(){
        if(!is_null($this->_users)) $this->_users = AssignManager::find_by_element_id($this->_id);

        return $this->_users;
    }

}


class AssignableMap extends IBlockORMMap{

    public $assign_code = 'NULL';

}