<?php

namespace rollun\api\Api\Google\Gmail\MessagesList;

use rollun\dic\InsideConstruct;
use Zend\Cache\Storage\Adapter\Filesystem;
use rollun\utils\Time\UtcTime;

class CachedObject
{

    const DATE_KEY = 'Cache date';
    const SERIALIZED_DATA_KEY = 'Serialized data';

    /**
     *
     * @var type string like 'from example@site.com'
     */
    public $messagesListName;

    /**
     *
     * @var int  UTC time
     */
    protected $dateOfCache;

    /**
     *
     * @var array
     */
    protected $messagesListData;

    /**
     *
     * @var Filesystem
     */
    protected $messagesListCacheAdapter;

    public function __construct($messagesListName, $messagesListCacheAdapter = null)
    {
        InsideConstruct::init();
    }

    public function setMessagesListData($messagesListData)
    {
        $this->messagesListData = $messagesListData;
    }

    public function getMessagesListData()
    {
        return $this->messagesListData;
    }

    public function setDateOfCache($utcDateInSec = null)
    {
        $utcDateInSec = $utcDateInSec ?: UtcTime::getUtcTimestamp();
        $this->dateOfCache = $utcDateInSec;
    }

    public function getDateOfCache()
    {
        return $this->dateOfCache;
    }

    public function clearCachedObject()
    {
        $this->setMessagesListData(null);
        $this->setDateOfCache(null);
    }

    public function removeFromCache()
    {
        $key = $this->messagesListName;
        return $this->messagesListCacheAdapter->removeItem($key);
    }

    public function saveToCache()
    {
        $messagesListData = $this->getMessagesListData();
        $dateOfCache = $this->getDateOfCache();
        $cachedItem = [
            self::SERIALIZED_DATA_KEY => $messagesListData,
            self::DATE_KEY => $dateOfCache
        ];
        $serializedCachedItem = serialize($cachedItem);

        $key = $this->messagesListName;
        return $this->messagesListCacheAdapter->setItem($key, $serializedCachedItem);
    }

    public function loadFromCache()
    {
        $key = $this->messagesListName;
        $this->clearCachedObject();
        $serializedCachedItem = $this->messagesListCacheAdapter->getItem($key);

        if (isset($serializedCachedItem)) {
            $cachedItem = unserialize($serializedCachedItem);

            $messagesListData = $cachedItem[self::SERIALIZED_DATA_KEY];
            $this->setMessagesListData($messagesListData);

            $dateOfCache = $cachedItem[self::DATE_KEY];
            $this->setDateOfCache($dateOfCache);

            return true;
        }
        return false;
    }

    public function getMessagesListCacheAdapter()
    {
        return $this->messagesListCacheAdapter;
    }

}
