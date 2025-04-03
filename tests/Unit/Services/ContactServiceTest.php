<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use App\Services\ContactService;

beforeEach(function () {
    $this->contactService = new ContactService();
});

test('add contact', function () {
    $user_1 = User::factory()->create();
    $user_2 = User::factory()->create();

    actingAs($user_1)->post("/api/contacts/add/$user_2->id")->assertStatus(200);

    $this->assertDatabaseHas('contacts', [
        'user_id' => $user_1->id,
        'contact_id' => $user_2->id,
    ]);
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
