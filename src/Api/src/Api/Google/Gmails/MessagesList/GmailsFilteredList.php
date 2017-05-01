<?php

namespace rollun\api\Api\Google\Gmails\MessagesList;

use Google_Service_Gmail_MessagePart;
use rollun\api\Api\Google\Gmail\GoogleServiceGmail;
use \Google_Service_Gmail_Message as GmailMessage;
use rollun\api\Api\Google\Client\ClientAbstract as GoogleClientAbstract;
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
class GmailsFilteredList
{

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
    protected $optParams;

    /**
     *
     * @param GoogleClientAbstract $gmailGoogleClient
     * @param array $optParams
     */
    public function __construct(GoogleClientAbstract $gmailGoogleClient, $optParams = [])
    {
        $this->gmailGoogleClient = $gmailGoogleClient;
        if (!isset($optParams['maxResults'])) {
            $optParams['maxResults'] = 500;
        }
        $optParams['pageToken'] = null;
        $this->optParams = $optParams;
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
     * $messagesListItems is array of
     * Google_Service_Gmail_Message Object ( [
     * collection_key:protected] => labelIds
     * [historyId] =>
     * [id] => 15baf63e5904cc09
     * [internalDate] =>
     * [labelIds] =>
     * [payloadType:protected] => Google_Service_Gmail_MessagePart
     * [payloadDataType:protected] =>
     * [raw] =>
     * [sizeEstimate] =>
     * [snippet] =>
     * [threadId] => 15baf63e5904cc09
     * [internal_gapi_mappings:protected] => Array ( )
     * [modelData:protected] => Array ( )
     * [processed:protected] => Array ( )
     *
     *
     *
     * @param type $messagesListItems see above
     * @return array af ids ["15baf63e5904cc09", "15baf6345504cc04", ...]
     */
    public function getIdsFromMessagesListItems($messagesListItems)
    {
        $messagesIds = [];
        foreach ($messagesListItems as $message) {
            $messagesIds[] = $message->getId();
        }
        return $messagesIds;
    }

    /**
     *
     * return array of
     * Google_Service_Gmail_Message Object ( [
     * collection_key:protected] => labelIds
     * [historyId] =>
     * [id] => 15baf63e5904cc09
     * [internalDate] =>
     * [labelIds] =>
     * [payloadType:protected] => Google_Service_Gmail_MessagePart
     * [payloadDataType:protected] =>
     * [raw] =>
     * [sizeEstimate] =>
     * [snippet] =>
     * [threadId] => 15baf63e5904cc09
     * [internal_gapi_mappings:protected] => Array ( )
     * [modelData:protected] => Array ( )
     * [processed:protected] => Array ( )
     *
     * @param int|null $newerThan - in days, null - all
     * @return array of $messagesListItems - see above
     */
    public function getMessagesListItems($newerThan = null)
    {
        $list = [];
        $optParams = $this->optParams;
        //$optParams['pageToken'] = null;

        switch (true) {
            case isset($optParams['q']) and ! is_null($newerThan):
                $optParams['q'] = "( " . $optParams['q'] . " ) AND( newer_than:" . (int) $newerThan . "d )";
                break;
            case!isset($optParams['q']) and ! is_null($newerThan):
                $optParams['q'] = "newer_than:" . (int) $newerThan . "d";
                break;
        }

        do {
            $googleServiceGmail = new GoogleServiceGmail($this->gmailGoogleClient);
            $messages = $googleServiceGmail->users_messages->listUsersMessages('me', $optParams);
            $list = $messages->getMessages() ? array_merge($list, $messages->getMessages()) : $list;
            $nextPageToken = $messages->getNextPageToken();
            $optParams['pageToken'] = $nextPageToken;
        } while (!is_null($nextPageToken));

        return $list;
    }

}
