<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Message extends Model
{
    protected $fillable = ['chat_id', 'sender_id', 'recipient_id', 'content'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    //TODO  додати json запису хто прочитав
    public function readBy(): BelongsToMany
    {

    }
}
