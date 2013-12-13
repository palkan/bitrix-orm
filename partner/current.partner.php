<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 11:07
 */

namespace ru\teachbase;
require_once(dirname(__FILE__).'/partner.php');


/**
 *
 * Contains info about current partner (and store it in session).
 *
 * Dislike CurrentUser CurrentPartner initializes only when User choose Partner (if there are several Partners assigned to User).
 *
 * Class CurrentPartner
 */


class CurrentPartner{

    /**
     * @var int
     */

    public $id=0;

    /**
     * @var string
     */

    public $name="";


    /**
     * Path to Partner's logo if it exists.
     *
     *
     * @see Partner::logo_path()
     * @var string|bool
     */

    public $logo="/bitrix/templates/main/images/bg/logo.png";


    /**
     * @var string|bool
     */

    public $subdomain="";


    private $_initialized = false;


    const PREFIX = 'tb_auth/user/partner/';


    function __construct(){

       if(defined("LOGGER")) Logger::print_debug(session(self::PREFIX));

       if(session(self::PREFIX)){

           foreach(get_object_vars($this) as $key => $val)
               $this->$key = session(self::PREFIX.$key);

           if(intval($this->id)) $this->_initialized = true;

       }

    }


    /**
     * @return bool
     */

    public function initialized(){

        return $this->_initialized;

    }


    /**
     *
     * Login user to the Partner.
     *
     * Stores Partner data in session.
     *
     * @param Partner $partner
     */

    public function login(Partner $partner){

        $this->id = session(self::PREFIX.'id',$partner->id());
        if($partner->logo_path()) $this->logo = session(self::PREFIX.'logo',$partner->logo_path());
        else  session(self::PREFIX.'logo',$this->logo);
        $this->name = session(self::PREFIX.'name',$partner->name());
        $this->subdomain = session(self::PREFIX.'subdomain',$partner->subdomain());
        $this->_initialized = true;
        session(self::PREFIX.'initialized',true);
    }

    /**
     *
     * Clear Partner data from session.
     *
     */

    public static function logout(){

        session(self::PREFIX,false);

    }


}