<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/hint.map.php');

class Hint extends IBlockORM{

    protected $_code = 0;

    protected $_url_matcher = '';

    protected $_video;

    protected $_preview;

    private $_preview_path;

    public function code($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function url_matcher($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function video($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function preview($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function preview_path(){

        if(is_null($this->_preview_path)) $this->_preview_path = intval($this->_preview) ? \CFile::GetPath($this->_preview) : false;

        return $this->_preview_path;

    }

    function __construct(){
        parent::__construct(new HintMap());
    }
}
