<?php

namespace rollun\api\Api\Google\Gmail;

use Google_Service_Gmail_MessagePart;
use rollun\api\Api\Google\Gmail\GoogleServiceGmail;
use \Google_Service_Gmail_Message as GmailMessage;
use rollun\api\Api\Google\GoogleClientAbstract;

/**
 *
 * Add to config:
 * <code>
 * 'services' => [
 *       'factories' => [
 *           GoogleClient::class => GoogleClientFactory::class,
 *       ],
 *       'aliases' => [
 *           'gmailGoogleClient' => GoogleClient::class,
 * ]
 * </code>
 *
 * time GMT
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
     *
     * @var GoogleClient
     */
    protected $gmailGoogleClient;

    /**
     *
     * @var array
     */
    public $messagesList = null;

    public function __construct(GoogleClientAbstract $gmailGoogleClient)
    {
        $this->gmailGoogleClient = $gmailGoogleClient;
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
    public function getGmailMessages($q = null)
    {
        $optParams = $q ? ['q' => $q] : [];
        $optParams['maxResults'] = 1000; // Return Only 1000 Messages
        //$optParams['labelIds'] = 'INBOX'; // Only show messages in Inbox
        $list = [];
        do {
            if (isset($nextPageToken)) {
                $optParams['pageToken'] = $nextPageToken;
            }
            $googleServiceGmail = new GoogleServiceGmail($this->gmailGoogleClient);
            $messages = $googleServiceGmail->users_messages->listUsersMessages('me', $optParams);
            $list = $messages->getMessages() ? array_merge($list, $messages->getMessages()) : $list;
            $nextPageToken = $messages->getNextPageToken();
        } while (!is_null($nextPageToken));
        return $list;
    }

    public function getMessagesIds($q = null)
    {
        $list = $this->getGmailMessages($q);
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

}
