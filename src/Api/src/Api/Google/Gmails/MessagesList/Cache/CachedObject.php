<?php

namespace rollun\api\Api\Google\Gmails\MessagesList\Cache;

use rollun\utils\Time\UtcTime;

class CachedObject
{

    const DATE_KEY = 'DATE_KEY';
    const LIST_KEY = 'LIST_KEY';

    /**
     *
     * @var array
     */
    protected $data;

    public function __construct($list, $utcDateInSec = null)
    {
        $this->setList($list);
        $this->setDate($utcDateInSec);
    }

    public function getList()
    {
        return $this->data[self::LIST_KEY];
    }

    public function setList($list)
    {
        $this->data[self::LIST_KEY] = $list;
    }

    public function setDate($utcDateInSec = null)
    {
        $utcDateInSec = $utcDateInSec ?: UtcTime::getUtcTimestamp();
        $this->data[self::DATE_KEY] = $utcDateInSec;
    }

    public function getDate()
    {
        return $this->data[self::DATE_KEY];
    }

}
