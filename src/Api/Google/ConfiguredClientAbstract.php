<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.02.17
 * Time: 11:22
 */

namespace rollun\api\Api\Google;

abstract class ConfiguredClientAbstract extends ClientAbstract
{

    /**
     * AuthcodeClientAbstract constructor.
     * @param string $clientName
     * @param array $config
     */
    //public function __construct($clientName, array $config = [])
    public function __construct(array $config , $clientName)
    {
        $this->clientName = $clientName;
        parent::__construct($config);
        $this->setConfigFromSecretFile();
    }

}
