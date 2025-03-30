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
        $apiToken = request()->session()->get('api_token');

        if (!$apiToken) {
            $currentUser = Auth::user();
            $token = $currentUser->createToken('api_token');
            $apiToken = $token->plainTextToken;
            request()->session()->put('api_token', $apiToken);
        }

        return Inertia::render('Users/UserPage', [
            'user' => $contactUser,
            'apiToken' => $apiToken,
            'isContact' => $is_contact,
        ]);
    }

}
