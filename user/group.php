<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(__DIR__ . '/../maps/group.map.php');

/**
 * Users group.
 *
 * Temporary  class for backward compatibility.
 */

class Group extends IBlockORM{


    protected $_partner_id;
    protected $_user_ids;

    /**
     *
     * Partner ID
     *
     */

    public function partner_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Users ids
     *
     */

    public function user_ids($val = null){return $this->_commit(__FUNCTION__,$val);}

    function __construct(){
        parent::__construct();
    }
}


BitrixORM::registerMapClass(new GroupMap(),Group::className());