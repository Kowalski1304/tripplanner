<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PollOption extends Model
{
    protected $fillable = ['poll_id', 'option_text'];

    public function files(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
