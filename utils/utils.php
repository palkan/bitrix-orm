<?
/**
 * User: palkan
 * Date: 27.03.13
 * Time: 14:07
 */


namespace ru\teachbase;

class AjaxResponse
{

    const POLL_SLEEP_MAX = 10;
    const POLL_SLEEP_TIME = 2;

    public $status = 1;

    public $error_message;

    public $data;

    private $_sleep_count=0;

    private $_content_ready = false;

    function __construct()
    {
        register_shutdown_function(array($this, 'onShutdown'));
    }


    public function error($message)
    {

        $this->error_message = $message;
        $this->status = 0;
        $this->reply();
        die();

    }

    public function sleep(){

        if($this->_content_ready) return false;

        $this->_sleep_count++;

        if($this->_sleep_count>=self::POLL_SLEEP_MAX){

            return false;
        }

        sleep(self::POLL_SLEEP_TIME);
        return true;
    }



    public function content_ready(){
        $this->_content_ready = true;
    }

    public function onShutdown()
    {
        global $DB;
        if (!is_object($DB)) $DB->Disconnect();
    }


    public function reply()
    {
        echo json_encode($this);
    }

}


class Utils
{

    /**
     *
     * Return user browser info (from HTTP_USER_AGENT)
     *
     * @return stdClass contains <i>name</i> and <i>version</i>
     */


