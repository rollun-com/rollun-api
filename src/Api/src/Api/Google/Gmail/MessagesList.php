<?php

namespace rollun\api\Api\Google\Gmail;

use Google_Service_Gmail_MessagePart;
use rollun\api\Api\Google\Gmail\GoogleServiceGmail;
use \Google_Service_Gmail_Message as GmailMessage;
use rollun\api\Api\Google\Client\ClientAbstract;
use rollun\api\Api\Google\Gmail\MessagesList\CachedObject;
use rollun\logger\Exception\LoggedException;
use rollun\utils\Time\UtcTime;

/**
 *
 * Add to config:
 * <code>
 * 'services' => [
 *        'abstract_factories' => [
 *           MessagesListAbstractFactory::class
 *       ],
 * ]
 * </code>
 *
 * time UTC
 * @see http://stackoverflow.com/questions/25427670/how-to-use-gmail-api-query-filter-for-datetime?noredirect=1&lq=1
 * @see http://stackoverflow.com/questions/33552890/why-does-search-in-gmail-api-return-different-result-than-search-in-gmail-websit
 *
 * @see http://stackoverflow.com/search?q=PHP%2C+Gmail+API+read
 * @see https://github.com/adevait/GmailPHP/blob/master/examples/messages.php
 * @see https://developers.google.com/gmail/api/quickstart/php#step_1_install_the_google_client_library
 */
class MessagesList
{

    /**
     * '/^[a-z0-9_\+\-]*$/Di' - any spaces, @ ...
     *
     * @var string MessagesListName like 'emails_from_Bob '
     */
    protected $name;

    /**
     *
     * @var GoogleClient
     */
    protected $gmailGoogleClient;

    /**
     * @opt_param bool includeSpamTrash Include messages from SPAM and TRASH in the
     * results.
     * @opt_param string labelIds Only return messages with labels that match all of
     * the specified label IDs.
     * @opt_param string maxResults Maximum number of messages to return.
     * @opt_param string pageToken Page token to retrieve a specific page of results
     * in the list.
     * @opt_param string q Only return messages matching the specified query.
     * @var string https://support.google.com/mail/answer/7190?hl=en&ref_topic=3394914
     */
    protected $optParam;

    /**
     *
     * @var CachedObject
     */
    protected $cachedObject;

    /**
     *
     * @var array
     */
    public $messagesList = null;

    /**
     *
     * @param string $name '/^[a-z0-9_\+\-]*$/Di'
     * @param ClientAbstract $gmailGoogleClient
     * @param array $optParam
     */
    public function __construct($name, ClientAbstract $gmailGoogleClient, $optParam = [])
    {
        static::checkName($name);
        $this->name = $name;
        $this->gmailGoogleClient = $gmailGoogleClient;
        $this->cachedObject = new CachedObject($this->name);

        if (!isset($optParam['maxResults'])) {
            $optParam['maxResults'] = 1000;
        }
        $optParam['pageToken'] = null;
        $this->optParam = $optParam;
    }

    /**
     *
     * @param int|null $actualDate  null - from Gmail, 0 - from Cache, Int - may be from cache (if actual)
     * @return type
     */
    public function getMessages($actualDate = null)
    {
        if (is_null($actualDate)) {
            // get acual data
            return $this->getGmailMessages();
        }

        $dateOfCache = $this->getDateOfCache();
        if (is_null($dateOfCache)) {
            // cache is empty
            return $this->getGmailMessages();
        }

        if ($actualDate <= $dateOfCache) {
            return $this->getCachedMessages();
        } else {
            // cache is invalidate
            return $this->getGmailMessages();
        }
    }

    public function getCachedMessages()
    {
        $list = $this->cachedObject->getMessagesListData();
        if (is_null($list)) {
            $this->cachedObject->loadFromCache();
            $list = $this->cachedObject->getMessagesListData();
        }
        return $list;
    }

    public function getDateOfCache()
    {
        $dateOfCache = $this->cachedObject->getDateOfCache();
        if (is_null($dateOfCache)) {
            $this->cachedObject->loadFromCache();
            $dateOfCache = $this->cachedObject->getDateOfCache();
        }
        return $dateOfCache;
    }

    public function getMessagesIds()
    {
        $list = $this->getGmailMessages();
        $messageIds = [];
        foreach ($list as $message) {
            $messageIds[] = $message->getId();
        }
        return $messageIds;
    }

    public function getGmailMessage($messageId)
    {
        $optParamsGet['format'] = 'full'; // Display message in payload
        $googleServiceGmail = new GoogleServiceGmail($this->gmailGoogleClient);
        $message = $googleServiceGmail->users_messages->get('me', $messageId, $optParamsGet);
        return $message;
    }

    /**
     *
     * @see https://support.google.com/mail/answer/7190?hl=en
     * @see http://www.technostall.com/how-to-use-gmail-search-box/
     * @see from:someuser@example.com
     * @see $opt_param['q'] = 'filename:(jpg OR png OR gif)'
     * @see $opt_param['q'] = 'subject:"reservation request"';
     * @todo limit,offset, query
     * @return array
     */
    protected function getGmailMessages()
    {
        $list = [];
        $this->optParams['pageToken'] = null;
        do {
            $googleServiceGmail = new GoogleServiceGmail($this->gmailGoogleClient);
            $messages = $googleServiceGmail->users_messages->listUsersMessages('me', $this->optParams);
            $list = $messages->getMessages() ? array_merge($list, $messages->getMessages()) : $list;
            $nextPageToken = $messages->getNextPageToken();
            $this->optParams['pageToken'] = $nextPageToken;
        } while (!is_null($nextPageToken));

        $this->cachedObject->setMessagesListData($list);
        $this->cachedObject->saveToCache();
        return $list;
    }

    public static function checkName($name)
    {
        if (empty($name)) {
            throw new LoggedException(
            "An empty $name isn't allowed"
            );
        }
        $pattern = '/^[a-z0-9_\+\-]*$/Di';
        if (!preg_match($pattern, $name)) {
            throw new LoggedException(
            "The key '{$name}' doesn't match against pattern '{$pattern}'"
            );
        }

        return $name;
    }

}
