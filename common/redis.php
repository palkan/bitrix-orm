<?php
namespace ru\teachbase;

require_once(dirname(__FILE__) . '/../utils/logger.php');

class RedisClient {

    /**
     * @return bool|\Redis
     */

    public static function get(){
        $redis = new \Redis();
        if($redis->connect(REDIS_HOST)){

            return $redis;

        }else
            Logger::log($redis->getLastError(),'error');

        return false;
    }

}