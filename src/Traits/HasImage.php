<?php

namespace yondbee\FacebookMessenger\Traits;


trait HasImage
{
    public $image;

    public function image($image)
    {
        $this->image = $image;
        return $this;
    }
}
