<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/tariff.map.php');

/**
 * Tariff model
 *
 */

class Tariff extends IBlockORM{


    protected $_user_limit;
    protected $_disk_limit;
    protected $_code;
    protected $_price;

    /**
     * Tariff code (e.g. 'demo').
     *
     * @param null $val
     * @return mixed
     */

    public function code($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     */

    public function user_limit($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     */

    public function disk_limit($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     */

    public function price($val = null){return $this->_commit(__FUNCTION__,$val);}
}


BitrixORM::registerMapClass(new TariffMap(),Tariff::className());