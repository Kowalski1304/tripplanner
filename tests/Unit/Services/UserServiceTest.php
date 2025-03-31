<?php

use App\Services\UserService;
use App\Models\User;

beforeEach(function () {
    $this->userService = new UserService();
});

it('retrieves a user by ID', function () {
    $data = ['name' => 'Charlie', 'email' => 'charlie@example.com'];
    $user = $this->userService->create($data);
    $foundUser = $this->userService->get($user->id);

    expect($foundUser)->toBeInstanceOf(User::class)
        ->and($foundUser->id)->toBe($user->id);
});

