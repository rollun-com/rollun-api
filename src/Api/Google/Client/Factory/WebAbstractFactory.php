<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10.02.17
 * Time: 12:06
 */

namespace rollun\api\Api\Google\Client\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use rollun\api\Api\Google\Client\Web;
use rollun\api\ApiException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\Session\Service\SessionManagerFactory;
use Zend\Session\SessionManager;

class WebAbstractFactory extends AbstractFactory
{
    const DEFAULT_CLASS = Web::class;
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        //Get config
        $smConfig = $container->get('config');
        $googleClientSmConfig = $smConfig[self::KEY_GOOGLE_API_CLIENTS][$requestedName];
        //Get class of Google Client - ApiGoogleClient as default
        $requestedClassName = $this->getClass($smConfig, $requestedName);
        //Get config from Service Manager config
        $clientConfigFromSmConfig = $googleClientSmConfig[static::KEY_CONFIG] ?: [];
        $arrayDiff = array_diff(array_keys($clientConfigFromSmConfig), static::GOOGLE_CLIENT_CONFIG_KEYS);
        if (count($arrayDiff) != 0) {
            throw new ApiException('Wrong key in Google Client config: ' . array_shift($arrayDiff));
        }

        if (!$container->has(SessionManager::class)) {
            $sessionManagerFactory = new SessionManagerFactory();
            $sessionManager = $sessionManagerFactory($container, SessionManager::class);
        } else {
            $sessionManager = $container->get(SessionManager::class);
        }
        $sessionContainer = new Container('SessionContainer', $sessionManager);


        /* @var $client ApiGoogleClient */
        $client = new $requestedClassName($clientConfigFromSmConfig, $requestedName, $sessionContainer);
        //Get and set SCOPES
        $scopes = $googleClientSmConfig[static::KEY_SCOPES] ?: ['openid'];
        $client->setScopes($scopes);

        return $client;

    }
}
