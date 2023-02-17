<?php

namespace AmRo045\LaravelHashId\Tests\Models;

use AmRo045\LaravelHashId\Traits\HasHashId;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasHashId;

    protected $appends = ['hash_id'];
}