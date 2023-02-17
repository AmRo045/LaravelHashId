<?php

namespace AmRo045\LaravelHashId\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasHashId
{
    protected function hashId(): Attribute
    {
        $hashids = app(Hashids::class);

        return Attribute::make(
            get: fn() => $hashids->encode($this->id)
        );
    }
}
