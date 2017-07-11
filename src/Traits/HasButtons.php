<?php
namespace yondbee\FacebookMessenger\Traits;

trait HasButtons
{
    //for anything that has a set of buttons
    public $buttons = [];

    public function buttons(array $buttons)
    {
        $this->buttons = $buttons;
        return $this;
    }

}
