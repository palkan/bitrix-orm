<?php
/**
 * User: palkan
 * Date: 9/23/13
 * Time: 6:42 PM
 */

namespace ru\teachbase;

require_once(dirname(__FILE__).'/../utils/logger.php');
require_once(dirname(__FILE__).'/../utils/utils.php');

class ErlyAPI {

    private $host;

    private $_login = "erlymaster";

    private $_pass = "erly11";


    /**
     * @param $host string Host url with tailing slash (e.g. <code>http://erly.server:8082/</code>)
     */

    function __construct($host){

        $this->host = $host.'api/teachbase/';

    }


    public function set_auth_hash($meeting_id, $name, $guest_id){

        $options = array('meeting_id' => $meeting_id, 'full_name' => $name, 'guest_id' => $guest_id);

        if(defined('LOGGER')) Logger::print_debug($options);

        $response  = $this->post('admin/user',$options);

        if(defined('LOGGER')) Logger::print_debug($response);

        if($response->code >= 200 && $response->code < 400){

            $data = json_decode($response->data,false);

            if($data->status == "ok")
                return $data->data;
            else
                Logger::log(__FILE__.":".__LINE__." Set meeting auth hash failed: meeting_id ".$meeting_id.", guest_id ".$guest_id.", description: ".$data->data->description,"error");


        }else
            Logger::log(__FILE__.":".__LINE__." Request failed: meeting_id ".$meeting_id.", guest_id ".$guest_id,"error");

        return false;
    }

    /**
     * @param $meeting_id
     * @return bool
     */

    public function finish_meeting($meeting_id){

        $response  = $this->post('admin/meetings/'.$meeting_id.'/finish',array());

        if(defined('LOGGER')) Logger::print_debug($response);

        if($response->code >= 200 && $response->code < 400){

            $data = json_decode($response->data,false);

            if($data->status == "ok")
                return true;
            else
                Logger::log(__FILE__.":".__LINE__." Failed to finish meeting: meeting_id ".$meeting_id.", description: ".$data->data->description,"warning");

        }else
            Logger::log(__FILE__.":".__LINE__." Request failed: finish meeting, meeting_id ".$meeting_id,"error");

        return false;
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

        $data['api_admin_login'] = $this->_login;
        $data['api_admin_pass'] = $this->_pass;

        if(defined("LOGGER")) Logger::print_debug($this->host.$target);

        return curl_post($this->host.$target,$data);
    }
}
