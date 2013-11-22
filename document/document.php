<?php
/**
 * User: palkan
 * Date: 02.09.13
 * Time: 10:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/document.map.php');
require_once(dirname(__FILE__) . '/../utils/file.php');
require_once(dirname(__FILE__) . '/../common/conversion.manager.php');

class Document extends Assignable
{

    protected $_partner_id;

    protected $_extension;

    protected $_size;

    protected $_parent_id;

    protected $_thumb;

    protected $_context_type = DocumentContext::NONE;

    protected $_context_id;

    protected $_data;

    protected $_type;

    protected $_file;


    // --------- Converted files urls ----------//

    protected $_thumbs;

    protected $_pages;

    protected $_swfs;

    protected $_pdf;

    /**
     * @param null $val
     * @return mixed
     */

    public function file($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }


    /**
     * @param null $val
     * @return mixed
     */

    public function type($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }


    /**
     * @param null $val
     * @return mixed
     */

    public function extension($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function size($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function partner_id($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function thumb($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function context_type($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function context_id($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function parent_id($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function data($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @return string|null
     */

    public function pdf()
    {
        if ($this->_data) return $this->_data->pdf;
        return null;
    }


    /**
     *
     * Return array of thumbs urls or null.
     *
     * @return array|null
     */

    public function thumbs()
    {

        if (!$this->_data || !$this->_data->thumbs) return null;

        if (!$this->_thumbs) {
            $response = curl_get($this->_data->thumbs);

            if ($response->code == 200) {

                $this->_thumbs = array();

                $data = json_decode($response->data, false);

                $dir = self::path2dir($this->_data->thumbs);

                foreach ($data->thumbs as $t) $this->_thumbs[] = $dir . $data->dir . '/' . $t;

            } else Logger::log(__CLASS__ . ":" . __LINE__ . " Cannot load thumbs json: " . $response->code . ", path: " . $this->_data->thumbs.", id: ".$this->_id, "warning");

        }

        return $this->_thumbs;

    }

    /**
     * Return array of pages urls or null.
     *
     * @return array|null
     */

    public function pages()
    {

        if (!$this->_data || !$this->_data->pages) return null;

        if (!$this->_pages) {
            $response = curl_get($this->_data->pages);

            if ($response->code == 200) {

                $this->_pages = array();

                $data = json_decode($response->data, false);

                $dir = self::path2dir($this->_data->pages);

                foreach ($data->pages as $t) $this->_pages[] = $dir . $data->dir . '/' . $t;

            } else Logger::log(__CLASS__ . ":" . __LINE__ . " Cannot load pages json: " . $response->code . ", path: " . $this->_data->pages.", id: ".$this->_id, "warning");

        }

        return $this->_pages;
    }


    /**
     * Return array of swfs urls or null.
     *
     * @return array|null
     */

    public function swfs()
    {

        if (!$this->_data || !$this->_data->swf) return null;

        if (!$this->_swfs) {
            $response = curl_get($this->_data->swf);

            if ($response->code == 200) {

                $this->_swfs = array();

                $data = json_decode($response->data, false);

                $dir = self::path2dir($this->_data->swf);

                foreach ($data->slides as $t) $this->_swfs[] = $dir . $data->dir . '/' . $t;

            } else Logger::log(__CLASS__ . ":" . __LINE__ . " Cannot load pages json: " . $response->code . ", path: " . $this->_data->swf.", id: ".$this->_id, "warning");

        }

        return $this->_pages;
    }


    /**
     *
     * Build new Document from file array.
     *
     * @param $file_arr
     * @return null|Document
     */

    static function build($file_arr)
    {

        if (!class_exists('CFile')) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " CFile is not found", "error");
            return null;
        }


        $real_name = $file_arr['name'];

        $file_arr['name'] = self::translit($file_arr['name']);

        Logger::print_debug($file_arr);

        $fid = \CFile::SaveFile($file_arr, 'library');

        if ($fid) {

            $doc = new Document();

            $file = new File($fid);

            Logger::print_debug("New File Id: $fid");
            //$file->fromBitrixData(\CFile::GetFileArray($fid));

            $doc->file($file);
            $doc->size(intval($file_arr['size']));

            $name_ext = self::path2name_ext($real_name);

            $doc->extension($name_ext->ext);
            $doc->name($name_ext->name);

            $doc->type(DocumentType::extension2type($doc->extension()));

            if($doc->extension() == "pdf"){
                $doc_data = new \stdClass();
                $doc_data->pdf = $doc->file()->path;
                $doc->data($doc_data);
            }

            return $doc;
        } else {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Error building file: " . $real_name, "error");
        }

        return null;
    }


    function __construct()
    {
        parent::__construct();
    }


    /**
     * @return bool|void
     */

    public function delete()
    {

        if (class_exists('CFile')) {

            if ($this->_file) {
                \CFile::Delete($this->_file->id);
            }

            if ($this->_thumb) {
                \CFile::Delete($this->_thumb->id);
            }
        } else
            Logger::log(__CLASS__ . ":" . __LINE__ . " CFile is not found; id: " . $this->_id, "error");

        parent::delete();
    }


    public function jsonData(){

        $data = parent::jsonData();

        $data->pages = $this->_pages;

        $data->thumbs = $this->_thumbs;

        $data->swfs = $this->_swfs;

        $data->pdf = $this->pdf();

        $data->file = $this->_file ? $this->_file->path : "";

        $data->thumb = $this->_thumb ? $this->_thumb->path : "";

        return $data;
    }


    /**
     * Run additional services: convert to other formats, create thumbs etc.
     *
     * Depends on type.
     *
     */


    public function prepare()
    {
        //todo:
    }

    /**
     * @param $path string
     */

    public function build_thumb_from_path($path)
    {

        if (!class_exists('CFile')) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " CFile is not found; id: " . $this->_id, "error");
            return;
        }

        $file_arr = \CFile::MakeFileArray($path);

        Logger::log("Path: $path","warning");

        $fid = \CFile::SaveFile($file_arr, 'library/thumbs');

        if ($fid) {

            if(!is_null($this->_thumb)) \CFile::Delete($this->_thumb->id);

            $file = new File($fid);
            $this->thumb($file);
        } else
            Logger::log(__CLASS__ . ":" . __LINE__ . " Error building thumb: $path, id: ".$this->_id, "error");
    }

    /**
     *
     * Run converter task to create pdf from file.
     *
     * @param array $listeners  Additional URLs to invoke on task complete.
     * @return bool
     */

    public function build_pdf($listeners = array())
    {

        if (!($this->_type == DocumentType::DOCUMENT
                || $this->_type == DocumentType::PRESENTATION
                || $this->_type == DocumentType::TABLE) || $this->_extension === "pdf"
        ) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Action is not possible, this type is " . $this->_type.", id: ".$this->_id, "error");
            return false;
        }


        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key.", id: ".$this->_id, "error");
            return false;
        }

        if ($this->_data && $this->_data->pdf) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Already exists; id: " . $this->_id, "error");
            return false;
        }

        $key = ConversionManager::fyler_convert($this->_id, 'doc_to_pdf', $this->_file->path, array(), $listeners);

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;

    }


    /**
     *
     * Run converter task to create thumb image.
     *
     * @param array $listeners  Additional URLs to invoke on task complete.
     * @return bool
     */


    public function build_thumb($listeners = array())
    {

        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key.", id: ".$this->_id, "error");
            return false;
        }

        if($this->_type == DocumentType::DOCUMENT
            || $this->_type == DocumentType::PRESENTATION
            || $this->_type == DocumentType::TABLE)
            return $this->build_thumbs($listeners);//$key = ConversionManager::fyler_convert($this->_id, 'pdf_thumb', $this->pdf(), array(), $listeners);
        elseif($this->_type == DocumentType::IMAGE)
            $key = ConversionManager::fyler_convert($this->_id, 'image_thumb', $this->_file->path, array(), $listeners);
        else{
            Logger::log(__CLASS__ . ":" . __LINE__ . " Action is not possible, this type is " . $this->_type.", id: ".$this->_id, "warning");
            return false;
        }

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;

    }


    /**
     *
     * Run converter task to create thumbs of all file's pages.
     *
     * @param array $listeners  Additional URLs to invoke on task complete.
     * @return bool
     */


    public function build_thumbs($listeners = array())
    {

        if (!($this->_type == DocumentType::DOCUMENT
            || $this->_type == DocumentType::PRESENTATION
            || $this->_type == DocumentType::TABLE)
        ) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Action is not possible, this type is " . $this->_type.", id: ".$this->_id, "error");
            return false;
        }


        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key.", id: ".$this->_id, "error");
            return false;
        }

        if ($this->_data && $this->_data->thumbs) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Already exists; id: " . $this->_id, "error");
            return false;
        }

        if ($this->pdf()) $key = ConversionManager::fyler_convert($this->_id, 'pdf_to_thumbs', $this->pdf(), array(), $listeners);
        else $key = ConversionManager::fyler_convert($this->_id, 'doc_to_pdf_thumbs', $this->_file->path, array(), $listeners);

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;

    }

    /**
     *
     * Run converter task to create jpeg pages from file and remove some pages from file (if <code>split_data</code> is provided)
     *
     * @param array $listeners   Additional URLs to invoke on task complete.
     * @param null|string $split_data   String containing info about pages to include in result pdf file. For example, "1-4,10-end" - include all but pages 5-9.
     * @return bool
     */


    public function build_pages($listeners = array(), $split_data = null)
    {

        if (!$this->pdf()) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Action is not possible, pdf not found, id: ".$this->_id, "error");
            return false;
        }


        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key.", id: ".$this->_id, "error");
            return false;
        }

        if ($this->_data && $this->_data->pages) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Already exists; id: " . $this->_id, "error");
            return false;
        }

        if(is_null($split_data))
            $key = ConversionManager::fyler_convert($this->_id, 'pdf_to_pages', $this->pdf(),array(),$listeners);
        else
            $key = ConversionManager::fyler_convert($this->_id, 'pdf_split_pages', $this->pdf(), array("split" => $split_data),$listeners);

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;

    }

    /**
     * @param array $listeners  Additional URLs to invoke on task complete.
     * @return bool
     */


    public function build_swf($listeners = array())
    {

        if (!($this->_type == DocumentType::DOCUMENT
            || $this->_type == DocumentType::PRESENTATION
            || $this->_type == DocumentType::TABLE)
        ) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Action is not possible, this type is " . $this->_type.", id: ".$this->_id, "error");
            return false;
        }


        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key.", id: ".$this->_id, "error");
            return false;
        }


        if ($this->pdf()){
            if ($this->_data && $this->_data->thumbs)
                $key = ConversionManager::fyler_convert($this->_id, 'pdf_to_swf', $this->pdf(),array(),$listeners);
            else
                $key = ConversionManager::fyler_convert($this->_id, 'pdf_to_swf_thumbs', $this->pdf(),array(),$listeners);
        }
        else $key = ConversionManager::fyler_convert($this->_id, 'doc_to_pdf_swf', $this->_file->path,array(),$listeners);

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;
    }


    /**
     *
     * Run converter task to create hls from meeting recording flv video.
     *
     * @param array $listeners  Additional URLs to invoke on task complete.
     * @return bool
     */

    public function build_hls_recording($listeners = array())
    {

        if ($this->_type != DocumentType::RECORDING)
        {
            Logger::log(__CLASS__ . ":" . __LINE__ . " Action is not possible, this type is " . $this->_type.", id: ".$this->_id, "error");
            return false;
        }


        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key.", id: ".$this->_id, "error");
            return false;
        }


        if (!$this->_data || !property_exists($this->_data,"stream_to_convert")) {
            Logger::log("No stream to convert in data, id: ".$this->_id, "error");
            return false;
        }

        $key = ConversionManager::fyler_convert($this->_id, 'flv_to_hls', $this->_data->url.$this->_data->stream_to_convert->name.".flv", array("target_dir" => $this->_data->url.$this->_data->stream_to_convert->name."/", "stream_type" => $this->_data->stream_to_convert->type), $listeners);

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->converting = true;
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;

    }


    public function conversion_complete($result)
    {

        if ($result->status == "ok" && $result->aws === false) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " We can not process not aws files now: " . implode(",", $result->path).", id: ".$this->_id, "error");
            return;
        }

        $data = $this->data() ? $this->data() : new \stdClass();
        if ($data->task_key) {
            unset($data->task_key);
        }
        $this->data($data);

        if($result->status == "failed"){
            Logger::log(__CLASS__ . ":" . __LINE__ . " task failed; task: ".json_encode($result), "error");
            return;
        }

        $handler = $result->type;

        $this->$handler($result);

        $this->save();
    }


    ///---------- Private functions for handling conversion results --------///

    /**
     *
     * @see image_thumb
     * @param $result
     */

    private function pdf_thumb($result)
    {
       $this->image_thumb($result);
    }


    /**
     * @param $result mixed Contains thumb URL as <code>$result->data->path[0]</code>.
     */

    private function image_thumb($result){
        $this->build_thumb_from_path("http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$result->data->path[0]);
    }

    /**
     * @param $result mixed Contains URL to thumbs JSON config as  <code>$result->data->path[0]</code>.
     */

    private function pdf_to_thumbs($result)
    {
        $data = $this->data();

        $data->thumbs = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$result->data->path[0];

        $this->data($data);

        if (!$this->_thumb && $this->thumbs() && count($this->thumbs())) {
            $this->build_thumb_from_path(current($this->thumbs()));
        }
    }

    /**
     * Convert pdf to jpeg pages.
     *
     * @param $result mixed Contains URL to pages JSON config as  <code>$result->data->path[0]</code>.
     */

    private function pdf_to_pages($result)
    {
        $data = $this->data();

        $data->pages = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$result->data->path[0];

        $this->data($data);
    }


    /**
     * Remove pages from pdf and create thumbs and pages.
     *
     * @param $result mixed Contains URLs to new pdf file, pages JSON config and thumbs JSON config in <code>$result->data->path</code>.
     */

    private function pdf_split_pages($result)
    {
        $data = $this->data();

        foreach ($result->data->path as $p) {

            if (preg_match('/^.+\.pdf$/i', $p)) $data->pdf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+\pages.json$/i', $p)) $data->pages = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+\thumbs.json$/i', $p)) {
                $data->thumbs = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

                $this->data($data);

                if ($this->thumbs() && count($this->thumbs())) {
                    $this->build_thumb_from_path(current($this->thumbs()));
                }

            }

        }

        $this->data($data);
    }

    /**
     *
     * Convert pdf to swf movies.
     *
     * @param $result mixed Contains URL to swfs JSON config as  <code>$result->data->path[0]</code>.
     */

    private function pdf_to_swf($result)
    {
        $data = $this->data();

        $data->swf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$result->data->path[0];

        $this->data($data);
    }

    /**
     * Convert pdf to swf movies and create thumbs.
     *
     * @param $result mixed Contains URLs to swfs JSON config and thumbs JSON config  in array <code>$result->data->path</code>.
     */

    private function pdf_to_swf_thumbs($result)
    {
        $data = $this->data();

        foreach ($result->data->path as $p) {

            if (preg_match('/^.+swfs\.json$/i', $p)) $data->swf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+thumbs\.json$/i', $p)) {
                $data->thumbs = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

                $this->data($data);

                if (!$this->_thumb && $this->thumbs() && count($this->thumbs())) {
                    $this->build_thumb_from_path(current($this->thumbs()));
                }

            }

        }

        $this->data($data);
    }

    /**
     * @param $result mixed Contains URL to pdf as  <code>$result->data->path[0]</code>.
     */

    private function doc_to_pdf($result)
    {
        $data = $this->data();
        foreach ($result->data->path as $p) {
            if (preg_match('/^.+\.pdf$/i', $p)) $data->pdf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+\.png$/i', $p)) $this->build_thumb_from_path("http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p);
        }
        $this->data($data);
    }

    /**
     * Convert pdf to swf movies and create thumbs.
     *
     * @param $result mixed Contains URLs to pdf file and thumbs JSON config  in array <code>$result->data->path</code>.
     */

    private function doc_to_pdf_thumbs($result)
    {
        $data = $this->data();

        foreach ($result->data->path as $p) {
            if (preg_match('/^.+\.pdf$/i', $p)) $data->pdf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+\.json$/i', $p)) {
                $data->thumbs = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

                $this->data($data);

                if ($this->thumbs() && count($this->thumbs())) {
                    $this->build_thumb_from_path(current($this->thumbs()));
                }

            }

        }

        $this->data($data);
    }

    /**
     * @param $result mixed Contains URLs to pdf file, thumbs JSON config and swf JSON config  in array <code>$result->data->path</code>.
     */


    private function doc_to_pdf_swf($result)
    {
        $data = $this->data();

        foreach ($result->data->path as $p) {
            if (preg_match('/^.+\.pdf$/i', $p)) $data->pdf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+swfs\.json$/i', $p)) $data->swf = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

            if (preg_match('/^.+thumbs\.json$/i', $p)) {
                $data->thumbs = "http://" . $result->bucket . ".s3.amazonaws.com/" . $result->data->dir.'/'.$p;

                $this->data($data);

                if (!$this->_thumb && $this->thumbs() && count($this->thumbs())) {
                    $this->build_thumb_from_path(current($this->thumbs()));
                }

            }

        }

        $this->data($data);
    }


    /**
     *
     * Convert flv_to_hls
     *
     * @param $result mixed Contains URL to M3U8 playlist as <code>$result->data->path[0]</code>.
     */

    private function flv_to_hls($result)
    {
        $data = $this->data();

        if($this->_type == DocumentType::RECORDING){
            unset($data->stream_to_convert);

            if(count($data->streams)){

                $data->stream_to_convert = current($data->streams);

                $this->data($data);

                if($this->build_hls_recording() !== false){
                    $data = $this->data();
                    array_shift($data->streams);
                }else{
                    Logger::log("Failed to run new task for converting another hls stream, id: ".$this->_id, "error");
                }
            }else
                $data->converting = false;
        }else{
            Logger::log("flv_to_hls for not recording is not implemented yet, id: ".$this->_id, "error");
        }

        $this->data($data);
    }

    ///------------------------------------------------------------------------///





    /**
     * @param $path
     * @return \stdClass {ext: string, name: string}
     */

    public static function path2name_ext($path)
    {

        $res = new \stdClass();

        if (preg_match('/^(?:[^:]+:\/\/)?(?:.+\/)?([^\/]+)\.([\w\d]+)$/', $path, $matches)) {

            $res->name = $matches[1];
            $res->ext = $matches[2];

        } else {
            $res->name = $path;
            $res->ext = '';
        }

        return $res;
    }

    /**
     * @param $path
     * @return string
     */

    public static function path2dir($path)
    {

        $res = '';

        if (preg_match('/^((?:[^:]+:\/\/)?(?:.+\/))?[^\/]+\.[\w\d]+$/', $path, $matches)) {
            $res = $matches[1];
        }

        return $res;
    }


    public static function translit($str)
    {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
        );
        return strtr($str, $tr);
    }


}


