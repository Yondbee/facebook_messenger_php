<?php

namespace yondbee\FacebookMessenger\Callback;

use yondbee\FacebookMessenger\Helper;

class Postback
{
    public $payload;
    public $referral;

    /**
     * Delivered constructor.
     * @param $payload
     */
    public function __construct($postback)
    {
        $this->payload = Helper::array_find($postback, 'payload');
        $this->referral = Helper::array_find($postback, 'referral');
    }
}
