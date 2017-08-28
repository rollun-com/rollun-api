<?php

namespace rollun\api\Api\Megaplan\Entity\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use rollun\actionrender\Factory\ActionRenderAbstractFactory;
use rollun\api\Api\Megaplan\Serializer\MegaplanOptionsInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use rollun\api\Api\Megaplan\Entity\EntityAbstract;

class AbstractFactory implements AbstractFactoryInterface
{
    const KEY = 'megaplan_entities';
    const ENTITY_KEY = 'entity';
    const DATASTORE_KEY = 'dataStore';
    const SERIALIZER_KEY = 'serializer';
    const SERIALIZER_OPTIONS_KEY = 'serializerOptions';

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        // There is no requested service section in the config
        if (!isset($config[static::KEY][$requestedName])) {
            return false;
        }
        // DataStore
        if (!isset($config[static::KEY][$requestedName][static::DATASTORE_KEY])) {
            return false;
        }
        // Specified kind of entity doesn't exist
        if (!isset($config[static::KEY][$requestedName][static::ENTITY_KEY])) {
            return false;
        }
        // Serializer
        if (!isset($config[static::KEY][$requestedName][static::SERIALIZER_KEY])) {
            return false;
        }
        // SerializerOptions
        if (!isset($config[static::KEY][$requestedName][static::SERIALIZER_OPTIONS_KEY])) {
            return false;
        }
        return is_a($this->getClassName($config, $requestedName), EntityAbstract::class, true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $serviceConfig = $config[static::KEY];

        $megaplanClient = $container->get('megaplan');
        $dataStore = $container->get($serviceConfig[$requestedName][static::DATASTORE_KEY]);

        $serializer = $container->get($serviceConfig[$requestedName][static::SERIALIZER_KEY]);
        $serializerOptions = $container->get($serviceConfig[$requestedName][static::SERIALIZER_OPTIONS_KEY]);
        if (!$serializerOptions instanceof MegaplanOptionsInterface) {
            throw new ServiceNotCreatedException("Class \"SerializerOptions\" has to implement MegaplanOptionsInterface.");
        }
        $serializer->setOptions($serializerOptions);

        $className = $this->getClassName($config, $requestedName);
        $instance = new $className($megaplanClient, $dataStore, $serializer);
        return $instance;
    }

    protected function getClassName($config, $requestedName)
    {
        $namespace = preg_replace("/Factory$/", "", __NAMESPACE__);
        $className = $namespace . ucfirst($config[static::KEY][$requestedName][static::ENTITY_KEY]);
        return $className;
    }
}