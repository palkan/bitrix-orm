<?php
/**
 * User: palkan
 * Date: 5/20/14
 * Time: 10:47 AM
 */

namespace ru\teachbase;

class CSRF{

    private $token='';

    function __construct(){
        if (!isset($_SESSION['csrf'])) {
            $this->token = md5(uniqid(rand(), TRUE));
            $_SESSION['csrf'] = $this->token;
        }
        else
            $this->token = $_SESSION['token'];
    }


    public function read($clear = true){

        $token = $this->token;

        if($clear){
            $this->token = '';
            $_SESSION['csrf'] = '';
        }

        return $token;
    }

}

$GLOBALS['csrf'] = new CSRF;
