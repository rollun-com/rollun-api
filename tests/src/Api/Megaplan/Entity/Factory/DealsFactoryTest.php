<?php

namespace rollun\test\api\Api\Megaplan\Entity\Factory;

use rollun\test\api\Api\Megaplan\Entity\ContainerMockTrait;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory;
use rollun\api\Api\Megaplan\Entity\Deal\Deals;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class DealsFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ContainerMockTrait;

    public function test_dealsFactory_correctConfig_shouldReturnDealsObject()
    {
        $factory = new DealsFactory();
        $instance = $factory($this->getContainerMock(), '');
        $this->assertInstanceOf(
            Deals::class, $instance
        );
        return $factory;
    }

    /**
     * @depends test_dealsFactory_correctConfig_shouldReturnDealsObject
     * @param $factory
     */
    public function test_dealsFactory_dealsSectionAbsents_shouldThrownException($factory)
    {
        unset($this->config['megaplan_entities']['deals']);
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("The configuration for deals entity is not found");
        $factory($this->getContainerMock(), '');
    }

    /**
     * @depends test_dealsFactory_correctConfig_shouldReturnDealsObject
     * @param $factory
     */
    public function test_dealsFactory_dealListFieldsSectionAbsents_shouldThrownException($factory)
    {
        unset($this->config['megaplan_entities']['deals']['dealListFields']);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Requested parameter \"dealListFields\" is not found in the entity config");
        $factory($this->getContainerMock(), '');
    }

    /**
     * @depends test_dealsFactory_correctConfig_shouldReturnDealsObject
     * @param $factory
     */
    public function test_dealsFactory_filterFieldSectionAbsentsOrNotFilled_shouldThrownException($factory)
    {
        unset($this->config['megaplan_entities']['deals']['filterField']['Program']);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Deals entity has to receive necessary parameter \"Program\" in the \"filterField\" array");
        $factory($this->getContainerMock(), '');
        // The same result will be obtained if I remove whole the 'filterField' part.
    }
}