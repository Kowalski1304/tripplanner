<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(private readonly ContactService $contactService)
    {
    }
    public function getAllUsersPage(): Response
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $contactUser): Response
    {
        $currentUser = Auth::user();
        $isСontact = $this->contactService->hasContact($currentUser, $contactUser);
        $mutualContact = $this->contactService->hasMutualContact($currentUser, $contactUser);
        $isSameUser = $currentUser->id === $contactUser->id;
        if ($contactUser->files && Storage::disk('private')->exists($contactUser->files->path)) {
            $fileUrl = URL::temporarySignedRoute(
                'private.file',
                now()->addMinutes(5),
                ['path' => basename($contactUser->files->path)]
            );
        }

        return Inertia::render('Users/UserPage', [
            'user' => [
                'id' => $contactUser->id,
                'name' => $contactUser->name,
                'profile' => [
                    'phone' => $mutualContact || $isSameUser ? $contactUser->profile?->phone : null,
                ],
                'files' => [
                    [
                        'id' => $contactUser->files?->id,
                        'path' => $fileUrl ?? null,
                    ]
                ],
            ],
            'isContact' => $isСontact,
            'isSameUser' => $isSameUser,
        ]);
    }

}
