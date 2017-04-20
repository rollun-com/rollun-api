<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 12:26
 */

namespace rollun\api\App;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use rollun\api\Api\Google\Gmail\MessagesList;

class MessagesListAction implements MiddlewareInterface
{

    /**
     *
     * @var MessagesList
     */
    public $messagesList;

    public function __construct(MessagesList $messagesList)
    {
        $this->messagesList = $messagesList;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param Request $request
     * @param DelegateInterface $delegate
     * @return Response
     * @throws \Exception
     */
    public function process(Request $request, DelegateInterface $delegate)
    {

        $str = get_class($this->messagesList);

        if ($name === "error") {
            throw new \Exception("Exception by string: $str");
        }
        $request = $request->withAttribute('responseData', ['str' => $str]);
        $response = $delegate->process($request);
        return $response;
    }

}
