<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 14:13
 */

namespace rollun\test\skeleton\Api;

use PHPUnit_Framework_TestCase;
use rollun\api\Api\HelloAction;
use Zend\Http\Client;

class HelloActionTest extends HelloActionTestProvider
{

    /** @var  Client */
    protected $client;

    public function setUp()
    {
        $this->client = new Client();
    }

    /**
     * @param $param
     * @param $env
     * @param $response
     * @dataProvider providerHtmlQuery()
     *
     */
    public function testHtmlQuery($param, $env, $response, $accept)
    {
        if (constant("APP_ENV") === $env) {

            $uri = "http://" . constant("HOST") . "/" . $param;
            $this->client->setUri($uri);
            $this->client->setHeaders([
                'Accept' => $accept
            ]);
            $resp = $this->client->send();
            $body = $resp->getBody();
            $this->assertTrue(preg_match('/' . quotemeta($response) . '/', $body) == 1);
        }
    }

}
