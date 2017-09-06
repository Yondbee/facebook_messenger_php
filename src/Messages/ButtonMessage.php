<?php

namespace yondbee\FacebookMessenger\Messages;

use yondbee\FacebookMessenger\Interfaces\MessageInterface;
use yondbee\FacebookMessenger\MessengerUtils;
use yondbee\FacebookMessenger\Traits\HasButtons;
use yondbee\FacebookMessenger\Traits\HasText;
use yondbee\FacebookMessenger\Traits\MessageTrait;
use JsonSerializable;

class ButtonMessage implements MessageInterface, JsonSerializable
{
    use MessageTrait, HasText, HasButtons;

    public function __construct($text = null)
    {
        $this->text($text);
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
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'button',
                        'text' => MessengerUtils::checkStringLengthAndEncoding($this->text, 320, 'UTF-8'),
                        'buttons' => MessengerUtils::checkArraySize($this->buttons, 3)
                    ]
                ]
            ]
        ];
    }

    public
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
