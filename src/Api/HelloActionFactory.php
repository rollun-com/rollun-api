<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 12:27
 */

namespace rollun\api\Api;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Stratigility\MiddlewareInterface;

class HelloActionFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($container->has(TemplateRendererInterface::class)) {
            return new HelloAction($container->get(TemplateRendererInterface::class), $container->get('gmailGoogleClient'));
        }
        //throw new \Exception(TemplateRendererInterface::class . " not fount in container");
    }

}
