<?php

namespace App\Services;

use App\Models\User;
use App\Services\API\ContactService as APIContactService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserService
{
    public function __construct(private readonly APIContactService $contactService)
    {
    }
    public function getAllUsersPage(): Response
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->with('profile')
            ->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $contactUser): Response
    {
        $currentUser = Auth::user();
        $is_contact = $this->contactService->hasContact($currentUser, $contactUser);

        return Inertia::render('Users/UserPage', [
            'user' => $currentUser,
            'isContact' => $is_contact,
        ]);
    }

}
