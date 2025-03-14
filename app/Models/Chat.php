<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_1');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_2');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }
}
