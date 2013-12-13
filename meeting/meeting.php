<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(__DIR__ . '/../maps/meeting.map.php');
require(__DIR__ . '/../common/erly.api.php');

require_once(__DIR__.'/../document/recording.php');
require_once(__DIR__.'/../notify/notify.manager.php');
//require_once(__DIR__.'/../common/authored.block.php');

class Meeting extends Assignable
{ //AuthoredBlock{

    protected $_partner_id;
    protected $_type;
    protected $_duration;
    protected $_users_num;
    protected $_guests_num;

    public function partner_id($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    public function type($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    public function duration($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    public function users_num($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    public function guests_num($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $users_roles  array Associative array [user_id] => [user_role]
     * @param $notify bool send notifications flag
     * @return array
     */

    public function add_participants($users_roles, $notify = false)
    {

        if (!$this->_id) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Trying to assign users to not saved meeting", "error");
            return false;
        }

        $assigned =  $this->_assign($users_roles);

        if($notify){
            //temp!
            //todo:create external templates
            $theme = "Вы приглашены на видеовстречу - «".$this->_name."».";
            $link = 'http://'.$_SERVER['HTTP_HOST'].'/meetings/'.$this->_id;
            $body = $theme.'<br/>Встреча состоится '.date('d.m.Y H:i',$this->_date_active_from).'.<br/>Для того, чтобы зайти на встречу, пройдите по ссылке <a href="'.$link.'">'.$link.'</a>';
            NotifyManager::notify(
                array_map(
                    function(Relation $rel){
                        return $rel->user_id();
                    },$assigned),
                new Notification($theme,$body,NotifyManager::MEETING));
        }

        return $assigned;
    }

    /**
     * @param $user_ids array Users' ids.
     * @param $notify bool send notifications flag
     * @return array
     */

    public function remove_participants($user_ids, $notify = false)
    {

        if (!is_array($user_ids)) $user_ids = array($user_ids);

        if (!$this->_id) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Trying to unassign users from not saved meeting", "error");
            return false;
        }

        return $this->_unassign($user_ids);
    }

    /**
     * First finish meeting and then delete.
     * Also delete all recordings.
     *
     * @return bool
     */

    public function delete()
    {
        if(!$this->_id) return false;

        $this->finish(false);

        $recordings = Recording::find(filter()->by_meeting_id($this->_id));

        foreach($recordings as $rec) $rec->delete();

        return parent::delete();
    }

    /**
     * Finish meeting and stop Erly meeting.
     * Save statistics (not implemented yet).
     *
     * @param bool $commit
     * @return $this
     */

    public function finish($commit = true)
    {

        $api = new ErlyAPI("http://".ERLY_HOST.":8082/");
        $api->finish_meeting($this->_id);

        //todo: receive statistics from Erly and save it

        $this->date_active_to(time());

        if (!$commit) return $this;

        return $this->save();
    }

}


class MeetingRoles
{

    const PARTICIPANT = 1;
    const PRESENTER = 2;

}

class MeetingTypes
{

    const PUB = 'public';
    const PRIV = 'private';

}

BitrixORM::registerMapClass(new MeetingMap(), Meeting::className());