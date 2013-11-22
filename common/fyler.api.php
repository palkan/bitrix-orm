<?php
/**
 * User: palkan
 * Date: 9/23/13
 * Time: 6:42 PM
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../utils/logger.php');
require_once(dirname(__FILE__).'/../utils/utils.php');

class FylerAPI {

    private $token;

    private $host;

    private $_login;

    private $_pass;


    /**
     * @param $host string Full api url (with port and 'api' prefix, with tailing slash) (e.g. <code>http://fyler.server:8008/api/</code>)
     */

    function __construct($host){

        $this->host = $host;

    }



    public function login($login,$pass){

        if(!$login || !$pass) return false;

        $this->_login = $login;
        $this->_pass = $pass;

        $response = $this->post('auth',array('login'=>$login,'pass'=>$pass));

        if(defined('LOGGER')) Logger::print_debug($response);

        if($response->code == 200){
            $data = json_decode($response->data,false);
            $this->token = $data->token;
            return true;
        }else{
            Logger::log(__FILE__.":".__LINE__." Attempt to authorize failed: ".$login.":".$pass,"warning");
            return false;
        }
    }



    public function send_task($type,$url,$options){

        if(is_null($this->token) && !$this->login($this->_login,$this->_pass)) return false;

        if(defined('LOGGER')) Logger::print_debug($options);

        $response  = $this->post('tasks',array_merge(array('url' => $url, 'type' => $type),$options));

        if(defined('LOGGER')) Logger::print_debug($response);

        if($response->code >= 200 && $response->code < 400){

            // task registered!!

            return true;

        }else if($response->code == 401){

            // authorization expired
            $this->token = null;
            return $this->send_task($type,$url,$options);
        }else{
            Logger::log(__FILE__.":".__LINE__." Send task failed: ".$url.":".$type,"error");
            return false;
        }

    }


    /**
     *
     * Make post request
     *
     * @param $target string Path to target (not including host)
     * @param $data array    Array of arguments to send
     * @return mixed
     *
     * @see curl_post
     * @private
     */

    private function post($target,$data){

        if($this->token) $data['fkey'] = $this->token;

        return curl_post($this->host.$target,$data);
    }
}
