<?php

namespace yondbee\FacebookMessenger\Traits;


trait HasText
{
    public $text;

    public function text($text)
    {
        $this->text = $text;
        return $this;
    }
}
