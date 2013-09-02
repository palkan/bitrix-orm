<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 11:07
 */


namespace ru\teachbase;
require_once(dirname(__FILE__).'/../partner/current.partner.php');
require_once(dirname(__FILE__).'/user.php');


/**
 *
 * Contains all necessary info about current user (and store it in session).
 *
 *
 *
 * Class CurrentUser
 */


class CurrentUser{

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

    const PREFIX = 'sess_auth/user/';

    function __construct(){

        if(!class_exists('CUser')) return;

        if(\CUser::IsAuthorized())
        {
            $this->id = intval(\CUser::GetID());

            if(session(self::PREFIX)){

                // some vars we share with Bitrix vars

                $this->fullname = session('sess_auth/name');
                $this->login = session('sess_auth/login');


                $this->photo = session(self::PREFIX.'photo');
                $this->role = session(self::PREFIX.'role');
                $this->hints = session(self::PREFIX.'hints');
                $this->show_hints = session(self::PREFIX.'show_hints');

                $this->_initialized = true;

                $this->partner = new CurrentPartner();

            }else{

               $user = User::find_by_id($this->id);



                if($user){

                    $this->fullname = session('sess_auth/name');
                    $this->login = session('sess_auth/login');

                    $this->photo = $user->photo_path();
                    $this->hints = $user->hints();
                    $this->show_hints = $user->show_hints();

                    session(self::PREFIX.'photo',$this->photo);
                    session(self::PREFIX.'initialized',true);
                    session(self::PREFIX.'hints',$this->hints);
                    session(self::PREFIX.'show_hints',$this->show_hints);

                    $this->_initialized = true;

                    $this->partner = new CurrentPartner();

                   if(count($user->partners()) === 1) $this->SetPartner(reset($user->partners()));

                }



            }

        }
    }


    public function SetPartner(Partner $partner){

      $this->partner->login($partner);

       $this->role = $partner->get_user_role($this->id);

       session(self::PREFIX.'role',$this->role);

    }


    public static function logout(){
        session(self::PREFIX,false);
        CurrentPartner::logout();
    }

    public function initialized(){
        return $this->_initialized;
    }

}