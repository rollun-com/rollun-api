<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16.01.17
 * Time: 12:26
 */

namespace rollun\api\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;
use rollun\api\Api\Gmail\GmailClient;
use rollun\api\Api\Google\Gmail\GoogleServiceGmail;
use rollun\api\Api\Google\Gmail\MessagesList;
use rollun\api\Api\Google\Client\Cli as ApiGoogleClientCli;
use rollun\api\Api\Google\Gmail\Message as GmailMessage;
use rollun\api\Api\Google\Gmails\MessagesList\GmailsFilteredList;
use rollun\api\Api\Google\Gmails\Data\MessageData;

class HelloAction implements MiddlewareInterface
{

    /**
     * @var TemplateRendererInterface
     */
    protected $templateRenderer;

    /**
     * @var GmailsFilteredList
     */
    protected $gmailsList;

    /**
     * HelloAction constructor.
     * @param TemplateRendererInterface $templateRenderer
     */
    public function __construct(TemplateRendererInterface $templateRenderer, GmailsFilteredList $gmailsList)
    {
        $this->templateRenderer = $templateRenderer;
        $this->gmailsList = $gmailsList;
    }

    /**
     * Process an incoming request and/or response.
     *
     * Accepts a server-side request and a response instance, and does
     * something with them.
     *
     * If the response is not complete and/or further processing would not
     * interfere with the work done in the middleware, or if the middleware
     * wants to delegate to another process, it can use the `$out` callable
     * if present.
     *
     * If the middleware does not return a value, execution of the current
     * request is considered complete, and the response instance provided will
     * be considered the response to return.
     *
     * Alternately, the middleware may return a response instance.
     *
     * Often, middleware will `return $out();`, with the assumption that a
     * later middleware will return a response.
     *
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        //$googleServiceGmail = new GoogleServiceGmail($this->gmailGoogleClient);
//        try {
//            $messagesList = new MessagesList($this->gmailGoogleClient);
//        } catch (\Exception $exc) {
//            throw $exc;
//        }
//        $str = $messagesList->getMessagesIds();
        $messages = $this->gmailsList->getMessagesListItems(10);
        //return new HtmlResponse($this->templateRenderer->render('app::home-page', ['str' => print_r($messages[0], true)]));
        //$message = new GmailMessage('1598794b3c840513'); //new GmailMessage($messages[0]->getId());
        //$str = $message->getBodyHtml();
        $ids = $this->gmailsList->getIdsFromMessagesListItems($messages);
        $mees = $this->gmailsList->getGmailMessage($ids[0]);
        $data = new MessageData($mees);
        $str = $data->getArrayCopy();
        return new HtmlResponse($this->templateRenderer->render('app::gmail_message', ['str' => $str]));
    }

}
