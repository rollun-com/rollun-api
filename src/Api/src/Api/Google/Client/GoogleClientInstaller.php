<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.03.17
 * Time: 12:10
 */

namespace rollun\api\Api\Google\Client;

use Google_Service_Gmail;
use rollun\api\Api\Google\Client\Factory\AbstractFactory as ApiGoogleClientAbstractFactory;
use rollun\datastore\AbstractFactoryAbstract;
use rollun\installer\Install\InstallerAbstract;
use rollun\utils\DbInstaller;

class GoogleClientInstaller extends InstallerAbstract
{

    /**
     * install
     * @return array
     */
    public function install()
    {
        return [
            'dependencies' => [
                'abstract_factories' => [
                    ApiGoogleClientAbstractFactory::class,
                ],
            ],
        ];
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Дает возможность использовать cli google client.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }

    public function isInstall()
    {
        $config = $this->container->get('config');
        return (
            isset($config['dependencies']['abstract_factories']) &&
            in_array(ApiGoogleClientAbstractFactory::class, $config['dependencies']['abstract_factories'])
        );
    }
}
