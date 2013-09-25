<?php

namespace ru\teachbase;

require_once(dirname(__FILE__)."/fyler.api.php");

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

    const REDIS = "10.59.55.82";

    const CALLBACK = "/api/fyler/callback/";

    const FYLER_HOST = 'http://dev1.teachbase.ru:8008/api/';

    const FYLER_LOGIN = "fad";

    const FYLER_PASSWORD = "gnwro3494GTY";



    /**
     *
     * Convert document using Fyler service.
     *
     * @param $id
     * @param $type
     * @param $url
     * @param array $conversion_options
     * @param array $listeners
     * @return bool|string
     */

    public static function fyler_convert($id, $type, $url, $conversion_options = array(), $listeners = array()){

        $redis = new \Redis();

        if($redis->connect(self::REDIS)){

            $tid = $redis->incr(self::RDB."total");
            $key = self::RDB.$tid;

            $data = array(
                'type' => $type,
                'id' => $id,
                'listeners' => $listeners
            );

            $conversion_options['callback'] = "http://".$_SERVER['HTTP_HOST'].self::CALLBACK.$key;

            if(defined('LOGGER')) Logger::log("Convert: $id,  $type, $url","debug");

            $fyler = new FylerAPI(self::FYLER_HOST);

            $fyler->login(self::FYLER_LOGIN,self::FYLER_PASSWORD);

            if($fyler->send_task($type,$url,$conversion_options)){
                $redis->set($key,json_encode($data));
                $redis->close();
                return $key;
            }else
                 return false;
        }else
            Logger::log($redis->getLastError(),'error');


        return false;
    }



    public static function task_complete($id,$data){

        $redis = new \Redis();


        if($redis->connect(self::REDIS)){

            if($val = $redis->get(self::RDB.$id)){
                $task = json_decode($val,false);

                $data->type = $task->type;
                $data->id = $task->id;

                Logger::log(array('data' => serialize($data), 'task' => serialize($task)),"warning");

                $doc = Document::find_by_id($task->id);

                if($doc){

                    $doc->conversion_complete($data);

                    foreach($task->listeners as $l) self::invoke_listener($l,$data);

                }else
                    Logger::log(__CLASS__.":".__LINE__." Document not found $task->id","warning");
            }

            $redis->close();

        }else
            Logger::log($redis->getLastError(),'error');

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