<?php

namespace rollun\test\api\Api\Megaplan;

use rollun\api\Api\Megaplan\MegaplanInstaller;
use rollun\installer\TestCase\InstallerTestCase;

class MegaplanInstallerTest extends InstallerTestCase
{
    public function test_install_shouldReturnInstalledConfig()
    {
        $container = $this->getContainer();

        $userInput = "y\n";
        $outputStream = $this->getOutputStream();
        $io = $this->getIo($userInput, $outputStream);

        $installer = new MegaplanInstaller($container, $io);

        $installedConfig = $installer->install();

        $this->assertTrue(
            is_array($installedConfig)
        );

        rewind($outputStream); //НЕ ЗАБЫВАЙТЕ ЭТО СДЕЛАТЬ!

        return $installedConfig;
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainMegaplanKey($installedConfig)
    {
        $sectionName = 'megaplan';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig
        );

        $this->assertArrayHasKey(
            'api_url', $installedConfig[$sectionName]
        );

        $this->assertArrayHasKey(
            'login', $installedConfig[$sectionName]
        );

        $this->assertArrayHasKey(
            'password', $installedConfig[$sectionName]
        );
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainMegaplanEntitiesKey($installedConfig)
    {
        $sectionName = 'megaplan_entities';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig
        );
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainInvokablesSection($installedConfig)
    {
        $sectionName = 'invokables';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig['dependencies']
        );
        $this->assertEquals(
            [
                \rollun\api\Api\Megaplan\Serializer\MegaplanSerializer::class =>
                    \rollun\api\Api\Megaplan\Serializer\MegaplanSerializer::class,
                \rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions::class =>
                    \rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions::class,
            ],
            $installedConfig['dependencies'][$sectionName]
        );
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainFactoriesSection($installedConfig)
    {
        $sectionName = 'factories';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig['dependencies']
        );

        $this->assertEquals(
            [
                \Megaplan\SimpleClient\Client::class =>
                    \rollun\api\Api\Megaplan\Entity\Factory\MegaplanClientFactory::class,
                \rollun\api\Api\Megaplan\Entity\Deal\Deals::class =>
                    \rollun\api\Api\Megaplan\Entity\Deal\Factory\DealsFactory::class,
                \rollun\api\Api\Megaplan\Entity\Deal\Fields::class =>
                    \rollun\api\Api\Megaplan\Entity\Deal\Factory\FieldsFactory::class,
                \rollun\api\Api\Megaplan\Entity\Deal\Deal::class =>
                    \rollun\api\Api\Megaplan\Entity\Deal\Factory\DealFactory::class,
            ],
            $installedConfig['dependencies'][$sectionName]
        );
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainAbstractFactoriesSection($installedConfig)
    {
        $sectionName = 'abstract_factories';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig['dependencies']
        );
        $this->assertEquals(
            [
                \rollun\api\Api\Megaplan\DataStore\Factory\MegaplanAbstractFactory::class,
            ],
            $installedConfig['dependencies'][$sectionName]
        );
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainAliasesSection($installedConfig)
    {
        $sectionName = 'aliases';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig['dependencies']
        );
        $this->assertEquals(
            [
                'megaplanClient' => \Megaplan\SimpleClient\Client::class,
                'serializer' => \rollun\api\Api\Megaplan\Serializer\MegaplanSerializer::class,
                'options' => \rollun\api\Api\Megaplan\Serializer\MegaplanSerializerOptions::class,
                'dealsEntity' => \rollun\api\Api\Megaplan\Entity\Deal\Deals::class,
                'dealEntity' => \rollun\api\Api\Megaplan\Entity\Deal\Deal::class,
                'dealListFields' => \rollun\api\Api\Megaplan\Entity\Deal\Fields::class,
                'dataStore' => 'megaplan_deal_dataStore_service',
            ],
            $installedConfig['dependencies'][$sectionName]
        );
    }

    /**
     * @depends test_install_shouldReturnInstalledConfig
     * @param $installedConfig
     */
    public function test_installedConfig_shouldContainSharedSection($installedConfig)
    {
        $sectionName = 'shared';
        $this->assertArrayHasKey(
            $sectionName, $installedConfig['dependencies']
        );
        $this->assertEquals(
            [
                'serializer' => false,
                'options' => false,
            ],
            $installedConfig['dependencies'][$sectionName]
        );
    }
}