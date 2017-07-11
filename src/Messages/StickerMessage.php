<?php
namespace yondbee\FacebookMessenger\Messages;

use yondbee\FacebookMessenger\Exceptions\CouldNotCreateMessage;
use yondbee\FacebookMessenger\Interfaces\MessageInterface;
use yondbee\FacebookMessenger\Traits\MessageTrait;
use JsonSerializable;

class StickerMessage implements MessageInterface, JsonSerializable
{
    use MessageTrait;

    public $sticker;

    public function sticker($sticker)
    {
        $this->sticker = $sticker;
        return $this;
    }

    public function __construct($sticker = null)
    {
        $this->sticker($sticker);
    }

    public static function create($sticker = null)
    {
        return new static($sticker);
    }

    public function toArray()
    {
        $this->checkRecipient();

        if (is_null($this->sticker)) {
            throw CouldNotCreateMessage::noStickerDefined();
        }

        return [
            'recipient' => [
                'id' => $this->recipient_id
            ],
            'sticker_id' => $this->sticker
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
