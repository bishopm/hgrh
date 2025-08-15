<?php

namespace Bishopm\Hgrh\Models;

use Bishopm\Hgrh\Traits\Taggable;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use Taggable;
    
    public $table = 'documents';
    protected $guarded = ['id'];

}
