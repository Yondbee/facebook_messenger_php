<?php
namespace yondbee\FacebookMessenger\Messages;

use yondbee\FacebookMessenger\Interfaces\MessageInterface;
use yondbee\FacebookMessenger\MessengerUtils;
use yondbee\FacebookMessenger\Traits\HasText;
use yondbee\FacebookMessenger\Traits\MessageTrait;

class TextMessage implements MessageInterface, \JsonSerializable
{
    use MessageTrait, HasText;

    public function __construct($text = null)
    {
        if (!is_null($text)) {
            $this->text($text);
        }
    }

    public static function create($text = null)
    {
        return new static($text);
    }

    public function toArray()
    {
        $this->checkRecipient();

        return [
            'recipient' => $this->getRecipientObject(),
            'message' => [
                'text' => MessengerUtils::checkStringLengthAndEncoding($this->text,320,'UTF-8')
            ]
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
