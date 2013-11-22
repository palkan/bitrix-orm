<?php
/**
 * User: VOVA
 * Date: 08.05.13
 * Time: 14:57
 */

namespace ru\teachbase;

require_once(dirname(__FILE__) . '/../assign/assignable.php');
require_once(dirname(__FILE__) . '/user.hash.php');

class AuthoredBlock extends Assignable
{

    protected $_author='';

    public function author(){return $this->_author;}

    public function fromBitrixData($data){
        parent::fromBitrixData($data);
        if($this->_created_by){
            if($name = UserHash::get($this->_created_by,"fullname")) $this->_author = $name;
            else{

                $user = User::find_by_id($this->_created_by);
                $this->_author = $user->full_name();
            }
        }
        return $this;
    }

    public function jsonData(){
        $data = parent::jsonData();

        $data->author = $this->_author;

        return $data;
    }

}
