<?php

namespace rollun\test\api\Api\MegaplanTest\Entity\Factory;

use Mockery;
use rollun\api\Api\Megaplan\Entity\Factory\MegaplanClientFactory;
use Megaplan\SimpleClient\Client;
use rollun\test\api\Api\Megaplan\Entity\ContainerMockTrait;

class MegaplanClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ContainerMockTrait;

    public function test_invoke_correctConfig_shouldReturnMegaplanClientObject()
    {
        $factory = new MegaplanClientFactory();
        $instance = $factory($this->getContainerMock(), '', null);
        $this->assertInstanceOf(
            Client::class, $instance
        );
    }
}