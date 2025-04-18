<?php

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

    $this->postJson(route('contacts.addContact'), ['contact_user_id' => $user_2->id]);

    $response = $this->postJson(route('contacts.addContact'), ['contact_user_id' => $user_2->id]);

    $response->assertStatus(400);
});

test('add yourself as a contact', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test1@example.com',
    ]);
    actingAs($user);

    $response = $this->postJson(route('contacts.addContact'), ['contact_user_id' => $user->id]);

    $response->assertStatus(400);
});
