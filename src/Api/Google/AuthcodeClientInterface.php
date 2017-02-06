<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06.02.17
 * Time: 17:20
 */

namespace rollun\api\Api\Google;

interface AuthcodeClientInterface extends ClientInterface
{
    /**
     * @param $code
     * @return void
     */
    public function setAuthCode($code);

    /**
     * @return string
     */
    public function getAuthCode();

    /**
     * state is cripted key for protection response with credential;
     * @param $state
     */
    public function requestAuthCode($state);
}
