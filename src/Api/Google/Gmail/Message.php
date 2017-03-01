<?php

namespace rollun\api\Api\Google\Gmail;

use rollun\api\Api\Google\Gmail\DbDataStore as DataStoreEmails;
use Zend\Filter\Word\UnderscoreToCamelCase as UnderscoreToCamelCaseFilter;
use Zend\Filter\Word\CamelCaseToUnderscore as CamelCaseToUnderscoreFilter;
use rollun\api\Api\Google\Gmail\MessagesList;
use \Google_Service_Gmail_Message as GmailMessage;
use rollun\dic\InsideConstruct;
use rollun\utils\Json\Serializer as JsonSerializer;
use rollun\api\Api\Google\Client\ClientAbstract as GoogleClientAbstract;

/**
 *
 *
 * Use rollun\api\Api\Google\Client\Cli for child of rollun\api\Api\Google\Client\ClientAbstract
 * for $gmailGoogleClient. Use rollun\api\Api\Google\Client\Factory\AbstractFactory fo create it.
 *
 * time UTC
 * @see http://stackoverflow.com/questions/25427670/how-to-use-gmail-api-query-filter-for-datetime?noredirect=1&lq=1
 * @see http://stackoverflow.com/questions/33552890/why-does-search-in-gmail-api-return-different-result-than-search-in-gmail-websit
 */
class Message
{

    /**
     *
     * @var ClientAbstract
     */
    protected $gmailGoogleClient;

    /**
     *
     * @var DataStoreEmails
     */
    protected $messageData;
    protected $filds = [
        DataStoreEmails::MESSAGE_ID,
        DataStoreEmails::SUBJECT,
        DataStoreEmails::SENDING_TIME,
        DataStoreEmails::FROM,
        DataStoreEmails::BODY_HTML,
        DataStoreEmails::BODY_TXT,
        DataStoreEmails::HEADERS
    ];

    public function __construct($messageId, GoogleClientAbstract $gmailGoogleClient = null)
    {
        //set $this->gmailGoogleClient as $cotainer->get('gmailGoogleClient');
        InsideConstruct::setConstructParams();

        $dataStore = new DataStoreEmails();
        $this->messageData = $dataStore->read($messageId);
        if (isset($this->messageData)) {
            $jsonStringHeaders = $this->messageData[DataStoreEmails::HEADERS];
            $arrayHeaders = (array) JsonSerializer::jsonUnserialize($jsonStringHeaders);
            $this->messageData[DataStoreEmails::HEADERS] = $arrayHeaders;
            return;
        }

        $messagesList = new MessagesList($this->gmailGoogleClient);
        $gmailMessage = $messagesList->getGmailMessage($messageId);
        $filter = new UnderscoreToCamelCaseFilter();
        foreach ($this->filds as $fildName) {
            $camelCaseFildName = $filter($fildName);
            $value = call_user_func([$this, 'getGmail' . $camelCaseFildName], $gmailMessage);
            call_user_func([$this, 'set' . $camelCaseFildName], $value);
        }
        $storedMessage = $this->messageData;
        $arrayHeaders = $this->messageData[DataStoreEmails::HEADERS];
        $jsonStringHeaders = JsonSerializer::jsonSerialize($arrayHeaders);
        $storedMessage[DataStoreEmails::HEADERS] = $jsonStringHeaders;
        $dataStore->create($storedMessage, true);
    }

    /**
     *
     * getId(), getSubject() ...
     * setId(), setSubject() ...
     */
    public function __call($name, array $params)
    {
        $namePrefix = substr($name, 0, 3);
        $getOrSet = $namePrefix == 'get' || $namePrefix == 'set' ? $namePrefix : false;
        $nameWitoutPrefix = substr($name, strlen('get'));
        $filter = new CamelCaseToUnderscoreFilter();
        $underscoreFildName = strtolower($filter($nameWitoutPrefix));
        $underscoreFildName = $underscoreFildName === 'message_id' ? 'id' : $underscoreFildName;
        if (!in_array($underscoreFildName, $this->filds) or ! $getOrSet) {
            throw new \RuntimeException('Wrong method name: ' . $name);
        }
        if ($getOrSet === 'get') {
            return $this->messageData[$underscoreFildName];
        } else {
            $this->messageData[$underscoreFildName] = $params[0];
        }
    }

    protected function getGmailId(GmailMessage $gmailMessage)
    {
        return $gmailMessage->getId();
    }

    protected function getGmailSubject(GmailMessage $gmailMessage)
    {
        return $this->getGmailHeader($gmailMessage, 'Subject');
    }

    protected function getGmailSendingTime(GmailMessage $gmailMessage)
    {
        $pstTime = $this->getGmailHeader($gmailMessage, 'Date'); //Pacific Standard Time
        $utcTimeInSec = strtotime($pstTime) + 8 * 60 * 60;
        return $utcTimeInSec;
    }

    protected function getGmailFrom(GmailMessage $gmailMessage)
    {
        return htmlspecialchars($this->getGmailHeader($gmailMessage, 'From'));
    }

    protected function getGmailHeader(GmailMessage $gmailMessage, $name)
    {
        $headers = $this->getGmailHeaders($gmailMessage);
        foreach ($headers as $header) {
            if ($header['name'] == $name) {
                return $header['value'];
            }
        }
        return null;
    }

    /**
     *
     * @see http://stackoverflow.com/questions/24503483/reading-messages-from-gmail-in-php-using-gmail-api
     * @see http://stackoverflow.com/questions/32655874/cannot-get-the-body-of-email-with-gmail-php-api
     */
    protected function getGmailBodyHtml(GmailMessage $gmailMessage)
    {
        $payload = $this->getGmailPayload($gmailMessage);
        $bodyData = $this->parseGmailParts($payload);
        $bodyHtml = isset($bodyData['text/html']) ? implode('', $bodyData['text/html']) : null;
        return $bodyHtml;
    }

    protected function getGmailBodyTxt(GmailMessage $gmailMessage)
    {
        $payload = $this->getGmailPayload($gmailMessage);
        $bodyData = $this->parseGmailParts($payload);
        $bodyText = isset($bodyData['text/plain']) ? implode('', $bodyData['text/plain']) : null;
        return $bodyText;
    }

    protected function getGmailPayload(GmailMessage $gmailMessage)
    {
        return $gmailMessage->getPayload();
    }

    protected function getGmailHeaders(GmailMessage $gmailMessage)
    {
        return $this->getGmailPayload($gmailMessage)->getHeaders();
    }

    /**
     *
     * @see mimeType RFC https://www.ietf.org/rfc/rfc1521.txt
     * @see mimeType RFC https://tools.ietf.org/html/rfc1341
     *
     * @param \zaboy\utils\Api\Gmail\Google_Service_Gmail_MessagePart $payload
     * @param type $result
     * @return type
     * @throws \RuntimeException
     */
    protected function parseGmailParts($payload, $result = [])
    {
        $mimeType = $payload->getMimeType();
        if ($mimeType === 'text/plain' || $mimeType === 'text/html') {
            $decodedPart = base64_decode(str_replace(array('-', '_'), array('+', '/'), $payload->getBody()->getData()));
            $partId = $payload->getPartId();
            if (isset($result[$mimeType][$partId])) {
                throw new \RuntimeException('Part ' . $partId . ' exist.');
            }
            $result[$mimeType][$partId] = $decodedPart;
        }
        $parts = $payload->getParts() ? $payload->getParts() : [];
        foreach ($parts as $part) {
            $result = $this->parseGmailParts($part, $result);
        }
        return $result;
    }

}
