<?php

namespace rollun\api\Api\Google\Client\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\api\Api\Google\Client as ApiGoogleClient;
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
 *                  //Must be set for Cli:
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
class AbstractFactory implements AbstractFactoryInterface
{

    const KEY_GOOGLE_API_CLIENTS = 'GOOGLE_API_CLIENTS';
    const KEY_SCOPES = 'SCOPES';
    const KEY_CONFIG = 'CONFIG';
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

        if (isset($smConfig[self::KEY_GOOGLE_API_CLIENTS][$requestedName])) {
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
        $googleClientSmConfig = $smConfig[self::KEY_GOOGLE_API_CLIENTS][$requestedName];
        //Get class of Google Client - ApiGoogleClient as default
        $requestedClassName = $this->getClass($smConfig, $requestedName);
        //Get config from Service Manager config
        $clientConfigFromSmConfig = $googleClientSmConfig[static::KEY_CONFIG] ? : [];
        $arrayDiff = array_diff(array_keys($clientConfigFromSmConfig), static::GOOGLE_CLIENT_CONFIG_KEYS);
        if (count($arrayDiff) != 0) {
            throw new ApiException('Wrong key in Google Client config: ' . array_shift($arrayDiff));
        }

        /* @var $client ApiGoogleClient */
        $client = new $requestedClassName($clientConfigFromSmConfig, $requestedName);

        //Get and set SCOPES
        $scopes = $googleClientSmConfig[static::KEY_SCOPES]? : [];
        $client->setScopes($scopes);

        return $client;
    }

    /**
     * Get class of Google Client - ApiGoogleClient as default
     *
     * @param array $smConfig $smConfig = $container->get('config');
     * @param string $requestedName
     * @return string
     * @throws ApiException
     */
    protected function getClass($smConfig, $requestedName)
    {
        $googleClientSmConfig = $smConfig[self::KEY_GOOGLE_API_CLIENTS][$requestedName];
        $requestedClassName = isset($googleClientSmConfig[AbstractFactoryAbstract::KEY_CLASS]) ?
                $googleClientSmConfig[AbstractFactoryAbstract::KEY_CLASS] :
                ApiGoogleClient::class;
        if (!is_a($requestedClassName, ApiGoogleClient::class, true)) {
            throw new ApiException("Class $requestedClassName is not instance of " . ApiGoogleClient::class);
        }
        return $requestedClassName;
    }

    /**
     *
     *
     * @param ContainerInterface $container
     * @return array ["ApiClient" => ApiGoogleClient::class, "NextClient" => CliApiGoogleClient::class, ..." ]
     */
    public function getAllClasses(ContainerInterface $container)
    {
        $smConfig = $container->get('config');
        if (isset($smConfig[self::KEY_GOOGLE_API_CLIENTS])) {
            $googleCliClientsSmConfig = $smConfig[self::KEY_GOOGLE_API_CLIENTS];
            foreach ($googleCliClientsSmConfig as $clientName => $value) {
                $allClasses[$clientName] = $this->getClass($smConfig, $clientName);
            }
            return $allClasses;
        } else {
            return [];
        }
    }

}
