<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 14:13
 */

namespace rollun\test\api\Api\Google\Gmail\MessagesList;

use rollun\api\Api\Google\GoogleClientAbstract;
use rollun\api\Api\Google\Gmail\MessagesList\CachedObject;
use Zend\Cache\Storage\Adapter\Filesystem;

class CachedObjectTest extends \PHPUnit\Framework\TestCase
{

    public function test_Construct()
    {
        $cache = new CachedObject('Test_Cache');
        $this->assertEquals(
                Filesystem::class, get_class($cache->getMessagesListCacheAdapter())
        );
    }

    public function test_DateGetSet_Null()
    {
        $cache = new CachedObject('Test_Cache');
        $cache->setDateOfCache();
        $this->assertTrue(
                is_numeric($cache->getDateOfCache())
        );
    }

    public function test_DateGetSet_Data()
    {
        $cache = new CachedObject('Test_Cache');
        $cache->setDateOfCache(95525156);
        $this->assertEquals(
                95525156, $cache->getDateOfCache()
        );
    }

    public function test_DataGetSet_Data()
    {
        $cache = new CachedObject('Test_Cache');
        $messagesListData = [
            [
                'collection_key' => 'labelIds',
                'historyId' => 456,
                'id' => '1598594b3c840513',
                'internalDate' => null,
                'labelIds' => null,
            ],
            [
                'collection_key' => 'labelIds2',
                'historyId' => 4562,
                'id' => '1598594b3c8405132',
                'internalDate' => 2,
                'labelIds' => 2,
            ]
        ];
        $cache->setMessagesListData($messagesListData);
        $this->assertEquals(
                $messagesListData, $cache->getMessagesListData()
        );
    }

    public function test_removeFromCache()
    {
        $cache = new CachedObject('Test_Cache');
        $messagesListData = [
            [
                'collection_key' => 'labelIds',
                'historyId' => 456,
                'id' => '1598594b3c840513',
                'internalDate' => null,
                'labelIds' => null,
            ]
        ];
        $cache->setMessagesListData($messagesListData);
        $cache->setDateOfCache(95525156);
        $cache->saveToCache();
        $cache->removeFromCache();
        $cache->loadFromCache();
        $this->assertNull(
                $cache->getMessagesListData()
        );
    }

    public function test_LoadSave()
    {
        $cache = new CachedObject('Test_Cache');
        $messagesListData = [
            [
                'collection_key' => 'labelIds',
                'historyId' => 456,
                'id' => '1598594b3c840513',
                'internalDate' => null,
                'labelIds' => null,
            ]
        ];
        $cache->setMessagesListData($messagesListData);
        $cache->setDateOfCache(95525156);
        $cache->saveToCache();
        $cache->clearCachedObject();
        $cache->loadFromCache();
        $this->assertEquals(
                $messagesListData, $cache->getMessagesListData()
        );
    }

}
