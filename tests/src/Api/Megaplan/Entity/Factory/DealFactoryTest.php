<?php

namespace rollun\test\api\Api\Megaplan\Entity\Factory;

use rollun\api\Api\Megaplan\Entity\Deal\Deal;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\DealFactory;
use rollun\test\api\Api\Megaplan\Entity\ContainerMockTrait;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class DealFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ContainerMockTrait;

    public function test_dealFactory_correctConfig_shouldReturnDealObject()
    {
        $factory = new DealFactory();
        $instance = $factory($this->getContainerMock(), '');
        $this->assertInstanceOf(
            Deal::class, $instance
        );
        return $factory;
    }

    /**
     * @depends test_dealFactory_correctConfig_shouldReturnDealObject
     * @param $factory
     */
    public function test_dealsFactory_dealsSectionAbsents_shouldThrownException($factory)
    {
        unset($this->config['megaplan_entities']['deals']);
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("The configuration for deals entity is not found");
        $factory($this->getContainerMock(), '');
    }
}