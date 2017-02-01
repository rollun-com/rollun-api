<?php

namespace rollun\api\Api\Google;

use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use Interop\Container\ContainerInterface;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\api\Api\Google\Cli as GoogleClient;
use rollun\api\ApiException;

/**
 *
 * return[
 *     'GOOGLE_API_CLIENTS' =>[
 *         AbstractFactoryAbstract::KEY_CLASS => GoogleClient::class, //optionaly
 *         'SCOPES' => [
 *             Google_Service_Gmail::GMAIL_READONLY,
 *             ...
 *         ],
 *         'CONFIG' =>[
 *             "client_id"=>"788567867260-jjbpfpjkj.jkjkk.jkjkkun38flks.apps.googleusercontent.com",
 *             "project_id"=>"notional-portal-145675673",
 *             "auth_uri"=>"https://accounts.google.com/o/oauth2/auth",
 *             "token_uri"=>"https://accounts.google.com/o/oauth2/token",
 *             "auth_provider_x509_cert_url"=?"https://www.googleapis.com/oauth2/v1/certs",
 *             "client_secret"=>"zvfgnfgeqCh4OKnmghiki8omD6H3wj",
 *             "redirect_uris"=>["urn:ietf:wg:oauth:2.0:oob","http://localhost"],
 *
 *             "login_hint"=>"user@gmail.com",
 *             "access_type" =>"offline",
 *         ]
 * ]
 *
 */
class ClientAbstractFactory extends AbstractFactoryAbstract
{

    const GOOGLE_API_CLIENTS_SERVICES_KEY = 'GOOGLE_API_CLIENTS';
    const SCOPES = 'SCOPES';
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
     * @return DataStoresInterface
     * @throws DataStoreException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //Get config
        $smConfig = $container->get('config');
        $googleClientSmConfig = $smConfig[self::GOOGLE_API_CLIENTS_SERVICES_KEY][$requestedName];
        //Get class of Google Client - GoogleClient as default
        $requestedClassName = $googleClientSmConfig[static::KEY_CLASS] ? : GoogleClient::class;
        if (!is_a($requestedClassName, GoogleClient::class, true)) {
            throw new ApiException('Class $requestedClassName is not instance of ' . GoogleClient::class);
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
        /* @var $client GoogleClient */
        $client = new $className($clientConfig, $requestedName);

        //Get and set SCOPES
        $scopes = $googleClientSmConfig[static::SCOPES]? : [];
        $client->setScopes($scopes);

        return $client;
    }

}
