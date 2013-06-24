<?php
/**
 * User: palkan
 * Date: 22.04.13
 * Time: 15:42
 */
namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/question.map.php');
require_once(dirname(__FILE__) . '/quiz.template.php');

/**
 * Quiz question
 *
 * Class Question
 */

class Question extends IBlockORM{


    protected $_quiz_id;
    protected $_type;
    protected $_time = 0;
    protected $_score = 1;
    protected $_answer;
    protected $_options;
    protected $_media;

    /**
     * ID of quiz template
     */

    public function quiz_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Question type
     *
     * @see QuestionType
     */

    public function type($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Time limit for question
     *
     */

    public function time($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Max score for question
     *
     */

    public function score($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     *
     * Answer for question (depends on question type)
     *
     */

    public function answer($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * Question options (depends on question type)
     *
     */

    public function options($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     * Array of QMedia
     *
     * @see QMedia
     */

    public function media($val = null){return $this->_commit(__FUNCTION__,$val);}


    /**
     * @var QuizTemplate
     */

    private $__quiz;


    function __construct(){
        parent::__construct(new QuestionMap());
    }


    /**
     * @return bool|mixed|QuizTemplate
     */


    public function quiz(){
        if(is_null($this->__quiz)){
            $this->__quiz = QuizTemplate::find_by_id($this->quiz_id);
        }

        return $this->__quiz;
    }

}

/**
 *
 * Question types constants
 *
 * Class QuestionType
 */

class QuestionType{

    const SHORT = "short";
    const MCHOICE = "multiple_choice";
    const SCHOICE = "single_choice";
    const ORDER = "order";
    const MATCH = "match";
    const OPEN = "open";

}


class QMedia implements tSerializable
{

    public $type;
    public $src;
    protected $path;

    function __construct($type, $src)
    {

        $this->type = $type;
        if(intval($src))
            $this->src = intval($src);
        elseif(!empty($src))
            $this->path = $src;
    }


    public function path(){

        if(empty($this->path) && intval($this->src)>0){
               $this->path = CFile::GetPath($this->src);
        }

        return $this->path;
    }


    public function jsonData(){

        $data = new stdClass();
        $data->type = $this->type;
        $data->path = $this->path();
        $data->src = $this->src;

        return $data;
    }

    public function __sleep(){

        // clean path if we have file id

        if(intval($this->src))
            $this->path = null;

        return array('type','src','path');

    }


}



class QOption implements tSerializable{

    public $text;
    protected  $src;
    protected $path;

    public $key='';


    /**
     * We need this flag to prevent double encoding of the same object on save
     *
     * @var bool
     */

    private $encoded = false;

    function __construct($txt,$img = false){
        $this->text =$txt;

        if(intval($img))
            $this->src = $img;
        elseif(!empty($img))
            $this->path = $img;

        $this->key = substr(md5('key'.rand(0,100).'random'),0,4).substr(md5('key'.rand().'rand'),2,6);
    }


    public function path(){

         if(empty($this->path) && intval($this->src)>0){
            $this->path = CFile::GetPath($this->src);
         }

         return $this->path;
    }


    public function jsonData(){

        $data = new stdClass();
        $data->text = $this->text;
        $data->src = $this->src;
        $data->key = $this->key;
        $data->path = $this->path();

        return $data;
    }

    public function __sleep(){
        if(!$this->encoded){
            $this->text = htmlentities($this->text,ENT_NOQUOTES,'UTF-8');
            $this->encoded = true;
        }

        // clean path if we have file id

        if(intval($this->src))
            $this->path = null;

        return array('text','src','path','key');
    }

    public function __wakeup(){
        $this->text = html_entity_decode($this->text,ENT_NOQUOTES,'UTF-8');
    }
}
