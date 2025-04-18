<?php

use App\Models\Contact;
use App\Models\File;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\actingAs;

describe('route user index', function () {
    test('get all users page', function () {
        $user_2 = User::factory()->create();

        actingAs($user_2);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
    });

    test('show all users page with users authorised', function () {
        $users = User::factory()->count(10)->create();

        actingAs($users->first());

        $this->get(route('users.index'))
            ->assertOk()->assertInertia(fn(Assert $page) => $page
                ->component('Users/Index')
                ->has('users', 10)
            );
    });

    test('redirect to login page', function () {
        $this->get(route('users.index'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    });

    test('redirect to verification page', function () {
        $user = User::factory()->create(['email_verified_at' => null]);

        actingAs($user);

        $this->get(route('users.index'))
            ->assertStatus(302)
            ->assertRedirect(route('verification.notice'));
    });

});

describe('route user show', function () {
    test('show user page', function () {
        Storage::fake('private');
        Storage::disk('private')->put('team_files/test_file.txt', 'Test content');

        $authUser = User::factory()->create();

        $userToShow = User::factory()->create(['name' => 'Test User']);

        Profile::factory()->create([
            'user_id' => $userToShow->getKey(),
            'phone' => '+380123456789',
        ]);

        File::factory()->create([
            'fileable_id' => $userToShow->getKey(),
            'path' => 'team_files/test_file.txt',
        ]);

        Contact::factory()->create([
            'user_id' => $authUser->getKey(),
            'contact_id' => $userToShow->getKey(),
        ]);

        Contact::factory()->create([
            'user_id' => $userToShow->getKey(),
            'contact_id' => $authUser->getKey(),
        ]);

        actingAs($authUser);

        $this->get(route('users.show', ['user' => $userToShow->getKey()]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/UserPage')
            ->has('user', fn(Assert $page) => $page
                ->where('id', $userToShow->getKey())
                ->where('name', 'Test User')
                ->where('profile.phone', '+380123456789')
                ->where('files.0.path', 'http://localhost/team_files/test_file.txt')
            )
        );
    });
    test('show user page without files', function () {
        $authUser = User::factory()->create();

        $userToShow = User::factory()->create(['name' => 'Test User']);

        Profile::factory()->create([
            'user_id' => $userToShow->getKey(),
            'phone' => '+380123456789',
        ]);

        Contact::factory()->create([
            'user_id' => $authUser->getKey(),
            'contact_id' => $userToShow->getKey(),
        ]);

        Contact::factory()->create([
            'user_id' => $userToShow->getKey(),
            'contact_id' => $authUser->getKey(),
        ]);

        actingAs($authUser);

        $this->get(route('users.show', ['user' => $userToShow->getKey()]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/UserPage')
            ->has('user', fn(Assert $page) => $page
                ->where('id', $userToShow->getKey())
                ->where('name', 'Test User')
                ->where('profile.phone', '+380123456789')
                ->where('files.0.path', null)
            )
        );
    });
    test('show user page without contact', function () {
        $authUser = User::factory()->create();

        $userToShow = User::factory()->create(['name' => 'Test User']);

        Profile::factory()->create([
            'user_id' => $userToShow->getKey(),
            'phone' => '+380123456789',
        ]);

        Contact::factory()->create([
            'user_id' => $authUser->getKey(),
            'contact_id' => $userToShow->getKey(),
        ]);

        Contact::factory()->create([
            'user_id' => $userToShow->getKey(),
            'contact_id' => $authUser->getKey(),
        ]);

        actingAs($authUser);

        $this->get(route('users.show', ['user' => $userToShow->getKey()]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/UserPage')
            ->has('user', fn(Assert $page) => $page
                ->where('id', $userToShow->getKey())
                ->where('name', 'Test User')
                ->where('profile.phone', '+380123456789')
                ->where('files.0.path', null)
            )
        );
    });
    test('show user page without phone and without files', function () {
        $authUser = User::factory()->create();

        $userToShow = User::factory()->create(['name' => 'Test User']);

        Profile::factory()->create([
            'user_id' => $userToShow->getKey(),
            'phone' => null,
        ]);

        Contact::factory()->create([
            'user_id' => $authUser->getKey(),
            'contact_id' => $userToShow->getKey(),
        ]);

        Contact::factory()->create([
            'user_id' => $userToShow->getKey(),
            'contact_id' => $authUser->getKey(),
        ]);

        actingAs($authUser);

        $this->get(route('users.show', ['user' => $userToShow->getKey()]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/UserPage')
            ->has('user', fn(Assert $page) => $page
                ->where('id', $userToShow->getKey())
                ->where('name', 'Test User')
                ->where('profile.phone', null)
                ->where('files.0.path', null)
            )
        );
    });
    test('show user page without phone and files', function () {
        Storage::fake('private');
        Storage::disk('private')->put('team_files/test_file.txt', 'Test content');

        $authUser = User::factory()->create();

        $userToShow = User::factory()->create(['name' => 'Test User']);

        Profile::factory()->create([
            'user_id' => $userToShow->getKey(),
            'phone' => null,
        ]);

        File::factory()->create([
            'fileable_id' => $userToShow->getKey(),
            'path' => 'team_files/test_file.txt',
        ]);

        Contact::factory()->create([
            'user_id' => $authUser->getKey(),
            'contact_id' => $userToShow->getKey(),
        ]);

        Contact::factory()->create([
            'user_id' => $userToShow->getKey(),
            'contact_id' => $authUser->getKey(),
        ]);

        actingAs($authUser);

        $this->get(route('users.show', ['user' => $userToShow->getKey()]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/UserPage')
            ->has('user', fn(Assert $page) => $page
                ->where('id', $userToShow->getKey())
                ->where('name', 'Test User')
                ->where('profile.phone', null)
                ->where('files.0.path', 'http://localhost/team_files/test_file.txt')
            )
        );
    });
    test('show user page has contact', function () {
        $authUser = User::factory()->create();

        $userToShow = User::factory()->create(['name' => 'Test User']);

        Contact::factory()->create([
            'user_id' => $authUser->getKey(),
            'contact_id' => $userToShow->getKey(),
        ]);

        actingAs($authUser);

        $this->get(route('users.show', ['user' => $userToShow->getKey()]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/UserPage')
            ->has('user', fn(Assert $page) => $page
                ->where('id', $userToShow->getKey())
                ->where('name', 'Test User')
                ->where('profile.phone', null)
                ->where('files.0.path', null)
            )
            ->has('isContact', true)
//            ->has('isSameUser', false)
        );
    });
});
