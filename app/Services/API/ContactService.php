<?php

namespace App\Services\API;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactService
{
    public function getContacts(): JsonResponse
    {
        $currentUser = Auth::user();
        $contacts = $currentUser->contacts()->with('contactUser')->get();

        return response()->json($contacts);
    }

    public function addContact(User $currentUser, User $contactUser): bool|Contact
    {
        if (!$this->hasContact($currentUser, $contactUser)) {
            return $currentUser->contacts()->create([
                'contact_id' => $contactUser->id,
                'status' => 'pending'
            ]);
        }

        return false;
    }

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
