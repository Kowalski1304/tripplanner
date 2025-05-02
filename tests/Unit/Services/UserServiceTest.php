<?php

use App\Models\Contact;
use App\Models\Profile;
use App\Services\ContactService;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Arr;
use Inertia\Response;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->userService = new UserService(new ContactService());
});

test('get all users page', function () {
    $users1 = User::factory()->create();
    $users2 = User::factory()->create();

    $resultUsers = [
        [
            'id' => $users1->id,
            'name' => $users1->name,
        ],
        [
            'id' => $users2->id,
            'name' => $users2->name,
        ],
    ];

    $result = $this->userService->getAllUsersPage();

    $resultData = $result->toResponse(request())->getOriginalContent();

    expect($result)->toBeInstanceOf(Response::class)
        ->and(Arr::get($resultData->getData(), 'page.component'))->toBe('Users/Index')
        ->and(Arr::get($resultData->getData(), 'page.props'))->toBe(['users' => $resultUsers]);
});

test('user can view own profile page successfully', function () {
    $user = User::factory()
        ->has(Profile::factory())
        ->create();
    actingAs($user);

    $resultProfile = [
        'user' =>
            [
                'id' => $user->id,
                'name' => $user->name,
                'profile' => [
                    'phone' => $user->profile->phone,
                ]
            ],
                'isContact' => false,
                'isSameUser' => true,
    ];

    $result = $this->userService->show($user);

    $resultData = $result->toResponse(request())->getOriginalContent();

    expect($result)->toBeInstanceOf(Response::class)
        ->and(Arr::get($resultData->getData(), 'page.component'))->toBe('Users/UserPage')
        ->and(Arr::get($resultData->getData(), 'page.props'))->toBe($resultProfile);
});
test('user can view contact profile page successfully', function () {
    $user = User::factory()
        ->has(Profile::factory())
        ->create();

    $contactUser = User::factory()
        ->has(Profile::factory())
        ->create();

    Contact::create([
        'user_id' => $user->id,
        'contact_id' => $contactUser->id,
    ]);

    actingAs($user);

    $resultProfile = [
        'user' =>
            [
                'id' => $contactUser->id,
                'name' => $contactUser->name,
                'profile' => [
                    'phone' => null,
                ]
            ],
        'isContact' => true,
        'isSameUser' => false,
    ];

    $result = $this->userService->show($contactUser);

    $resultData = $result->toResponse(request())->getOriginalContent();

    expect($result)->toBeInstanceOf(Response::class)
        ->and(Arr::get($resultData->getData(), 'page.component'))->toBe('Users/UserPage')
        ->and(Arr::get($resultData->getData(), 'page.props'))->toBe($resultProfile);
});
test('user can view mutual contact profile page successfullyRetry', function () {
    $user = User::factory()
        ->has(Profile::factory())
        ->create();

    $contactUser = User::factory()
        ->has(Profile::factory())
        ->create();

    Contact::create([
        'user_id' => $user->id,
        'contact_id' => $contactUser->id,
    ]);

    Contact::create([
        'user_id' => $contactUser->id,
        'contact_id' => $user->id,
    ]);

    actingAs($user);

    $resultProfile = [
        'user' =>
            [
                'id' => $contactUser->id,
                'name' => $contactUser->name,
                'profile' => [
                    'phone' => $contactUser->profile->phone,
                ]
            ],
        'isContact' => true,
        'isSameUser' => false,
    ];

    $result = $this->userService->show($contactUser);

    $resultData = $result->toResponse(request())->getOriginalContent();

    expect($result)->toBeInstanceOf(Response::class)
        ->and(Arr::get($resultData->getData(), 'page.component'))->toBe('Users/UserPage')
        ->and(Arr::get($resultData->getData(), 'page.props'))->toBe($resultProfile);
});
test('user can view non-contact profile page successfully', function () {
    $user = User::factory()
        ->has(Profile::factory())
        ->create();

    $contactUser = User::factory()
        ->has(Profile::factory())
        ->create();

    Contact::create([
        'user_id' => $contactUser->id,
        'contact_id' => $user->id,
    ]);

    actingAs($user);

    $resultProfile = [
        'user' =>
            [
                'id' => $contactUser->id,
                'name' => $contactUser->name,
                'profile' => [
                    'phone' => null,
                ]
            ],
        'isContact' => false,
        'isSameUser' => false,
    ];

    $result = $this->userService->show($contactUser);

    $resultData = $result->toResponse(request())->getOriginalContent();

    expect($result)->toBeInstanceOf(Response::class)
        ->and(Arr::get($resultData->getData(), 'page.component'))->toBe('Users/UserPage')
        ->and(Arr::get($resultData->getData(), 'page.props'))->toBe($resultProfile);
});
