<?php
/**
 * Created by PhpStorm.
 * User: asanta
 * Date: 7/18/17
 * Time: 6:34 PM
 */

namespace yondbee\FacebookMessenger;


class MessengerPersistentMenu
{

    public $locale;

    public $composer_input_disabled;

    public $call_to_actions;

    static public function createMenu($locale = 'default', $composer_input_disabled = false)
    {
        $o = new self();

        return $o->setLocale($locale)
                 ->setComposerDisabled($composer_input_disabled);
    }

    public function setComposerDisabled($v)
    {
        $this->composer_input_disabled = $v;
        return $this;
    }

    public function setLocale($v)
    {
        $this->locale = $v;
        return $this;
    }

    public function addItem(MessengerPersistentMenuItem $i)
    {
        // max depth 5
        if (count($this->call_to_actions) == 5)
            return false;

        $this->call_to_actions = $this->call_to_actions ?: [];
        $this->call_to_actions[] = $i;

        return $this;
    }

    public function setItems($items)
    {
        // max depth 5
        if (count($items) > 5)
            return false;

        $this->call_to_actions = $items;

        return $this;
    }

    public function toArray()
    {
        $json = [
            'locale' => $this->locale,
            'call_to_actions'   => array_map(function ($i) { return $i->toArray(); }, $this->call_to_actions)
        ];

        if ($this->composer_input_disabled)
            $json['composer_input_disabled'] = true;

        return $json;
    }
}
