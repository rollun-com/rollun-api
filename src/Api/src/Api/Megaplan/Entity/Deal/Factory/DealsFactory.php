<?php

namespace rollun\api\Api\Megaplan\Entity\Deal\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\Api\Megaplan\Entity\Deal\Deals;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use rollun\api\Api\Megaplan\Entity\Factory\AbstractFactory;
use rollun\api\Api\Megaplan\Exception\InvalidArgumentException;

class DealsFactory extends AbstractFactory
{
    const DEALS_KEY = 'deals';
    const DEAL_LIST_FIELDS_KEY = 'dealListFields';
    const FILTER_FIELD_KEY = 'filterField';
    const FILTER_FIELD_PROGRAM_KEY = 'Program';
    const REQUESTED_FIELDS_KEY = 'requestedFields';
    const EXTRA_FIELDS_KEY = 'extraFields';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::__invoke($container, $requestedName, $options);
        $config = $container->get('config');
        if (!isset($config[static::KEY][self::DEALS_KEY])) {
            throw new ServiceNotFoundException("The configuration for deals entity is not found");
        }
        $serviceConfig = $config[static::KEY][self::DEALS_KEY];
        if (!isset($serviceConfig[static::DEAL_LIST_FIELDS_KEY])) {
            throw new InvalidArgumentException("Requested parameter \""
                . static::DEAL_LIST_FIELDS_KEY . "\" is not found in the entity config");
        }
        if (!isset($serviceConfig[static::FILTER_FIELD_KEY][static::FILTER_FIELD_PROGRAM_KEY])) {
            throw new InvalidArgumentException("Deal entity has to receive necessary parameter \""
                . static::FILTER_FIELD_PROGRAM_KEY . "\" in the \"" . static::FILTER_FIELD_KEY . "\" array");
        }

        $dealListFields = $container->get($serviceConfig[static::DEAL_LIST_FIELDS_KEY]);
        $filterFields = isset($serviceConfig[static::FILTER_FIELD_KEY]) ? $serviceConfig[static::FILTER_FIELD_KEY] : [];
        $requestedFields = isset($serviceConfig[static::REQUESTED_FIELDS_KEY]) ? $serviceConfig[static::REQUESTED_FIELDS_KEY] : [];
        $extraFields = isset($serviceConfig[static::EXTRA_FIELDS_KEY]) ? $serviceConfig[static::EXTRA_FIELDS_KEY] : [];

        $instance = new Deals($dealListFields, $filterFields, $requestedFields, $extraFields);
        return $instance;
    }

}