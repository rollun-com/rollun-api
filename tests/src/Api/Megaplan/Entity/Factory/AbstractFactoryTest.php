<?php

namespace rollun\test\api\Api\Megaplan\Entity\Factory;

use Mockery;
use rollun\api\Api\Megaplan\Entity\Deal;
use rollun\api\Api\Megaplan\Entity\Factory\AbstractFactory;
use rollun\test\api\Api\Megaplan\Entity\ContainerMockTrait;

class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ContainerMockTrait;

    public function test_factory_canCreate_correctConfig_shouldReturnBoolean()
    {
        $factory = new AbstractFactory();
        $this->assertTrue(
            $factory->canCreate($this->getContainerMock(), 'deal_service')
        );
        return $factory;
    }

    /**
     * @depends test_factory_canCreate_correctConfig_shouldReturnBoolean
     */
    public function test_factory_invoke_correctConfig_shouldReturnEntityObject($factory)
    {
        $instance = $factory($this->getContainerMock(), 'deal_service');
        $this->assertInstanceOf(
            Deal::class, $instance
        );
    }
}