class DocumentContext
{
    const NONE = 'none';
    const COURSE = 'course';
    const QUIZ = 'quiz';
    const MEETING = 'meeting';
}


class DocumentType
{
    const OTHER = 'other';
    const FOLDER = 'folder';
    const IMAGE = 'image';
    const VIDEO = 'video';
    const AUDIO = 'audio';
    const PRESENTATION = 'presentation';
    const DOCUMENT = 'document';
    const TABLE = 'table';
    const RECORDING = 'recording';

    static $ext2type = array(
        'doc' => DocumentType::DOCUMENT,
        'docx' => DocumentType::DOCUMENT,
        'odt' => DocumentType::DOCUMENT,
        'rtf' => DocumentType::DOCUMENT,
        'pdf' => DocumentType::DOCUMENT,
        'xls' => DocumentType::TABLE,
        'xlsx' => DocumentType::TABLE,
        'ods' => DocumentType::TABLE,
        'png' => DocumentType::IMAGE,
        'jpg' => DocumentType::IMAGE,
        'jpeg' => DocumentType::IMAGE,
        'gif' => DocumentType::IMAGE,
        'bmp' => DocumentType::IMAGE,
        'mpg' => DocumentType::VIDEO,
        'mp4' => DocumentType::VIDEO,
        'flv' => DocumentType::VIDEO,
        'avi' => DocumentType::VIDEO,
        'mov' => DocumentType::VIDEO,
        'wmv' => DocumentType::VIDEO,
        'mp3' => DocumentType::AUDIO,
        'wav' => DocumentType::AUDIO,
        'ogg' => DocumentType::AUDIO,
        'ppt' => DocumentType::PRESENTATION,
        'pptx' => DocumentType::PRESENTATION,
        'odp' => DocumentType::PRESENTATION
    );

    static function extension2type($ext)
    {
        if (array_key_exists($ext, self::$ext2type))
            return self::$ext2type[$ext];
        else
            return self::OTHER;
    }

}


BitrixORM::registerMapClass(new DocumentMap(), Document::className());