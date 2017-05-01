<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 14:13
 */

namespace rollun\test\api\Api\Google\Gmails\MessagesList\Cache;

use rollun\api\Api\Google\Gmails\MessagesList\Cache\CachedList;
use rollun\api\Api\Google\Gmails\MessagesList\Cache\CachedObject;
use rollun\utils\Time\UtcTime;
use Zend\Cache\Storage\Adapter\Filesystem;
use rollun\logger\Exception\LoggedException;

class CachedListTest extends \PHPUnit\Framework\TestCase
{

    public function test_Construct()
    {
        $cache = new CachedList();
        $this->assertEquals(
                Filesystem::class, get_class($cache->getCacheAdapter())
        );
    }

    public function test_GetSet()
    {
        $cache = new CachedList();
        $cache->setList('listName', ['dataKey1' => 1, 'dataKey2' => 2]);
        $cache = new CachedList();
        $list = $cache->getList('listName')->getList();
        $this->assertEquals(
                ['dataKey1' => 1, 'dataKey2' => 2], $list
        );
    }

    public function test_GetSet_Error()
    {
        $cache = new CachedList();
        $this->expectException(LoggedException::class);
        $list = $cache->getList('list_Name_Absent')->getList();
    }

}
