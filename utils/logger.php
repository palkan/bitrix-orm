<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 12:32
 */


namespace ru\teachbase;

define(LOGGER,true);

class Logger {

    const DEBUG = 4;
    const WARNING = 2;
    const ERROR = 1;

    public static $LEVEL = WARNING;

    public static $LOG_DIR = "/var/www/tmp/logs/";


    /**
     *
     * Print variable info on screen.
     *
     * @param mixed $object
     * @param bool $public  define whether to show only public properties
     * @param int|null $height height of view block
     */


    public static function print_debug($object, $public = true, $height = null){

        if(Logger::$LEVEL < Logger::DEBUG) return;

        if(is_object($object) && $public){
            if($object instanceof tSerializable)
                $o = $object->jsonData();
            else
                $o = get_object_vars($object);
        }
        else
            $o =& $object;
        if(!$height){
            $heightBlock = "min-height: auto";
        } else {
            $heightBlock = "height: ".$height."px";
        }
        $bt =  debug_backtrace();
        $bt = $bt[0];
        $dRoot = $_SERVER["DOCUMENT_ROOT"];
        $dRoot = str_replace("/","\\",$dRoot);
        $bt["file"] = str_replace($dRoot,"",$bt["file"]);
        $dRoot = str_replace("\\","/",$dRoot);
        $bt["file"] = str_replace($dRoot,"",$bt["file"]);
        ?>
        <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000; <?=$heightBlock?>; overflow: auto;'>
            <div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?=$bt["file"]?> [<?=$bt["line"]?>]</div>
            <pre style='padding:10px;'><?print_r($o)?></pre>
        </div>
    <?


    }


    public static function log($message,$level){

        if(Logger::$LEVEL == Logger::DEBUG) Logger::print_debug(array('LEVEL' => $level, 'MESSAGE' => $message));
        else{
            // todo log to file
        }

    }



}
