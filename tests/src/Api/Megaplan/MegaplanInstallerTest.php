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
                'rollun\api\Api\Megaplan\Serializer\Megaplan' => 'rollun\api\Api\Megaplan\Serializer\Megaplan',
                'rollun\api\Api\Megaplan\Serializer\MegaplanOptions' => 'rollun\api\Api\Megaplan\Serializer\MegaplanOptions',
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
                'Megaplan\SimpleClient\Client' => 'rollun\api\Api\Megaplan\Entity\Factory\MegaplanClientFactory',
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
                'rollun\api\Api\Megaplan\Entity\Factory\AbstractFactory',
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
                'megaplan' => 'Megaplan\SimpleClient\Client',
                'serializer' => 'rollun\api\Api\Megaplan\Serializer\Megaplan',
                'serializerOptions' => 'rollun\api\Api\Megaplan\Serializer\MegaplanOptions',
            ],
            $installedConfig['dependencies'][$sectionName]
        );
    }
}