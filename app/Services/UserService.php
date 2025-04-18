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
        $isSameUser = $currentUser->getKey() === $contactUser->getKey();
        if ($contactUser->file && Storage::disk('private')->exists($contactUser->file->path)) {
            $fileUrl = asset($contactUser->file->path);
        }

        return Inertia::render('Users/UserPage', [
            'user' => [
                'id' => $contactUser->getKey(),
                'name' => $contactUser->name,
                'profile' => [
                    'phone' => $contactUser->profile?->phone,
                ],
                'files' => [
                    [
                        'id' => $contactUser->files?->getKey(),
                        'path' => $fileUrl ?? null,
                    ]
                ],
            ],
            'isContact' => $isСontact,
            'isSameUser' => $isSameUser,
        ]);
    }

}
