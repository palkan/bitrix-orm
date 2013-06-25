<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 11:07
 */

namespace ru\teachbase;

require_once(dirname(__FILE__) . '/../base/user.orm.php');
require(dirname(__FILE__) . '/../maps/user.map.php');
require_once(dirname(__FILE__) . '/../assign/assign.manager.php');

class User extends BitrixUserORM{

    protected $_hints = 0;

    protected $_show_hints = true;

    protected $_notifications = 0;

    protected $_notifications_sms = 0;

    private $_photo_path;

    function __construct(){
        parent::__construct(new UserMap());
    }

    /**
     * Bitmask for hints to show.
     *
     * If equals to 0 then show all hints.
     *
     * If <code>(Hint.code & hints) > 0</code> - the hint have been already shown.
     *
     * @var int
     */

    public function hints($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Bitmask for E-mail notifications to send. If notification's bit is set then we don't send this notification to the User.
     *
     * TODO: Is it really bad idea to filter users on php side rather then on sql side (using multiple fields)?
     *
     * @param null $val
     * @return mixed
     */

    public function notifications($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     *
     * Bitmask for SMS notifications to send. If notification's bit is set then we don't send this notification to the User.
     *
     *
     * @param null $val
     * @return mixed
     */

    public function notifications_sms($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     *
     * Define whether to show hints at all.
     *
     * @var bool
     */

    public function show_hints($val = null){return $this->_commit(__FUNCTION__,$val);}


    public function full_name(){
        $res = $this->_name;

        !empty($this->_last_name) && ($res.=' '.$this->_last_name);

        return $res;
    }


    public function photo_path(){

        if(is_null($this->_photo_path)) $this->_photo_path = intval($this->_photo) ? \CFile::GetPath($this->_photo) : false;

        return $this->_photo_path;

    }

    /**
     * Returns array of partners for User
     *
     * @return array
     *
     */

    public function partners(){

        //TODO: method stub


        return array(new Partner());

    }


    public function delete(){

        if(parent::delete()){
            AssignManager::delete_by_user_id($this->_id);
            return true;
        }else return false;

    }

    public static function delete_by_id($id){

        if(parent::delete_by_id($id)){
            AssignManager::delete_by_user_id($id);
            return true;
        }else return false;

    }

}


class UserRoles{

    const GUEST = 0;
    const LISTENER = 1;
    const SPECIALIST = 3;
    const MANAGER = 7;

}