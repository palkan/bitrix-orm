<?php
namespace ru\teachbase;

require_once(dirname(__FILE__) . '/../utils/logger.php');

class RedisClient {

    const REDIS = "10.59.55.82";

    /**
     * @return bool|\Redis
     */

    public static function get(){
        $redis = new \Redis();
        if($redis->connect(self::REDIS)){

            return $redis;

        }else
            Logger::log($redis->getLastError(),'error');

        return false;
    }

}