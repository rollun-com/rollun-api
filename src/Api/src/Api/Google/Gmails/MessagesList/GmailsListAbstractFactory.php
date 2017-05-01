<?php

namespace rollun\api\Api\Google\Gmails\MessagesList;

use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\api\Api\Google\Client as ApiGoogleClient;
use rollun\api\ApiException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use rollun\api\Api\Google\Gmails\MessagesList\GmailsFilteredList;
use rollun\logger\Exception\LoggedException;

/**
 *
 * return[
 *     'GMAILS_LIST' => [
 *         "emails_from_rm_test" => [
 *             GmailsListAbstractFactory::KEY_CLASS => GmailsFilteredList::class, //optionaly
 *             'GOOGLE_API_CLIENT' => 'gmailGoogleClient', //name of service for Api\Google\Client\Factory\AbstractFactory see below
 *             'OPT_PARAM' => [
 *                 //'q' => 'filename:(jpg OR png OR gif)', //'!in:chats' https://support.google.com/mail/answer/7190?hl=en&ref_topic=3394914
 *                 'includeSpamTrash' => true,
 *             //'labelIds' => "UNREAD", // "SENT", "INBOX"]
 *             ],
 *         ],
 *     ],
 *     'GOOGLE_API_CLIENTS' => [
 *         'gmailGoogleClient' => [
 *             'class' => 'rollun\api\Api\Google\Client\Cli',
 *             'SCOPES' => [
 *                 'https://www.googleapis.com/auth/gmail.readonly',
 *             ],
 *             'CONFIG' => [
 *                 'login_hint' => 'test.rocky.gift@gmail.com',
 *                 'application_name' => 'Gmail Parser',
 *                 'approval_prompt' => 'auto',
 *             ],
 *         ],
 *     ],
 *     'dependencies' => [
 *         'abstract_factories' => [
 *             GmailsListAbstractFactory::class
 *         ]
 *     ]
 * ];
 *
 */
class GmailsListAbstractFactory implements AbstractFactoryInterface
{

    const DEFAULT_CLASS = GmailsFilteredList::class;
    const KEY = 'GMAILS_LIST';
    const KEY_CLASS = 'class';
    const KEY_GOOGLE_API_CLIENT = 'GOOGLE_API_CLIENT';
    const KEY_OPT_PARAM = 'OPT_PARAM';

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $smConfig = $container->get('config');

        if (isset($smConfig[self::KEY][$requestedName])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create and return an instance of the GoogleClient.
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  array $options
     * @return AuthcodeClientAbstract
     * @throws ApiException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //Get config
        $smConfig = $container->get('config');
        $listSmConfig = $smConfig[self::KEY][$requestedName];
        //Get class of Google Client - MessagesList as default
        $requestedClassName = $this->getClass($listSmConfig, $requestedName);
        $listName = $requestedName;
        if (!isset($listSmConfig[static::KEY_GOOGLE_API_CLIENT]) || !$container->has($listSmConfig[static::KEY_GOOGLE_API_CLIENT])) {
            throw new LoggedException('Ther is not GOOGLE_API_CLIENT for MessagesList: ' . $listName);
        }
        $googleApiClient = $container->get($listSmConfig[static::KEY_GOOGLE_API_CLIENT]);

        $optParams = isset($listSmConfig[static::KEY_OPT_PARAM]) ? $listSmConfig[static::KEY_OPT_PARAM] : [];

        /* @var $messagesList MessagesList */
        $messagesList = new $requestedClassName($googleApiClient, $optParams);

        return $messagesList;
    }

    /**
     * Get class of Google Client - ApiGoogleClient as default
     *
     * @param array $smConfig $smConfig = $container->get('config');
     * @param string $requestedName
     * @return string
     * @throws ApiException
     */
    protected function getClass($listSmConfig, $requestedName)
    {
        $requestedClassName = isset($listSmConfig[AbstractFactoryAbstract::KEY_CLASS]) ?
                $listSmConfig[AbstractFactoryAbstract::KEY_CLASS] :
                static::DEFAULT_CLASS;
        if (!is_a($requestedClassName, static::DEFAULT_CLASS, true)) {
            throw new LoggedException("Class $requestedClassName is not instance of " . static::DEFAULT_CLASS);
        }
        return $requestedClassName;
    }

}
