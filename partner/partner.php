<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/partner.map.php');
require_once(dirname(__FILE__).'/tariff.php');

/**
 * Partner model
 */

class Partner extends Assignable{


    protected $_tariff_id;
    protected $_is_aps;
    protected $_aps_id;
    protected $_logo;
    protected $_subdomain;
    protected $_info_id;

    /**
     *
     * Tariff ID
     *
     */

    public function tariff_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Define whether Partner registered thru APS
     *
     */

    public function is_aps($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * APS ID
     *
     */

    public function aps_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Partner logo file ID
     *
     */

    public function logo($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Subdomain
     *
     */

    public function subdomain($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * ID of info object     //TODO: not implemented yet
     *
     */

    public function info_id($val = null){return $this->_commit(__FUNCTION__,$val);}


    private $_logo_path;

    private $_tariff;


    function __construct(){
        parent::__construct();
    }


    /**
     *
     * Return a path to Partner's logo if it exists.
     *
     * @return bool|string
     */


    public function logo_path(){

        if(is_null($this->_logo_path)) $this->_logo_path = intval($this->_logo) ? \CFile::GetPath($this->_logo) : false;

        return $this->_logo_path;


    }

    /**
     * @return bool|mixed|null
     */

    public function tariff(){

        if(!$this->_tariff_id) return null;

        if(!$this->_tariff) $this->_tariff = Tariff::find_by_id($this->_tariff_id);

        return $this->_tariff;
    }


    /**
     * @param $partner_id
     * @param $user_id
     * @param int $role
     * @return Relation|bool
     */

    public static function addUser($partner_id, $user_id, $role = UserRoles::LISTENER){

        $rel = new Relation();

        $rel->code(BitrixORM::assignCodeByClass(Partner::className()));
        $rel->role($role);
        $rel->element_id($partner_id);
        $rel->user_id($user_id);

        return $rel->save();
    }


    /**
     * @param $partner_id
     * @param $user_id
     * @param $new_role
     * @return bool|Relation
     */

    public function changeRole($partner_id, $user_id, $new_role){

        $rel = AssignManager::find($user_id,$partner_id);

        if(!$rel){
            Logger::log(__CLASS__.": Cannot change role, relation doesn't exist: user_id ".$user_id.", partner_id ".$partner_id, "error");
            return false;
        }

        $rel->role($new_role);

        return $rel->save();
    }


    /**
     * @param $partner_id
     * @param $user_id
     * @return bool
     */

    public static function removeUser($partner_id, $user_id){
        return AssignManager::delete($user_id,$partner_id);
    }


    public function jsonData(){

        $data = parent::jsonData();

        $data->tariff = $this->_tariff ? $this->_tariff->jsonData() : null;

        return $data;
    }



}


BitrixORM::registerMapClass(new PartnerMap(),Partner::className());