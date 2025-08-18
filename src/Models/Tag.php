<?php

namespace Bishopm\Hgrh\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    public $table = 'tags';
    protected $guarded = ['id'];
    public $timestamps = false;

    public static function unslug($slug){
        return ucwords(str_replace('-', ' ', $slug));
    }

    public function documents(): MorphToMany
    {
        return $this->morphedByMany(Document::class, 'taggable');
    }
}
