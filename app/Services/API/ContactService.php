<?php

namespace App\Services\API;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactService
{

    public function removeContact(User $currentUser, User $contactUser)
    {
        return Contact::query()
            ->where('user_id', $currentUser->id)
            ->where('contact_id', $contactUser->id)
            ->delete();
    }

    public function hasContact(User $currentUser, User $contactUser): bool
    {
        return Contact::query()
            ->where('user_id', $currentUser->id)
            ->where('contact_id', $contactUser->id)
            ->exists();
    }
}
