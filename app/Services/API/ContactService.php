<?php

namespace App\Services\API;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactService
{
    public function getContacts(): JsonResponse
    {
        $currentUser = Auth::user();
        $contacts = $currentUser->contacts()->with('contactUser')->get();

        return response()->json($contacts);
    }

    public function getPendingContacts(): JsonResponse
    {
        $currentUser = Auth::user();
        $pendingContacts = $currentUser->pendingContacts()->with('user')->get();

        return response()->json($pendingContacts);
    }

    public function addContact(User $currentUser, User $contactUser): bool
    {
        if (!$this->hasContact($contactUser, $contactUser)) {
            return $currentUser->contacts()->create([
                'contact_id' => $contactUser->id,
                'status' => 'pending'
            ]);
        }

        return false;
    }

    public function acceptContact(User $currentUser, User $contactUser): bool
    {
        $contact = Contact::where('user_id', $contactUser->id)
            ->where('contact_id', $currentUser->id)
            ->first();

        if ($contact) {
            $contact->update(['status' => 'accepted']);
            return true;
        }

        return false;
    }

    public function removeContact(User $currentUser, User $contactUser)
    {
        return Contact::where(function($query) use ($currentUser, $contactUser) {
            $query->where('user_id', $contactUser->id)
                ->where('contact_id', $currentUser->id);
        })->orWhere(function($query) use ($currentUser, $contactUser) {
            $query->where('user_id', $currentUser->id)
                ->where('contact_id', $contactUser->id);
        })->delete();
    }

    public function hasContact(User $currentUser, User $contactUser): bool
    {
        return Contact::where(function($query) use ($currentUser, $contactUser) {
            $query->where('user_id', $contactUser->id)
                ->where('contact_id', $currentUser->id);
        })->orWhere(function($query) use ($currentUser, $contactUser) {
            $query->where('user_id', $currentUser->id)
                ->where('contact_id', $contactUser->id);
        })->exists();
    }
}
