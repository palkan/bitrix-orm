<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 12:32
 */


namespace ru\teachbase;

require_once(dirname(__FILE__).'/last_error.php');

class Logger {

    const DEBUG = 4;
    const WARNING = 2;
    const ERROR = 1;

    public static $LEVEL = self::WARNING;


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

        if($public)
            $o = self::print_object($object,$public);
        else
            $o = $object;

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


    private static function print_object($object){
        if($object instanceof tSerializable)
            return $object->jsonData();
        elseif(is_array($object)){
            $res = array();
            foreach($object as $key => $o) $res[$key] = self::print_object($o);
            return $res;
        }elseif(is_object($object))
            return get_object_vars($object);
        else
            return $object;
    }


    public static function log($message,$level){

        if(Logger::$LEVEL == Logger::DEBUG) Logger::print_debug(array('LEVEL' => $level, 'MESSAGE' => $message));

        LastError::$level($message);

    }



}
