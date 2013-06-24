<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/partner.map.php');

/**
 * Partner model
 *
 * Class Question
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



    function __construct(){
        parent::__construct(new PartnerMap());
    }


    /**
     *
     * Return a path to Partner's logo if it exists.
     *
     * @return bool|string
     */


    public function logo_path(){

        if(is_null($this->_logo_path)) $this->_logo_path = intval($this->_logo) ? CFile::GetPath($this->_logo) : false;

        return $this->_logo_path;


    }


    public function get_user_role(int $id){
        return UserRoles::LISTENER;
    }


}