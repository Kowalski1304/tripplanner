<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'creator_id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function files(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    //TODO перенести звязок між юзером та табл team_user
}
