<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(private readonly ContactService $contactService)
    {
    }

    public function getAllContactsPage()
    {
        return $this->contactService->getAllContactsPage();
    }

    public function getAllContactsInvitePage()
    {
        return $this->contactService->getAllContactsInvitePage();
    }


    public function addContact(User $contactUser)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $contactUser->id) {
            return response()->json([
                'message' => 'Can\'t add yourself to contacts'
            ], 400);
        }

        $result = $this->contactService->addContact($currentUser, $contactUser);

        return $result
            ? response()->json(['message' => 'Contact request sent'], 201)
            : response()->json(['message' => 'Contact already exists'], 400);
    }

    public function acceptContact(User $contactUser)
    {
        $currentUser = Auth::user();

        $result = $this->contactService->acceptContact($currentUser, $contactUser);

        return $result
            ? response()->json(['message' => 'Contact confirmed'], 200)
            : response()->json(['message' => 'Contact verification failed'], 400);
    }

    public function removeContact(User $contactUser)
    {
        $currentUser = Auth::user();

        $result = $this->contactService->removeContact($currentUser, $contactUser);

        return $result
            ? response()->json(['message' => 'Contact deleted'], 200)
            : response()->json(['message' => 'Unable to delete contact'], 400);
    }

    public function getContacts()
    {
        return $this->contactService->getContacts();
    }

    public function getPendingContacts()
    {
        return $this->contactService->getPendingContacts();
    }
}
