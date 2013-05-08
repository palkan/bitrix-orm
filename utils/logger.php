<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 12:32
 */


namespace ru\teachbase;

define(LOGGER,true);

class Logger {


    /**
     *
     * Print variable info on screen.
     *
     * @param mixed $object
     * @param bool $public  define whether to show only public properties
     * @param int|null $height height of view block
     */


    public static function print_debug($object, $public = true, $height = null){

        if(is_object($object) && $public){
            $o = get_object_vars($object);
        }else
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
        //TODO: implement logging to file or database
    }



}