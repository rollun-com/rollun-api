<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 14:13
 */

namespace rollun\test\api\Api\Google\Gmail\MessagesList;

use rollun\api\Api\Google\GoogleClientAbstract;
use rollun\api\Api\Google\Client;
use rollun\api\Api\Google\Gmail\MessagesList\CachedObject;
use Zend\Cache\Storage\Adapter\Filesystem;
use rollun\api\Api\Google\Gmail\MessagesList;

class MessagesListTest extends \PHPUnit\Framework\TestCase
{

    public function test_Construct()
    {
        $client = new Client([]);
        $messagesList = new MessagesList('Test_MessagesList', $client);
        $this->assertEquals(
                MessagesList::class, get_class($messagesList)
        );
    }

    public function test_Construct_Wrong_Name()
    {
        $client = new Client([]);
        $this->expectException(\Exception::class);
        $messagesList = new MessagesList('Test MessagesList', $client);
    }

}
