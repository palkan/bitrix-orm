<?php
/**
 * User: palkan
 * Date: 02.09.13
 * Time: 10:42
 */

namespace ru\teachbase;

require(dirname(__FILE__) . '/../maps/recording.map.php');
require_once(dirname(__FILE__) . '/../common/conversion.manager.php');

class Recording extends Assignable
{

    protected $_partner_id;

    protected $_size;

    protected $_duration;

    protected $_meeting_id;

    protected $_data;

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
     *
     * Recording duration in milliseconds
     *
     * @param null $val
     * @return mixed
     */

    public function duration($val = null)
    {
        return $this->_commit(__FUNCTION__, $val);
    }

    /**
     * @param null $val
     * @return mixed
     */

    public function meeting_id($val = null)
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


    function __construct()
    {
        parent::__construct();
    }


    /**
     *
     * Run converter task to create hls from meeting recording flv video.
     *
     * @param array $listeners  Additional URLs to invoke on task complete.
     * @return bool
     */

    public function to_hls($listeners = array())
    {

        if ($this->_data && $this->_data->task_key) {
            Logger::log("Task in progress: " . $this->_data->task_key . ", id: " . $this->_id, "error");
            return false;
        }

        if (!$this->_data || !property_exists($this->_data, "stream_to_convert")) {
            Logger::log("No stream to convert in data, id: " . $this->_id, "error");
            return false;
        }

        $key = $this->convert('flv_to_hls', $this->_data->url . $this->_data->stream_to_convert->name . ".flv", array("target_dir" => $this->_data->url . $this->_data->stream_to_convert->name . "/", "stream_type" => $this->_data->stream_to_convert->type), $listeners);

        if ($key) {
            $data = $this->data() ? $this->data() : new \stdClass();
            $data->converting = true;
            $data->task_key = $key;
            $this->data($data);
            return true;
        } else
            return false;

    }

    /**
     * @param $type
     * @param $path
     * @param $params
     * @param $listeners
     * @return bool
     */

    private function convert($type, $path, $params, $listeners)
    {

        return ConversionManager::fyler_convert($this->_id, $type, self::className(), $path, $params, $listeners);

    }

    public function conversion_complete($result)
    {

        if ($result->status == "ok" && $result->aws === false) {
            Logger::log(__CLASS__ . ":" . __LINE__ . " We can not process not aws files now: " . implode(",", $result->path) . ", id: " . $this->_id, "error");
            return;
        }

        $data = $this->data() ? $this->data() : new \stdClass();
        if ($data->task_key) {
            unset($data->task_key);
        }
        $this->data($data);

        if ($result->status == "failed") {
            Logger::log(__CLASS__ . ":" . __LINE__ . " task failed; task: " . json_encode($result), "error");
            return;
        }

        $handler = $result->type;

        $this->$handler($result);

        $this->save();

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

        unset($data->stream_to_convert);

        if (count($data->streams)) {

            $data->stream_to_convert = current($data->streams);

            $this->data($data);

            if ($this->to_hls() !== false) {
                $data = $this->data();
                array_shift($data->streams);
            }else{
                Logger::log("Failed to run new task for converting another hls stream, id: " . $this->_id, "error");
            }
        } else
            $data->converting = false;

        $this->data($data);
    }


    public function jsonData()
    {

        $data = parent::jsonData();

        return $data;
    }
}

BitrixORM::registerMapClass(new RecordingMap(), Recording::className());