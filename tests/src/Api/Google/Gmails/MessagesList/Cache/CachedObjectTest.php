<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 14:13
 */

namespace rollun\test\api\Api\Google\Gmails\MessagesList\Cache;

use rollun\api\Api\Google\Gmails\MessagesList\Cache\CachedObject;
use rollun\utils\Time\UtcTime;

class CachedObjectTest extends \PHPUnit\Framework\TestCase
{

    public function test_Construct()
    {
        $cachedObject = new CachedObject('Test Cache Data');
        $this->assertEquals(
                'Test Cache Data', $cachedObject->getList()
        );
        $this->assertTrue(
                $cachedObject->getDate() - UtcTime::getUtcTimestamp() < 5//sec
        );
    }

    public function test_Construct_WithDate()
    {
        $cachedObject = new CachedObject(['Test Cache Data1', 'Test Cache Data2'], 60 * 60 * 24 * 30 * 12 * 2000);
        $this->assertEquals(
                ['Test Cache Data1', 'Test Cache Data2'], $cachedObject->getList()
        );
        $this->assertEquals(
                60 * 60 * 24 * 30 * 12 * 2000, $cachedObject->getDate()
        );
    }

    public function test_Set()
    {
        $cachedObject = new CachedObject(['Test Cache Data1', 'Test Cache Data2'], 60 * 60 * 24 * 30 * 12 * 2000);
        $cachedObject->setList([1, 2, 3]);
        $cachedObject->setDate(123);
        $this->assertEquals(
                [1, 2, 3], $cachedObject->getList()
        );
        $this->assertEquals(
                123, $cachedObject->getDate()
        );
    }

}
