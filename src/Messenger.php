<?php
namespace yondbee\FacebookMessenger;

use Yii;
use GuzzleHttp\Psr7;

/**
 * Class Messenger
 * @package yondbee\FacebookMessenger
 */
class Messenger
{
    protected $api;

    protected $notification_type = null;

    public static function create($token)
    {
        return new static($token);
    }

    public function __construct($token)
    {
        $this->api = new MessengerApi($token);
    }

    public function hubReply(array $data, $verify_token)
    {
        //change to request data array
        if (array_key_exists('hub_mode', $data) &&
            $data['hub_mode'] == 'subscribe' &&
            $data['hub_verify_token'] == $verify_token
        ) {
            return new Psr7\Response(200,null,$data['hub_challenge']);
        }
        return new Psr7\Response(400,null,'No Valid Challenge');
    }

    //all message objects have toArray on them...
    public function send($message, $path = 'me/messages')
    {
        return $this->api->callApi($path, $message->toArray());
    }

    public function receive($data)
    {
        //parse incoming data and return a callback object
        return Callback::create($data);
    }

    public function user($user_id)
    {
        return UserProfile::create($this->api->callApi($user_id, null, MessengerApi::GET));
    }

    /*
     * Personalization...
     * {{user_first_name}}
     * {{user_last_name}}
     * {{user_full_name}}
     */
    public function setGreetingText($text)
    {
        // build greetings in all languages
        $greetings = [];
        if (is_string($text))
        {
            $greetings[] = [
                'locale' => 'default',
                'text' => MessengerUtils::checkStringEncoding(
                          MessengerUtils::checkStringLength($text, 160), 'UTF-8')
            ];
        }
        else if (is_array($text))
            $greetings = array_map(function ($k, $v) {
                return [
                  'locale'  => $k,
                  'text'    => MessengerUtils::checkStringEncoding(
                               MessengerUtils::checkStringLength($v, 160), 'UTF-8')
                ];
            }, array_keys($text), $text);

        if (!empty($greetings)) {

            Yii::info('[FBM] Trying to set greeting text: ' . print_r($greetings, true));

            return $this->api->callApi('me/messenger_profile', [
                'greeting' => $greetings
            ]);
        }
        return false;
    }

    public function deleteGreetingText()
    {
        return $this->api->callApi('me/messenger_profile', [
            'fields' => ['greeting'],
        ], MessengerApi::DELETE);
    }

    public function setGetStartedButton($payload)
    {
        return $this->api->callApi('me/messenger_profile', [
            'get_started' => [
                'payload' => $payload
            ]
        ]);
    }

    public function deleteGetStartedButton()
    {
        return $this->api->callApi('me/messenger_profile', [
            'fields' => ['get_started'],
        ], MessengerApi::DELETE);
    }

    // takes array of persistent menus or just one
    public function setPersistentMenu($menu)
    {
        $data = [];
        if (is_array($menu))
            $data['persistent_menu'] = array_map(function ($m) { return $m->toArray(); }, $menu);
        else
            $data['persistent_menu'] = [$menu->toArray()];

        Yii::info('[FBM] Trying to set persistent menu: ' . print_r($data, true));

        return $this->api->callApi('me/messenger_profile', $data);
    }

    public function deletePersistentMenu()
    {
        return $this->api->callApi('me/messenger_profile', [
            'fields' => ['persistent_menu'],
        ], MessengerApi::DELETE);
    }

    public function setWhitelistedDomains(array $domains)
    {
        return $this->api->callApi('me/messenger_profile', [
            'whitelisted_domains' => MessengerUtils::checkArraySize($domains, 50)
        ]);
    }

    public function deleteWhitelistedDomains()
    {
        return $this->api->callApi('me/messenger_profile', [
            'fields' => ['whitelisted_domains'],
        ], MessengerApi::DELETE);
    }

    public function setChatExtensionHome($url, $share = 'hide')
    {
        return $this->api->callApi('me/messenger_profile', [
            'home_url' =>
                [
                    'url' => $url,
                    'webview_height_ratio' => 'tall',
                    'webview_share_button' => $share,
                    'in_test' => YII_ENV != 'prod'
                ]
        ]);
    }

    public function deleteChatExtensionHome()
    {
        return $this->api->callApi('me/messenger_profile', [
            'fields' => ['home_url'],
        ], MessengerApi::DELETE);
    }

    public function getAppScopedId($page_user_id, $app_id, $app_secret)
    {
        $token = $this->api->getToken();
        $appsecret_proof = hash_hmac('sha256', $token, $app_secret);

        Yii::info("[FBM] $page_user_id/ids_for_apps?access_token=$token&appsecret_proof=$appsecret_proof&app=$app_id");

        $result = $this->api->callApi($page_user_id . '/ids_for_apps', [
            'app' => $app_id,
            'appsecret_proof' => $appsecret_proof
        ], MessengerApi::GET, false);

        Yii::info('[FBM] /ids_for_apps result ' . print_r($result,true));

        // loop over the result, and select the wanted id
        foreach ($result->data as $item)
            if ($item->app->id == $app_id)
                return $item->id;

        return false;
    }

    public function getPageScopedId($app_user_id, $page_id, $app_secret)
    {
        return $this->api->callApi($app_user_id . '/ids_for_pages', [
            'page' => $page_id,
            'appsecret_proof' => hash_hmac('sha256', $this->api->getToken(), $app_secret)
        ], MessengerApi::GET, false);
    }

    protected function addRecipient($data, $recipient_id)
    {
        $data['recipient'] = ['id' => $recipient_id];
        return $data;
    }

    //Todo Fix Auth
//    public function linkAccount($account_linking_token)
//    {
//        //return PAGE_ID & PSID
//        return $this->api->callApi(
//            'me?fields=recipient&account_linking_token=' . $account_linking_token,
//            [],
//            MessengerApi::GET);
//    }
//
//    public function getLinkAccountPSID($account_linking_token)
//    {
//        //return PAGE_ID & PSID
//        return $this->api->callApi(
//            'me?fields=recipient&account_linking_token=' . $account_linking_token,
//            [],
//            MessengerApi::GET);
//    }
//
//    public function unlinkAccount($psid)
//    {
//        return $this->api->callApi('me/unlink_accounts', [
//            'psid' => $psid,
//        ]);
//    }

//TODO Fix api call - Tried accessing nonexisting field
//    public function validateWebhook($app_id, $page_id)
//    {
//        Log::debug(
//            $this->api->callApi($app_id . '/subscriptions_sample?object_id=' . $page_id . '&object=page&custom_fields={"page_id":' . $page_id.'}',
//                [], MessengerApi::GET)
//        );
//    }
}
