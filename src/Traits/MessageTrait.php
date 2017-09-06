<?php

namespace yondbee\FacebookMessenger\Traits;


use yondbee\FacebookMessenger\Enums\NotificationType;
use yondbee\FacebookMessenger\Exceptions\CouldNotCreateMessage;

/**
 * Class MessageTrait
 * @package yondbee\FacebookMessenger\Traits
 */
trait MessageTrait
{
    /**
     * @var
     * Recipient PSID
     */
    public $recipient_id;

    /**
     * @var
     * Recipient temporary user ref (for Checkbox plugin)
     */
    public $user_ref;

    /**
     * @param $recipient_id
     * @return $this
     */
    public function to($recipient_id)
    {
        return $this->recipient_id($recipient_id);
    }

    /**
     * @param $recipient_id
     * @return $this
     */
    public function recipient_id($recipient_id)
    {
        $this->recipient_id = $recipient_id;
        return $this;
    }

    /**
     * @param $user_ref
     * @return $this
     */
    public function user_ref($user_ref)
    {
        $this->user_ref = $user_ref;
        return $this;
    }

    //TODO Check for ID or phone number
    public function checkRecipient()
    {
        if (!isset($this->recipient_id) && !isset($this->user_ref)) {
            throw CouldNotCreateMessage::noRecipientDefined();
        }
    }

    public $notification_type = NotificationType::REGULAR;

    /**
     * @param $notification_type
     * @return $this
     */
    public function notification_type($type)
    {
        $this->notification_type = $type;
        return $this;
    }

    /**
     * @var array Extra metadata to be stored into message for application info.
     */
    public $metadata = [];

    public function metadata($value)
    {
        $this->metadata = $value;
        return $this;
    }

    public function getRecipientObject()
    {
        if (!empty($this->user_ref))
            return [
                'user_ref' => $this->user_ref
            ];
        else
            return [
                'id' => $this->recipient_id
            ];
    }
}
