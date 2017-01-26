<?php

namespace rollun\api\Api\Google;

use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\api\Api\Google\Client as GoogleClient;
use rollun\api\ApiException;

class ClientAbstractFactory extends AbstractFactoryAbstract
{

    const KEY_GOOGLE_CLIENT = 'KEY_GOOGLE_CLIENT';
    const SECRET_SERVICE_KEY = 'SECRET_KEY';
    const SCOPES = 'SCOPES';
    const APP_NAME_KEY = 'APP_NAME_KEY';

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');

        if (!isset($config[self::KEY_GOOGLE_CLIENT][$requestedName])) {
            return false;
        } else {
            $clientConfig = $config[self::KEY_GOOGLE_CLIENT][$requestedName];
        }
        $requestedClassName = $clientConfig[static::KEY_CLASS] ? : GoogleClient::class;
        if (!is_a($requestedClassName, GoogleClient::class, true)) {
            throw new ApiException('Class $requestedClassName is not instance of ' . GoogleClient::class);
        }
        return true;
    }

    /**
     * Create and return an instance of the GoogleClient.
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  array $options
     * @return DataStoresInterface
     * @throws DataStoreException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (isset($config[self::KEY_GOOGLE_CLIENT][$requestedName])) {
            $requestedClassName = $config[self::KEY_GOOGLE_CLIENT][$requestedName][static::KEY_CLASS];
        } else {
            $requestedClassName = GoogleClient::class;
        }
        return new $requestedClassName();
    }

}
