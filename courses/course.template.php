<?php
/**
 * User: palkan
 * Date: 02.09.13
 * Time: 10:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/course.template.map.php');

class CourseTemplate extends IBlockORM{

    protected $_partner_id;

    protected $_is_public;

    protected $_editable;

    protected $_pages_num = 0;

    protected $_cover_img;

    protected $_pages;

    /**
     * @param null $val
     * @return mixed
     */

    public function is_public($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * @param null $val
     * @return mixed
     */

    public function editable($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * @param null $val
     * @return mixed
     */

    public function partner_id($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * @param null $val
     * @return mixed
     */

    public function pages_num($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * @param null $val
     * @return mixed
     */

    public function cover_img($val = null){return $this->_commit(__FUNCTION__,$val);}

    /**
     * @param bool $reload
     * @return mixed|null
     */

    public function pages($reload = false){
        if(!isset($this->_id)) return null;

        if(!isset($this->_pages) || $reload)
            $this->_pages = CoursePage::find(filter()->by_course_id($this->_id),navi()->order_by_index('asc'));

        return $this->_pages;
    }

    function __construct(){
        parent::__construct();
    }


    /**
     * @param $type
     * @param $index
     * @param bool|string $title
     * @param bool|string $audio
     * @param null $data
     * @return bool|CoursePage
     */

    public function addPage($type,  $index = -1, $title = false, $audio = false, $data = null){

        $page = new CoursePage();
        $page->course_id($this->_id);
        $page->active(true);
        $page->index($index>-1 ? $index : $this->_pages_num+1);
        $page->type($type);
        $page->partner_id($this->_partner_id);
        $page->has_audio($audio ? true : false);
        $audio && $page->audio($audio);
        $page->has_title($title ? true : false);
        $title && $page->title($title);

        $page->name($this->_name."_p".$page->index());

        $page->data($data);

        if($page->save()){

            $this->pages_num($this->_pages_num+1);

            $this->save();

            $this->pages(true);

            return $page;
        }else
            return false;

    }


    /**
     * @param $id
     * @return bool
     */


    public function removePage($id){

        $page = CoursePage::find_by_id($id);

        if(!$page) return true;

        $page->delete();

        $this->pages_num($this->_pages_num-1);

        if(!$this->save()) return false;

        return true;

    }


    public function jsonData(){

        $data = parent::jsonData();

        if($this->_pages) $data->pages = array_map(function($p){ return $p->jsonData();}, array_values($this->_pages));

        return $data;
    }

}

BitrixORM::registerMapClass(new CourseTemplateMap(),CourseTemplate::className());