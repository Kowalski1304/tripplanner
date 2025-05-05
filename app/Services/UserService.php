<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Broadcast;

class UserService
{
    public function __construct(
        private readonly ContactService $contactService,
        private readonly FileService $fileService
    ) {
    }

    public function getAllUsersPage(): Response
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->with('file')
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $this->fileService->getAvatarUrl($user->file)
                ];
            });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'broadcastChannel' => 'users',
        ]);
    }

    public function show(User $contactUser): Response
    {
        $currentUser = Auth::user();
        $isСontact = $this->contactService->hasContact($currentUser, $contactUser);
        // TODO refactor isMutualContact
        $isMutualContact = $this->contactService->hasMutualContact($currentUser, $contactUser);
        $isSameUser = $currentUser->getKey() === $contactUser->getKey();
        
        $fileUrl = null;
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
                'avatar' => $this->fileService->getAvatarUrl($contactUser->file),
            ],
            'isContact' => $isСontact,
            'isSameUser' => $isSameUser,
            'broadcastChannel' => 'user.' . $contactUser->id,
        ]);
    }

}
