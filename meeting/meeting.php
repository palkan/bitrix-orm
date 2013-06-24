<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/meeting.map.php');

class Meeting extends Assignable{

    protected $_partner_id;
    protected $_type;
    protected $_duration;
    protected $_users_num;
    protected $_guests_num;

    public function partner_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function type($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function duration($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function users_num($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function guests_num($val = null){return $this->_commit(__FUNCTION__,$val);}

    function __construct(){
        parent::__construct(new MeetingMap());
    }
}


class MeetingRoles{

    const PARTICIPANT = 1;
    const PRESENTER = 2;

}

class MeetingTypes{

    const PUB = 'public';
    const PRIV = 'private';

}