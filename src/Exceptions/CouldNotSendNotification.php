<?php

namespace yondbee\FacebookMessenger\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends \Exception
{
    public static function facebookResponseError(ClientException $exception)
    {
        $result = json_decode($exception->getResponse()->getBody());
        return new static("Facebook responded with an error `{$result->error->code} - {$result->error->type} {$result->error->message}`", $result->error->code, $exception);
    }

    public static function invalidFacebookToken()
    {
        return new static('The Facebook Token provided was invalid');
    }

    public static function invalidStatusCode($code)
    {
        return new static('Facebook returned an invalid status code of' . $code, $code);
    }

    public static function couldNotCommunicateWithFacebook(Exception $exception)
    {
        return new static('The communication with Facebook failed. Reason: ' . $exception->getMessage(), $exception->getCode(), $exception);
    }
}
