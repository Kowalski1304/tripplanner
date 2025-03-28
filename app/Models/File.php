<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    protected $fillable = ['name', 'path', 'fileble_id', 'fileble_type'];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
