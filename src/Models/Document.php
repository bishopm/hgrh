<?php

namespace Bishopm\Hgrh\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Document extends Model
{
    
    public $table = 'documents';
    protected $guarded = ['id'];

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

}
