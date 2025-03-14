<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeamUser extends Model
{

    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }


}
