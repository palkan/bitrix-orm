<?php
/**
 * User: palkan
 * Date: 11/22/13
 * Time: 5:12 PM
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../utils/logger.php');
require_once(dirname(__FILE__).'/../utils/utils.php');
require_once(dirname(__FILE__) . "/redis.php");

class UserHash {

    /**
     * Redis "database" name for tasks
     */
    const RDB = "user.hash.";

    /**
     * @param $user_id
     * @param $prop
     * @param $val
     */

    public static function set($user_id,$prop,$val){
        $redis = RedisClient::get();

        if($redis){
            $redis->set(self::RDB.".".$user_id.".".$prop,$val);
            $redis->close();
        }
    }

    /**
     * @param $user_id
     * @param $prop
     * @return array|string|null
     */


    public static function get($user_id,$prop){
        $redis = RedisClient::get();

        if($redis){

            if(!is_array($user_id)) $res=$redis->get(self::RDB.".".$user_id.".".$prop);
            else{

                $res = array();

                foreach($user_id as $uid){
                    $res[$uid] = $redis->get(self::RDB.".".$uid.".".$prop);
                }
            }
            $redis->close();
            return $res;
        }

        return null;
    }


}
