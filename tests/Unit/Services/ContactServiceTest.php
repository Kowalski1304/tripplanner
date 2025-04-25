<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use function Pest\Laravel\actingAs;
use App\Services\ContactService;

beforeEach(function () {
    $this->contactService = new ContactService();
});

test('get all contacts page', function () {
    $users = User::factory()->count(2)->create();

    $result = $this->contactService->getAllContactsPage();

    $resultData = $result->toResponse(request())->getOriginalContent();

    expect($result)->toBeInstanceOf(Response::class)
        ->and(Arr::get($resultData->getData(), 'page.component'))->toBe('Contacts/Index')
        ->and(Arr::get($resultData->getData(), 'page.props'))->toBe($users);
});

test('doodling request add contact', function () {
    $user_1 = User::factory()->create();
    $user_2 = User::factory()->create();

    actingAs($user_1);

    $this->postJson(route('contacts.addContact'), ['contact_user_id' => $user_2->getKey()]);

    $response = $this->postJson(route('contacts.addContact'), ['contact_user_id' => $user_2->getKey()]);

    $response->assertStatus(400);
});

test('add yourself as a contact', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test1@example.com',
    ]);
    actingAs($user);

    $response = $this->postJson(route('contacts.addContact'), ['contact_user_id' => $user->getKey()]);

    $response->assertStatus(400);
});

describe('addContact function', function () {
    test('add contact successfully', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        $result = $this->contactService->addContact($currentUser, $contactUser);
        expect($result)->toBeInstanceOf(Contact::class)
            ->and($result->user_id)->toBe($currentUser->getKey())
            ->and($result->contact_id)->toBe($contactUser->getKey());
    });
    test('contact to already exists', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $currentUser->getKey(),
            'contact_id' => $contactUser->getKey(),
        ]);

        $result = $this->contactService->addContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
});

describe('removeContact function', function () {
    test('remove contact successfully', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $currentUser->getKey(),
            'contact_id' => $contactUser->getKey(),
        ]);

        $result = $this->contactService->removeContact($currentUser, $contactUser);

        expect($result)->toBeTrue();
    });
    test('remove contact failed', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        $result = $this->contactService->removeContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
    test('remove not contact failed', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $contactUser->getKey(),
            'contact_id' => $currentUser->getKey(),
        ]);

        $result = $this->contactService->removeContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
    test('remove not without current user', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();
        $otherUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $currentUser->getKey(),
            'contact_id' => $otherUser->getKey(),
        ]);

        $result = $this->contactService->removeContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
});

describe('hasContact function', function () {
    test('has contact successfully', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $currentUser->getKey(),
            'contact_id' => $contactUser->getKey(),
        ]);

        $result = $this->contactService->hasContact($currentUser, $contactUser);

        expect($result)->toBeTrue();
    });
    test('has not contact failed', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        $result = $this->contactService->hasContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
    test('has not contact without current user', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $contactUser->getKey(),
            'contact_id' => $currentUser->getKey(),
        ]);

        $result = $this->contactService->hasContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
    test('has not contact without contact user', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();
        $otherUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $contactUser->getKey(),
            'contact_id' => $otherUser->getKey(),
        ]);

        $result = $this->contactService->hasContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
});

describe('hasMutualContact function', function () {
    test('has mutual contact successfully', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $currentUser->getKey(),
            'contact_id' => $contactUser->getKey(),
        ]);
        Contact::factory()->create([
            'user_id' => $contactUser->getKey(),
            'contact_id' => $currentUser->getKey(),
        ]);

        $result = $this->contactService->hasMutualContact($currentUser, $contactUser);

        expect($result)->toBeTrue();
    });
    test('has not mutual contact for current user', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $currentUser->getKey(),
            'contact_id' => $contactUser->getKey(),
        ]);

        $result = $this->contactService->hasMutualContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
    test('has not mutual contact for contact user', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $contactUser->getKey(),
            'contact_id' => $currentUser->getKey(),
        ]);

        $result = $this->contactService->hasMutualContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
    test('has not mutual contact for contact user', function () {
        $currentUser  = User::factory()->create();
        $contactUser = User::factory()->create();

        Contact::factory()->create([
            'user_id' => $contactUser->getKey(),
            'contact_id' => $currentUser->getKey(),
        ]);

        $result = $this->contactService->hasMutualContact($currentUser, $contactUser);

        expect($result)->toBeFalse();
    });
});
