<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PollOption extends Model
{


    public function files(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
