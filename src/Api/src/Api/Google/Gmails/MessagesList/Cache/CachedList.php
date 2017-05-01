<?php

namespace rollun\api\Api\Google\Gmails\MessagesList\Cache;

use rollun\dic\InsideConstruct;
use rollun\api\Api\Google\Gmails\MessagesList\Cache\CachedObject;
use Zend\Cache\StorageFactory;
use rollun\logger\Exception\LoggedException;
use rollun\installer\Command;

class CachedList
{

    /**
     *
     * @var Filesystem
     */
    protected $cacheAdapter;

    public function __construct($messagesListCacheAdapter = null)
    {
        InsideConstruct::init();
        if (is_null($this->cacheAdapter)) {
            $this->cacheAdapter = StorageFactory:: factory([
                        'adapter' => [
                            'name' => 'filesystem',
                            "namespace" => 'MessagesList',
                            'cache_dir' => Command::getDataDir() . 'cache',
                        ],
                        'plugins' => [
                            // Don't throw exceptions on cache errors
                            'exception_handler' => [
                                'throw_exceptions' => false
                            ],
                        ],
            ]);
        }
    }

    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
    }

    public function setList($listName, $list, $utcDateInSec = null)
    {
        static::checkName($listName);
        $cachedObject = new CachedObject($list, $utcDateInSec);
        $serializedCachedObject = serialize($cachedObject);
        $cacheAdapter = $this->getCacheAdapter();
        return $cacheAdapter->setItem($listName, $serializedCachedObject);
    }

    /**
     *
     * @param string $listName
     * @return CachedObject
     * @throws LoggedException
     */
    public function getList($listName)
    {
        static::checkName($listName);
        $success = null;
        $serializedCachedObject = $this->cacheAdapter->getItem($listName, $success);
        if (!$success) {
            throw new LoggedException('Cache error for key: ' . $listName);
        }
        $cachedObject = unserialize($serializedCachedObject);
        return $cachedObject;
    }

    public static function checkName($name)
    {
        if (empty($name)) {
            throw new LoggedException(
            "An empty $name isn't allowed"
            );
        }
        $pattern = '/^[a-z0-9_\+\-]*$/Di';
        if (!preg_match($pattern, $name)) {
            throw new LoggedException(
            "The key '{$name}' doesn't match against pattern '{$pattern}'"
            );
        }
    }

}
