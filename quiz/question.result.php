<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 15:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/question.result.map.php');

/**
 * User answer and result
 *
 * Class QuestionResult
 */

class QuestionResult extends CustomORM{

    protected $_id;
    protected $_quiz_id;
    protected $_user_id;
    protected $_question_id;
    protected $_time = 0;
    protected $_score = 0;
    protected $_answer;
    protected $_success = false;
    protected $_complete = false;
    protected $_checked = false;
    protected $_attempt = 1;
    protected $_updated_at;


    public function id(){return $this->_id;}
    /**
     * ID of quiz
     *
     */

    public function quiz_id($val = null){return $this->_commit(__FUNCTION__,$val);}


    public function user_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    public function question_id($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     * Time spent
     *
     */

    public function time($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Score gained
     *
     */

    public function score($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * User's answer
     *
     */

    public function answer($val = null){return $this->_commit(__FUNCTION__,$val);}


    public function success($val = null){return $this->_commit(__FUNCTION__,$val);}



    public function complete($val = null){return $this->_commit(__FUNCTION__,$val);}



    public function checked($val = null){return $this->_commit(__FUNCTION__,$val);}


    public function attempt($val = null){return $this->_commit(__FUNCTION__,$val);}



    public function updated_at($val = null){return $this->_commit(__FUNCTION__,$val);}


    function __construct(){
        parent::__construct();
    }

}

BitrixORM::registerMapClass(new QuestionResultMap(), QuestionResult::className());