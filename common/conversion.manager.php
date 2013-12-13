<?php

namespace ru\teachbase;

require_once(dirname(__FILE__)."/fyler.api.php");
require_once(dirname(__FILE__) . "/redis.php");

/**
 *
 *
 * Manage Fyler API and Redis storage for tasks.
 *
 *
 * Redis scheme:
 *
 *  key -> data (
 *
 * Class ConversionManager
 */

class ConversionManager {


    /**
     * Redis "database" name for tasks
     */

    const RDB = "tasks_";

    const CALLBACK = "/api/fyler/callback/";

    /**
     *
     * Convert document using Fyler service.
     *
     * @param $id
     * @param $type
     * @param $klass
     * @param $url
     * @param array $conversion_options
     * @param array $listeners
     * @return bool
     */

    public static function fyler_convert($id, $type, $klass, $url, $conversion_options = array(), $listeners = array()){

        $redis = RedisClient::get();

        if($redis){

            $tid = $redis->incr(self::RDB."total");
            $key = self::RDB.$tid;

            $data = array(
                'type' => $type,
                'id' => $id,
                'klass' => $klass,
                'listeners' => $listeners,
                'status' => 'progress'
            );

            $conversion_options['callback'] = "http://".$_SERVER['HTTP_HOST'].self::CALLBACK.$key;

            $fyler = new FylerAPI('http://'.FYLER_HOST.':8008/api/');

            $fyler->login(FYLER_LOGIN,FYLER_PASSWORD);

            if($fyler->send_task($type,$url,$conversion_options)){
                $redis->set($key,json_encode($data));
                return $tid;
            }else
                 return false;

            $redis->close();
        }

        return false;
    }


    /**
     * @param $id  string Task id
     * @param $data mixed   Post data as object
     * @param $post_data mixed  Raw post data (as array).
     */


    public static function task_complete($id,$data,$post_data){

        $redis = RedisClient::get();


        if($redis){

            if($val = $redis->get(self::RDB.$id)){
                $task = json_decode($val,false);

                $data->type = $task->type;
                $data->id = $task->id;

                $klass = $task->klass;

                $target = $klass::find_by_id($task->id);

                if($target){

                    $target->conversion_complete($data);
                    $task->status = 'complete';

                    $redis->set(self::RDB.$id,json_encode($task));
                    $redis->expire(self::RDB.$id,60*10);

                    foreach($task->listeners as $l) self::invoke_listener($l,$post_data);

                }else
                    Logger::log(__CLASS__.":".__LINE__." Document not found $task->id","warning");


            }

            $redis->close();

        }

    }

    /**
     *
     * Get task info from Redis by id.
     *
     * @param $id
     * @return bool|mixed|null
     */

    public static function get_task($id){

        $redis = RedisClient::get();


        if($redis){

            if($val = $redis->get(self::RDB.$id)){
                $task = json_decode($val,false);

                return $task;
            }

            $redis->close();

            return false;

        }

        return null;

    }



    public static function invoke_listener($listener,$data){

        $l = TaskListener::build($listener);

        $l->dispatch($data);

    }

}



class TaskListener{

    const HTTP = "http";

    public $type;

    function __construct($type){
        $this->type = $type;
    }

    /**
     * @param $data
     * @return bool
     */

    public function dispatch($data){
        // template method
        return true;
    }

    public static function build($data){

        switch($data->type){
            case self::HTTP:
                $l = new HTTPListener($data->url);
                return $l;
            default:
                return new TaskListener($data->type);
        }

    }

}



class HTTPListener extends  TaskListener{

    public $url;

    function __construct($url){

        parent::__construct(TaskListener::HTTP);

        $this->url = $url;
    }


    public function dispatch($data){

       $response = curl_post($this->url,$data);

       if($response->code >= 200 && $response->code < 400)
           return true;
       else{
           Logger::log(__CLASS__.":".__LINE__." Dispatch failed: $response->code; $response->data","error");
           return false;
       }
    }

}





if(!$_SERVER['HTTP_HOST']) $_SERVER['HTTP_HOST'] = "dev2.teachbase.ru";