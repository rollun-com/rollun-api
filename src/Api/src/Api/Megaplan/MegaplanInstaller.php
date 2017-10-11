<?php

namespace rollun\api\Api\Megaplan;

use rollun\installer\Install\InstallerAbstract;

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
            return [
                'megaplan' => [
                    'api_url' => '',
                    'login' => '',
                    'password' => '',
                ],
                'dataStore' => [
                    'megaplan_deal_dataStore_service' => [
                        'singleEntity' => 'dealEntity',
                        'listEntity' => 'dealsEntity',
                        'class' => \rollun\api\Api\Megaplan\DataStore\MegaplanDataStore::class,
                    ],
                ],
                'megaplan_entities' => [
                    'deals' => [
                        'dealListFields' => 'dealListFields',
                        'filterField' => [
                            \rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory::FILTER_FIELD_PROGRAM_KEY => 6,
                        ],
                        'requestedFields' => [],
                        'extraFields' => [],
                    ],
                ],
                'dependencies' => [
                    'invokables' => [
                        \rollun\api\Api\Megaplan\Serializer\MegaplanSerializer::class =>
                            \rollun\api\Api\Megaplan\Serializer\MegaplanSerializer::class,
                        \rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions::class =>
                            \rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions::class,
                    ],
                    'factories' => [
                        \Megaplan\SimpleClient\Client::class =>
                            \rollun\api\Api\Megaplan\Entity\Factory\MegaplanClientFactory::class,
                        \rollun\api\Api\Megaplan\Entity\Deal\Deals::class =>
                            \rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory::class,
                        \rollun\api\Api\Megaplan\Entity\Deal\Fields::class =>
                            \rollun\api\Api\Megaplan\Entity\Deal\Factory\FieldsFactory::class,
                        \rollun\api\Api\Megaplan\Entity\Deal\Deal::class =>
                            \rollun\api\Api\Megaplan\Entity\Deal\Factory\DealFactory::class,
                    ],
                    'abstract_factories' => [
                        \rollun\api\Api\Megaplan\DataStore\Factory\MegaplanAbstractFactory::class,
                    ],
                    'aliases' => [
                        'megaplanClient' => \Megaplan\SimpleClient\Client::class,
                        'serializer' => \rollun\api\Api\Megaplan\Serializer\MegaplanSerializer::class,
                        'options' => \rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions::class,
                        'dealsEntity' => \rollun\api\Api\Megaplan\Entity\Deal\Deals::class,
                        'dealEntity' => \rollun\api\Api\Megaplan\Entity\Deal\Deal::class,
                        'dealListFields' => \rollun\api\Api\Megaplan\Entity\Deal\Fields::class,
                        'dataStore' => 'megaplan_deal_dataStore_service',
                    ],
                    'shared' => [
                        'serializer' => false,
                        'options' => false,
                    ],
                ],
            ];
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
        return (isset($config['dependencies']['aliases']['megaplanClient']));
    }
}