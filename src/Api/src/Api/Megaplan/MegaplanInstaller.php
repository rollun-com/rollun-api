<?php

namespace rollun\api\Api\Megaplan;

use rollun\installer\Install\InstallerAbstract;
use rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory;

class MegaplanInstaller extends InstallerAbstract
{
    protected $message = 'The constant "APP_ENV" is not defined or its value is not "dev".
        You can\'t do anything in a non-DEV mode.';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function install()
    {
        if (constant('APP_ENV') !== 'dev') {
            $this->consoleIO->write($this->message);
            return [];
        } else {
            $config = [
                'megaplan' => [
                    'api_url' => '',
                    'login' => '',
                    'password' => '',
                ],
                'megaplan_entities' => [
                    'deals' => [
                        'filterField' => [
                            DealsFactory::FILTER_FIELD_PROGRAM_KEY => null,
                        ],
                        'requestedFields' => [],
                        'extraFields' => [],
                    ],
                ],
            ];

            $config['megaplan']['api_url'] = $this->consoleIO->ask(
                "Type the <info>URL</info> to access to the Megaplan's API (without protocol, domain name only) [{$config['megaplan']['api_url']}]:",
                $config['megaplan']['api_url']
            );

            $config['megaplan']['login'] = $this->consoleIO->ask(
                "Type the <info>login</info> to access to the Megaplan's API [{$config['megaplan']['login']}]:",
                $config['megaplan']['login']
            );

            $config['megaplan']['password'] = $this->consoleIO->ask(
                "Type the <info>password</info> to access to the Megaplan's API [{$config['megaplan']['password']}]:",
                $config['megaplan']['password']
            );

            $programId = $config['megaplan_entities']['deals']['filterField'][DealsFactory::FILTER_FIELD_PROGRAM_KEY];
            $config['megaplan_entities']['deals']['filterField'][DealsFactory::FILTER_FIELD_PROGRAM_KEY] =
                $this->consoleIO->ask(
                    "Set the <info>id of a scheme</info> in the Megaplan to use [{$programId}]:",
                    $programId
                );

            $this->consoleIO->write("You can also specify additional values of <info>FilterFields, RequestedFields and ExtraFields</info> in the relevant sections.");
            return $config;
        }
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function uninstall()
    {
        if (constant('APP_ENV') !== 'dev') {
            $this->consoleIO->write($this->message);
        } else {
            // Does nothing
        }
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Предоставляет доступ к API Магаплана.";
                break;
            case "en":
                $description = "Allows to use the Megaplan API.";
                break;
            default:
                $description = "Description does not exist.";
        }
        return $description;
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function isInstall()
    {
        $config = $this->container->get('config');
        return (isset($config['megaplan']));
    }
}