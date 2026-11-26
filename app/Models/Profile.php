<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public static function current(): self
    {
        return static::query()->first() ?? new static();
    }
}
