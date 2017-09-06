<?php

namespace yondbee\FacebookMessenger\Callback;

use yondbee\FacebookMessenger\Helper;

class Optin
{
    public $ref;

    public $user_ref;

    /**
     * Delivered constructor.
     * @param $optin
     */
    public function __construct($optin)
    {
        $this->ref = Helper::array_find($optin, 'ref');
        $this->user_ref = Helper::array_find($optin, 'user_ref');
    }
}
