<?php

namespace rollun\api\Api\Google\Client\Factory;

use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;
use rollun\api\ApiException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 *
 * return[
 *      'GOOGLE_API_CLIENTS' =>[
 *          "CliClient" =>[
 *              AbstractFactoryAbstract::KEY_CLASS => GoogleClient::class, //optionaly
 *              'SCOPES' => [ //Must be set:
 *                  Google_Service_Gmail::GMAIL_READONLY,
 *                  ...
 *              ],
 *              'CONFIG' =>[
 *                  //Must be set:
 *                  "login_hint"=>"user@gmail.com", //<<--imortant!!!  will be use as user's Email
 *                  //optionaly:
 *                  "application_name"=>"MyApp",
 *                  "approval_prompt" =>"",
 *                  ...
 *              ],
 *          ],
 *          "NextCliClient" =>[
 *      ]
 * ]
 *
 */
class CliAbstractFactory implements AbstractFactoryInterface
{

    const GOOGLE_API_CLIENTS_SERVICES_KEY = 'GOOGLE_API_CLIENTS';
    const SCOPES_KEY = 'SCOPES';
    const CONFIG_KEY = 'CONFIG';
    const GOOGLE_CLIENT_CONFIG_KEYS = [ 'application_name', 'base_path',
        'client_id', 'client_secret', 'redirect_uri', 'state', 'developer_key',
        'use_application_default_credentials', 'signing_key', 'signing_algorithm',
        'subject', 'hd', 'prompt', 'openid.realm', 'include_granted_scopes',
        'login_hint', 'request_visible_actions', 'access_type', 'approval_prompt',
        'retry', 'cache_config', 'token_callback',
    ];

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

        if (isset($smConfig[self::GOOGLE_API_CLIENTS_SERVICES_KEY][$requestedName])) {
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
        $googleClientSmConfig = $smConfig[self::GOOGLE_API_CLIENTS_SERVICES_KEY][$requestedName];
        //Get class of Google Client - ApiGoogleClientCli as default
        $requestedClassName = $googleClientSmConfig['class'] ? : ApiGoogleClientCli::class;
        if (!is_a($requestedClassName, ApiGoogleClientCli::class, true)) {
            throw new ApiException('Class $requestedClassName is not instance of ' . ApiGoogleClientCli::class);
        }
        //Get config from Service Manager config
        $clientConfigFromSmConfig = $googleClientSmConfig[static::CONFIG_KEY] ? : [];
        $clientConfig = [];
        foreach ($clientConfigFromSmConfig as $key => $value) {
            if (in_array($key, static::GOOGLE_CLIENT_CONFIG_KEYS)) {
                $clientConfig[$key] = $value;
            } else {
                throw new ApiException('Wrong key in Google Client config: ' . $key);
            }
        }
        /* @var $client GoogleCLIClient */
        $client = new $requestedClassName($clientConfig, $requestedName);

        //Get and set SCOPES
        $scopes = $googleClientSmConfig[static::SCOPES_KEY]? : [];
        $client->setScopes($scopes);

        return $client;
    }

}
