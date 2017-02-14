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
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Composer\IO\ConsoleIO;

class ConsoleIoFactory implements FactoryInterface
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
        if ($container->has(ConsoleIO::class)) {
            return $container->get(ConsoleIO::class);
        }
        return $this->createConsoleIO();
    }

    public function createConsoleIO()
    {
        /** init composer IO  */
        $consoleInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        $helperSet = new HelperSet([
            'question' => new QuestionHelper(),
            'formatter' => new FormatterHelper(),
            'descriptor' => new DescriptorHelper(),
            'process' => new ProcessHelper(),
            'debugFormatter' => new DebugFormatterHelper(),
        ]);
        $consoleIo = new ConsoleIO($consoleInput, $consoleOutput, $helperSet);
        return $consoleIo;
    }

}
