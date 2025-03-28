<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use App\Services\API\ContactService as APIContactService;

class ContactService
{
    public function __construct(private readonly APIContactService $contactService)
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

    public function getAllContactsInvitePage()
    {

    }


    public function getContacts(): JsonResponse
    {
        return $this->contactService->getContacts();
    }

    public function getPendingContacts(): JsonResponse
    {
        return $this->contactService->getPendingContacts();
    }

    public function addContact(User $currentUser, User $contactUser): bool
    {
        return $this->contactService->addContact($currentUser, $contactUser);
    }

    public function acceptContact(User $currentUser, User $contactUser): bool
    {
        return $this->contactService->acceptContact($currentUser, $contactUser);
    }

    public function removeContact(User $currentUser, User $contactUser)
    {
        return $this->contactService->removeContact($currentUser, $contactUser);
    }

    public function hasContact(User $currentUser, User $contactUser): Contact
    {
        return $this->contactService->hasContact($currentUser, $contactUser);
    }
}
