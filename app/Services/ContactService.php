<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactService
{
    public function __construct()
    {
    }

    public function getAllContactsPage()
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->with('profile')
            ->get();

        return Inertia::render('Contacts/Index', [
            'users' => $users,
        ]);
    }
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
                'contact_id' => $contactUser->getKey(),
                'status' => 'pending'
            ]);
        }

        return false;
    }

    public function removeContact(User $currentUser, User $contactUser): bool
    {
        return Contact::query()
            ->where('user_id', $currentUser->getKey())
            ->where('contact_id', $contactUser->getKey())
            ->delete();
    }

    public function hasContact(User $currentUser, User $contactUser): bool
    {
        return Contact::query()
            ->where('user_id', $currentUser->getKey())
            ->where('contact_id', $contactUser->getKey())
            ->exists();
    }

    public function hasMutualContact(User $currentUser, User $contactUser): bool
    {
        return Contact::query()
                ->where('user_id', $currentUser->getKey())
                ->where('contact_id', $contactUser->getKey())
                ->exists() &&
            Contact::query()
                ->where('user_id', $contactUser->getKey())
                ->where('contact_id', $currentUser->getKey())
                ->exists();
    }
}
