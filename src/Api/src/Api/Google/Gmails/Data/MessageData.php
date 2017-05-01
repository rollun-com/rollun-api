<?php

namespace rollun\api\Api\Google\Gmails\Data;

use rollun\api\Api\Google\Gmails\Data\DbDataStore as DataStoreEmails;
use \Google_Service_Gmail_Message as GmailMessage;
use Zend\Stdlib\ArrayObject;
use rollun\logger\Exception\LoggedException;
use rollun\utils\Json\Coder as JsonCoder;

class MessageData extends ArrayObject
{

    /**
     *
     * @param  GmailMessage|Array
     */
    public function __construct($message)
    {

        switch (true) {
            case is_a($message, GmailMessage::class, true):
                /* @var $message GmailMessage */
                $messageDataArray[DataStoreEmails::MESSAGE_ID] = $message->getId();
                $messageDataArray[DataStoreEmails::SUBJECT] = $this->getGmailHeader($message, 'Subject');
                $messageDataArray[DataStoreEmails::SENDING_TIME] = $this->getGmailSendingTime($message);
                $messageDataArray[DataStoreEmails::FROM] = $this->getGmailHeader($message, 'From');
                $messageDataArray[DataStoreEmails::BODY_HTML] = $this->getGmailBodyHtml($message);
                $messageDataArray[DataStoreEmails::BODY_TXT] = $this->getGmailBodyTxt($message);
                $messageDataArray[DataStoreEmails::HEADERS] = JsonCoder::jsonEncode($this->getGmailHeaders($message));
                break;

            case is_a($message, '\ArrayObject', true) || is_array($message):
                /* @var $message array */
                $messageDataArray = $message;
                break;

            default:
                $type = is_object($message) ? get_class($message) : gettype($message);
                throw new LoggedException('Wrong param type $message:' . $type);
        }

        parent::__construct($messageDataArray);
    }

    protected function getGmailPayload(GmailMessage $gmailMessage)
    {
        return $gmailMessage->getPayload();
    }

    protected function getGmailHeaders(GmailMessage $gmailMessage)
    {
        return $this->getGmailPayload($gmailMessage)->getHeaders();
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
     * time UTC
     * @see http://stackoverflow.com/questions/25427670/how-to-use-gmail-api-query-filter-for-datetime?noredirect=1&lq=1
     * @see http://stackoverflow.com/questions/33552890/why-does-search-in-gmail-api-return-different-result-than-search-in-gmail-websit
     * @param GmailMessage $gmailMessage
     * @return type
     */
    protected function getGmailSendingTime(GmailMessage $gmailMessage)
    {
        $pstTime = $this->getGmailHeader($gmailMessage, 'Date'); //Pacific Standard Time
        $utcTimeInSec = strtotime($pstTime) + 8 * 60 * 60;
        return $utcTimeInSec;
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

}
