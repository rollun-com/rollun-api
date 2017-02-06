<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06.02.17
 * Time: 16:58
 */

namespace rollun\api\Api\Google;

interface ClientInterface
{
    /**
     * @return string
     */
    public function getClientName();

    /**
     * @param $accessToken
     * @return void
     */
    public function setCredential($accessToken);

    /**
     * auth clietn with set Credential.
     * @return mixed
     */
    public function authByCredential();
}
