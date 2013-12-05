<?php
/**
 * User: VOVA
 * Date: 19.04.13
 * Time: 12:58
 */

namespace ru\teachbase;

require(__DIR__.'/config/hint.config.php');
require_once(__DIR__.'/../base/iblock.orm.php');

class HintMap extends IBlockORMMap{

    public $iblock_id = _HintIblockId;

    public $props = array(
        array('bname' => 'CODE', 'name' => 'code', 'type' => BitrixORMDataTypes::INT),
        array('bname' => 'VIDEO', 'name' => 'video', 'type' => BitrixORMDataTypes::STRING),
        array('bname' => 'URL_MATCHER', 'name' => 'url_matcher', 'type' => BitrixORMDataTypes::STRING)
    );

    function __construct(){

        $this->fields[] = array('bname' => 'PREVIEW_PICTURE', 'name' => 'preview', 'type' => BitrixORMDataTypes::INT);

        parent::__construct();

    }

}