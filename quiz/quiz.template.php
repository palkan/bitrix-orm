<?php
/**
 * User: VOVA
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/quiz.template.map.php');

class QuizTemplate extends IBlockORM{

    protected $_is_public;

    protected $_is_survey;

    protected $_max_score;

    protected $_ques_num;

    public function is_public($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function is_survey($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function max_score($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function ques_num($val = null){return $this->_commit(__FUNCTION__,$val);}

    function __construct(){
        parent::__construct(new QuizTemplateMap());
    }
}