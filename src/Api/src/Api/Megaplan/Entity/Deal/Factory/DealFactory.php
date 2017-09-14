<?php

namespace rollun\api\Api\Megaplan\Entity\Deal\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\Api\Megaplan\Entity\Deal\Deal;
use rollun\api\Api\Megaplan\Entity\Factory\AbstractFactory;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class DealFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::__invoke($container, $requestedName, $options);
        $config = $container->get('config');
        if (!isset($config[static::KEY][DealsFactory::DEALS_KEY])) {
            throw new ServiceNotFoundException("The configuration for deals entity is not found");
        }
        $serviceConfig = $config[static::KEY][DealsFactory::DEALS_KEY];

        $requestedFields = isset($serviceConfig[DealsFactory::REQUESTED_FIELDS_KEY]) ? $serviceConfig[DealsFactory::REQUESTED_FIELDS_KEY] : [];
        $extraFields = isset($serviceConfig[DealsFactory::EXTRA_FIELDS_KEY]) ? $serviceConfig[DealsFactory::EXTRA_FIELDS_KEY] : [];

        $instance = new Deal($requestedFields, $extraFields);
        return $instance;
    }
}