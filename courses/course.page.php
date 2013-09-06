<?php
/**
 * User: palkan
 * Date: 02.09.13
 * Time: 10:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/course.page.map.php');

class CoursePage extends IBlockORM{

    protected $_partner_id;

    protected $_type;

    protected $_has_title;

    protected $_title;

    protected $_has_audio;

    protected $_audio;

    protected $_thumb;

    protected $_index;

    protected $_data;

    protected $_course_id;

    public function type($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function has_title($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function partner_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function title($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function index($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function thumb($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function data($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function has_audio($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function audio($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function course_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    function __construct(){
        parent::__construct();
    }
}


class PageTypes{

    const IMAGE = "image";
    const FLASH = "flash";
    const VIDEO = "video";
    const CUSTOM = "custom";
    const HTML = "html";

}

BitrixORM::registerMapClass(new CoursePageMap(),CoursePage::className());