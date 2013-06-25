<?php
/**
 * User: VOVA
 * Date: 06.05.13
 * Time: 17:46
 */


namespace ru\teachbase;
require_once(dirname(__FILE__).'/../user/user.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/libs/sms/QTSMS.class.php');

/**
 *
 * Notification manager (send notifications of a type NOTIFY)
 *
 * Class NotifyManager
 */

class NotifyManager {

    const EVENT = "NOTIFY";


    /** Notifications codes */

    const QUIZ = 1;
    const COURSE = 2;
    const MEETING = 4;
    const LIBRARY = 8;

    /**
     * @param $user_ids
     * @param $notification
     */

    public static function notify($user_ids, Notification $notification)
    {

        if(!$user_ids) return;

        if(!is_array($user_ids)){

            $user = User::find_by_id($user_ids);

            self::notifyUser($user,$notification);
        }else{

            $users = User::find(filter()->by_id($user_ids));

            foreach($users as $u){

                self::notifyUser($u,$notification);

            }
        }
    }






    private static function notifyUser(User $user, Notification $notification){

        if(!$user || (($user->notifications() & $notification->code)>0)) return;

        $arEventFields = array(
            "USER_EMAIL"  => $user->email(),
            "THEME" => $notification->theme,
            "NAME" => $user->full_name(),
            "BODY" => $notification->body
        );

        \CEvent::Send(self::EVENT, SITE_ID, $arEventFields);

        self::notifyUserSMS($user,$notification);
    }


    private static function notifyUserSMS(User $user, Notification $notification){

        if($user->phone() && (($user->notifications_sms() & $notification->code) <= 0)){

            $sms= new \QTSMS('22358','99116584','web.mirsms.ru');

            $period=600;
            $txt_sms = $notification->theme;

            $sms->post_message($txt_sms, $user->phone(), '','',$period);

        }
    }

}



class Notification{

    /**
     * @var
     */

    public $theme;

    /**
     * @var
     */
    public $body;

    /**
     * @var
     */

    public $code;

    function __construct($theme,$body,$code = 0){

        $this->body = $body;
        $this->theme = $theme;
        $this->code = $code;
    }

}
