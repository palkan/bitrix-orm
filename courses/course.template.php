<?php
/**
 * User: palkan
 * Date: 02.09.13
 * Time: 10:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/course.template.map.php');

class CourseTemplate extends IBlockORM{

    protected $_is_public;

    protected $_is_survey;

    protected $_max_score;

    protected $_ques_num;

    public function is_public($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function is_survey($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function max_score($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function ques_num($val = null){return $this->_commit(__FUNCTION__,$val);}

    function __construct(){
        parent::__construct();
    }
}

BitrixORM::registerMapClass(new HintMap(),Hint::className());