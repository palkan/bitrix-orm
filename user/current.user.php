<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 11:07
 */


namespace ru\teachbase;
require_once(dirname(__FILE__) . '/../partner/current.partner.php');
require_once(dirname(__FILE__) . '/user.php');


/**
 *
 * Contains all necessary info about current user (and store it in session).
 *
 *
 *
 * Class CurrentUser
 */


class CurrentUser
{

    /**
     * User's id
     *
     * @var int
     */

    public $id;

    /**
     * User's full name
     *
     * @var string
     */

    public $fullname;

    /**
     *
     * User's email (login)
     *
     * @var string
     */

    public $login;


    /**
     *
     * Current role
     *
     * @var int
     */

    public $role;

    /**
     *
     * Path to User photo if it exists.
     *
     * @var string|bool
     */

    public $photo;


    /**
     *
     * @var int
     */

    public $hints;


    /**
     *
     * @var bool
     */

    public $show_hints;


    /**
     *
     * @var CurrentPartner
     *
     */

    public $partner;

    private $_initialized = false;

    private $_is_guest = false;

    const PREFIX = 'tb_auth/user/';

    function __construct()
    {

        if (!class_exists('CUser')) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " CUser not found", "warning");
            return;
        }

        if (\CUser::IsAuthorized()) {

            $this->id = intval(\CUser::GetID());

            if(defined("LOGGER")) Logger::print_debug("Authorized: " . $this->id);

            if (session(self::PREFIX.'registered')) {

                $this->fullname = session(self::PREFIX . 'registered/name');
                $this->login = session(self::PREFIX . 'registered/login');
                $this->photo = session(self::PREFIX . 'registered/photo');
                $this->role = session(self::PREFIX . 'registered/role');
                $this->hints = session(self::PREFIX . 'registered/hints');
                $this->show_hints = session(self::PREFIX . 'registered/show_hints');

                $this->_initialized = true;

                $this->partner = new CurrentPartner();

                if(!$this->partner->initialized()){

                    Logger::print_debug("Req: ".strpos($_SERVER['REQUEST_URI'],'/account'));

                    if(strpos($_SERVER['REQUEST_URI'],'/account') !== 0){
                       LocalRedirect('/account/');
                    }
                }

            }else{

                $user = User::find_by_id($this->id);

                if ($user) {

                    $this->fullname = $user->name();
                    $this->login = $user->login();
                    $this->photo = $user->photo_path();
                    $this->hints = $user->hints();
                    $this->show_hints = $user->show_hints();

                    session(self::PREFIX . 'registered/photo', $this->photo);
                    session(self::PREFIX . 'registered/initialized', true);
                    session(self::PREFIX . 'registered/name', $this->fullname);
                    session(self::PREFIX . 'registered/login', $this->login);
                    session(self::PREFIX . 'registered/hints', $this->hints);
                    session(self::PREFIX . 'registered/show_hints', $this->show_hints);

                    $this->_initialized = true;

                    $this->partner = new CurrentPartner();

                    if (count($user->partners()) === 1)
                        $this->set_partner(reset($user->partners()));
                    elseif(count($user->partners()) > 1)
                        LocalRedirect('/account/');
                    else
                        CurrentUser::logout();

                }else{
                    Logger::log(__CLASS__ . ":" . __LINE__ . " User not found: id ".$this->id, "warning");
                }
            }

        } else {

            if (session(self::PREFIX.'guest')) {

                // already logged in as guest

                if(defined("LOGGER"))  Logger::print_debug("Login as returned guest");

                $this->id = session(self::PREFIX . 'guest/id');
                $this->fullname = session(self::PREFIX . 'guest/fullname');

                if($this->fullname) $this->_is_guest = true;

            } else {

                // Login as guest

                if(defined("LOGGER"))  Logger::print_debug("Login as guest from session");

                $this->id = session('sess_guest_id');

                session(self::PREFIX.'guest/id',$this->id);

            }

        }
    }


    public function set_partner(Relation $rel)
    {
        $partner = Partner::find_by_id($rel->element_id());

        if($partner){
            $this->role = $rel->role();
            session(self::PREFIX . 'registered/role', $this->role);
            $this->partner->login($partner);
        }else
            Logger::log("Partner doesn't exist: partner_id ".$partner->id().", user_id ".$this->id,"error");

    }


    /**
     * @param $name
     */

    public function set_guest_name($name)
    {

        if(!$name) return;

        $this->fullname = $name;
        session(self::PREFIX . 'guest/fullname', $this->fullname);

        $this->_is_guest = true;
    }


    public static function logout()
    {
        session(self::PREFIX, false);
        CurrentPartner::logout();

        if(\CUser::IsAuthorized()){
            \CUser::Logout();
        }
    }


    /**
     * @return int
     */

    public function is_specialist(){
        return ($this->role & UserRoles::SPECIALIST);
    }

    /**
     * @return int
     */

    public function is_manager(){
        return ($this->role & UserRoles::MANAGER);
    }

    /**
     * @return bool
     */

    public function user_initialized()
    {
        return $this->_initialized;
    }

    /**
     *
     * Return TRUE if user and partner are both initialized.
     *
     * @return bool
     */

    public function initialized()
    {
        return ($this->_initialized && $this->partner->initialized());
    }

    public function is_guest()
    {
        return $this->_is_guest;
    }

}