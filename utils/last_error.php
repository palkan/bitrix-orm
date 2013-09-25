<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 12:32
 */


namespace ru\teachbase;

class LastError {

    public static $LOG_DIR = "/var/www/.priv/log/";

    static $errors = array();

    static $warnings = array();


    public static function error($message){
        self::$errors[] = $message;

        self::write_to_file('error',$message);
    }

    public static function warning($message){
        self::$warnings[] = $message;

        self::write_to_file('warning',$message);
    }

    public static function last_error(){
        return count(self::$errors)>0 ? self::$errors[count(self::$errors-1)] : null;
    }


    private static function write_to_file($level,$message){
        error_log("[".date('Y-m-d H:i:s')."] ".(is_string($message) ? $message : json_encode($message))."\n",3,self::$LOG_DIR.$level.".log");
    }

}
