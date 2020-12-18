<?php

namespace Tests\Feature\Http\Controllers;

use App\Events\NewUser;
use App\Models\User;
use App\Notification\NewUserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UsersController
 */
class UsersControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $users = User::factory()->count(3)->create();

        $response = $this->getJson(route('users.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UsersController::class,
            'store',
            \App\Http\Requests\UserStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        Notification::fake();
        Event::fake();

        $response = $this->postJson(route('users.store'), [
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $response->assertOk();

        $users = User::query()
            ->where('email', $email)
            ->get();

        $this->assertCount(1, $users);

        $user = $users->first();

        $this->assertTrue(Hash::check($password, $user->password));

        Notification::assertSentTo($user, NewUserNotification::class, function ($notification) use ($user) {
            return $notification->user->is($user);
        });

        Event::assertDispatched(NewUser::class, function ($event) use ($user) {
            return $event->user->is($user);
        });
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UsersController::class,
            'update',
            \App\Http\Requests\UserUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $user = User::factory()->create();
        $email = $this->faker->safeEmail;

        $response = $this->put(route('users.update', $user), [
            'email' => $email,
        ]);

        $user->refresh();

        $this->assertEquals($email, $user->email);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', $user));

        $response->assertNoContent();

        $this->assertDeleted($user);
    }
}