    public static function UserBrowserInfo()
    {

        if (isset($_SERVER["HTTP_USER_AGENT"]) OR ($_SERVER["HTTP_USER_AGENT"] != "")) {
            $visitor_user_agent = $_SERVER["HTTP_USER_AGENT"];
        } else {
            $visitor_user_agent = "Unknown";
        }

        $bname = 'Unknown';
        $version = "0.0.0";
        $short = '';

        if (preg_match('MSIE', $visitor_user_agent) && !preg_match('Opera', $visitor_user_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
            $short = 'IE';
        } elseif (preg_match('Firefox', $visitor_user_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
            $short = 'FF';
        } elseif (preg_match('Chrome', $visitor_user_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
            $short = 'Chrome';
        } elseif (preg_match('Safari', $visitor_user_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
            $short = 'Safari';
        } elseif (preg_match('Opera', $visitor_user_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
            $short = 'Opera';
        } else
            $ub = "Unknown";


        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        preg_match_all($pattern, $visitor_user_agent, $matches);

        $i = count($matches['browser']);

        if ($i != 1) {
            if (strripos($visitor_user_agent, "Version") < strripos($visitor_user_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        if ($version == null || $version == "") {
            $version = "?";
        }


        $result = new stdClass();

        $result->name = $bname;
        $result->version = $version;
        $result->short_name = $short;

        return $result;

    }

    /**
     *
     * Return user system name (from HTTP_USER_AGENT)
     *
     * @return string
     */

    public static function UserSystemInfo()
    {

        if (isset($_SERVER["HTTP_USER_AGENT"]) OR ($_SERVER["HTTP_USER_AGENT"] != "")) {
            $visitor_user_agent = $_SERVER["HTTP_USER_AGENT"];
        } else {
            $visitor_user_agent = "Unknown";
        }
        $oses = array(
            'Mac OS X(Apple)' => '(iPhone)|(iPad)|(iPod)|(MAC OS X)|(OS X)',
            'Apple\'s mobile/tablet' => 'iOS',
            'BlackBerry' => 'BlackBerry',
            'Android' => 'Android',
            'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows 98' => '(Windows 98)|(Win98)',
            'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
            'Windows 2003' => '(Windows NT 5.2)',
            'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
            'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME' => 'Windows ME',
            'Linux' => '(Linux)|(X11)',
            'ROBOT' => '(Spider)|(Bot)|(Ezooms)|(YandexBot)|(AhrefsBot)|(nuhk)|
                    (Googlebot)|(bingbot)|(Yahoo)|(Lycos)|(Scooter)|
                    (AltaVista)|(Gigabot)|(Googlebot-Mobile)|(Yammybot)|
                    (Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)|
                    (Ask Jeeves/Teoma)|(Java/1.6.0_04)'
        );
        foreach ($oses as $os => $pattern) {
            if (preg_match($pattern, $visitor_user_agent)) {
                return $os;
            }
        }

        return 'Unknown';
    }


}


//------------ Global functions (helpers) ------------------//


/**
 *
 * Get or set $_SESSION var.
 *
 * Note: set/get data only with uppercased var name. Path can be of any case.
 *
 * Example:
 * <code>
 * $_SESSION = array('1' => array('1.1' => 0, '1.2' => 1), '2' => 2);
 * session('1/1.1') === 0; //true
 * session('2',4); //setting var
 * session('2') === 4 //true
 * </code>
 *
 * @param string $path url-like path to variable
 * @param mixed|null $val if $val is null then 'get'; otherwise 'set'
 * @return bool
 */


function session($path, $val = null)
{

    $path = preg_replace('/^\/?(.+[^\/])\/?$/','$1',$path);

    $path_arr = explode('/', $path);

    $i = 0;
    $len = count($path_arr);

    if(is_null($val)){

        $session =& $_SESSION;

        while(isset($session[strtoupper($path_arr[$i])]) && $i<$len){
            $session =& $session[strtoupper($path_arr[$i])];

            $i++;
        }


        if($i != $len) return false;

        return $session;

    }else{

        $session =& $_SESSION;

        while($i<$len-1){

            if(!isset($session[strtoupper($path_arr[$i])]))
                $session[strtoupper($path_arr[$i])] = array();

            $session =& $session[strtoupper($path_arr[$i])];

            $i++;
        }

        $session[strtoupper($path_arr[$i])] = $val;

        return $val;

    }


}


/**
 * @param array $array
 * @param string $field
 * @return array
 */


function make_assoc($array,$field){

    if(!count($array)) return array();

    $keys = array_map(function($obj) use ($field){ return $obj->$field();}, $array);

    return array_combine($keys,array_values($array));

}

/**
 *
 * Make POST request to <code>url</code>.
 *
 *
 * @param $url string Target URL
 * @param $data array Assoc array with params
 * @return \stdClass  With fields <code>data</code>  containing response, <code>code</code> - HTTP status code.
 */


function curl_post($url,$data){

    $query_data = array();

    foreach ($data as $key => $val)
        $query_data[] = $key . "=" . $val;

    $query = implode("&",$query_data);

    if(defined('LOGGER')) Logger::print_debug(array('data' => $data,'qs' => $query));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

    $response = new \stdClass();

    $response->data = curl_exec($ch);

    $response->code = intval(curl_getinfo($ch,CURLINFO_HTTP_CODE));

    curl_close($ch);

    return $response;
}


/**
 *
 * Make get request to <code>url</code>.
 *
 * @param $url
 * @return \stdClass  With fields <code>data</code>  containing response, <code>code</code> - HTTP status code.
 */

function curl_get($url){

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = new \stdClass();

    $response->data = curl_exec($ch);

    $response->code = intval(curl_getinfo($ch,CURLINFO_HTTP_CODE));

    curl_close($ch);

    return $response;

}


/**
 *
 * Convert array to object recursively.
 *
 * @param $arr
 * @param int $deep Recursion depth
 * @return mixed
 */

function array_to_object($arr,$deep = 0){

   if(is_assoc_array($arr)){
       $data = new \stdClass();

       foreach($arr as $key => $val){
           if($deep)
            $data->$key = array_to_object($val,$deep-1);
           else
            $data->$key = $val;
       }

       return $data;
   }

   return $arr;

}

/**
 * Check whether array <code>arr</code> has standard int keys (0,1,...) or not.
 *
 * @param $arr
 * @return bool
 */

function is_assoc_array($arr){
    return ($arr !== array_values($arr));
}


/**
 *
 * Convert array of objects to array of 'filtered' objects - with only selected fields.
 *
 * @param $arr
 * @param $fields
 * @return array
 */


function array_select($arr, $fields){
    return array_map(function($el) use ($fields){
        $data = new \stdClass();
        foreach($fields as $f) $data->$f = $el->$f;
        return $data;
    },$arr);
}

/**
 *
 * Convert time (as number of seconds) to readable string with respect to current time (e.g. "now", "in 2 minutes").
 *
 * @param $time int
 *
 * @return bool|string
 */


function time_to_string($time){
  //todo:
    return date('d.m.Y H:i', $time);
}


/**
 * @param $length
 * @return string
 */

function rand_string($length) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

}