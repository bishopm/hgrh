<?php

namespace Bishopm\Hgrh\Models;

use Bishopm\Hgrh\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Taggable;
    
    public $table = 'settings';
    protected $guarded = ['id'];

    public static function getValue(string $slug, $default = null)
    {
        return static::where('setting', $slug)->value('value') ?? $default;
    }

}
