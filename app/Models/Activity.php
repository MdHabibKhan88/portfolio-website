<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = ['id'];

    public static function log(string $description): void
    {
        static::create(['description' => mb_substr($description, 0, 250)]);
    }
}
