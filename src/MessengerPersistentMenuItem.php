<?php
/**
 * Created by PhpStorm.
 * User: asanta
 * Date: 7/18/17
 * Time: 6:34 PM
 */

namespace yondbee\FacebookMessenger;


class MessengerPersistentMenuItem
{
    const TYPE_URL       = 'web_url';
    const TYPE_POSTBACK  = 'postback';
    const TYPE_NESTED    = 'nested';

    protected $type;

    public $title;

    public $url;

    public $payload;

    public $messenger_extensions = false;


    const WEBVIEW_COMPACT   = 'compact';
    const WEBVIEW_TALL      = 'tall';
    const WEBVIEW_FULL      = 'full';

    public $webview_height_ratio;
    public $webview_share_button;


    public $fallback_url;

    // children items
    public $call_to_actions;


    static public function createUrlItem($title, $url)
    {
        $o = new self();
        $o->type = self::TYPE_URL;

        return $o->setTitle($title)
                 ->setUrl($url);
    }

    static public function createPostbackItem($title, $payload)
    {
        $o = new self();
        $o->type = self::TYPE_POSTBACK;

        return $o->setTitle($title)
                 ->setPayload($payload);
    }

    static public function createNestedItem($title, $items = [])
    {
        $o = new self();
        $o->type = self::TYPE_NESTED;

        return $o->setTitle($title)
                 ->setItems($items);
    }

    public function setMessengerExtensions($v, $fallback = null)
    {
        $this->messenger_extensions = $v;
        $this->fallback_url = $v ? $fallback : null;
        return $this;
    }

    public function setDisplayShareButton($v)
    {
        $this->webview_share_button = $v ? null : 'hide';
        return $this;
    }

    public function setWebviewHeight($v)
    {
        $this->webview_height_ratio = $v;
        return $this;
    }

    public function setTitle($v)
    {
        $this->title = MessengerUtils::checkStringEncoding(
            MessengerUtils::checkStringLength($v, 30), 'UTF-8');
        return $this;
    }

    public function setUrl($v)
    {
        $this->url = MessengerUtils::checkStringEncoding(
            MessengerUtils::checkStringLength($v, 255), 'UTF-8');
        return $this;
    }

    public function setPayload($v)
    {
        $this->payload = MessengerUtils::checkStringEncoding(
            MessengerUtils::checkStringLength($v, 1000), 'UTF-8');
        return $this;
    }

    public function addItem(MessengerPersistentMenuItem $i)
    {
        // max depth 5
        if (count($this->call_to_actions) == 5)
            return false;

        $this->call_to_actions = $this->call_to_actions ?: [];
        $this->call_to_actions[] = $i;
        $this->type = self::TYPE_NESTED;

        return $this;
    }

    public function setItems($items)
    {
        // max depth 5
        if (count($items) > 5)
            return false;

        $this->call_to_actions = $items;
        $this->type = self::TYPE_NESTED;

        return $this;
    }

    public function toArray()
    {
        $json = [];
        switch($this->type)
        {
            case self::TYPE_URL:
                 $json = [
                    'type'  => $this->type,
                    'title' => $this->title,
                    'url'   => $this->url,
                 ];

                 if (!empty($this->webview_height_ratio))
                     $json['webview_height_ratio'] = $this->webview_height_ratio;

                 if ($this->messenger_extensions) {
                     $json['messenger_extensions'] = true;

                     if (!empty($this->fallback_url))
                         $json['fallback_url'] = $this->fallback_url;
                 }

                 if (!empty($this->webview_share_button))
                     $json['webview_share_button'] = $this->webview_share_button;

            break;

            case self::TYPE_POSTBACK:
                $json = [
                    'type'      => $this->type,
                    'title'     => $this->title,
                    'payload'   => $this->payload,
                ];
            break;

            case self::TYPE_NESTED:

                $json = [
                    'type'              => $this->type,
                    'title'             => $this->title,
                    'call_to_actions'   => array_map(function ($i) { return $i->toArray(); }, $this->call_to_actions)
                ];

            break;
        }

        return $json;
    }
}
