<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

test('get all users page', function () {
    $user_2 = User::factory()->create();

    actingAs($user_2);

    $response = $this->get(route('users.index'));

    $response->assertStatus(200);
});

