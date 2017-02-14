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
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;
use Zend\Session\Service\SessionManagerFactory;
use Zend\Session\SessionManager;

class WebFactory implements FactoryInterface
{

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

        $config = $container->has('config') ? $container->get('config') : [];
        $webConfig = isset($config[Web::KEY_WEB_CLIENT]) ? $config[Web::KEY_WEB_CLIENT] : [];
        if (!$container->has(SessionManager::class)) {
            $sessionManagerFactory = new SessionManagerFactory();
            $sessionManager = $sessionManagerFactory($container, SessionManager::class);
        } else {
            $sessionManager = $container->get(SessionManager::class);
        }
        $sessionContainer = new Container('SessionContainer', $sessionManager);

        $webClient = new Web($webConfig, $sessionContainer);
        $webClient->addScope('openid');
        return $webClient;
    }
}
