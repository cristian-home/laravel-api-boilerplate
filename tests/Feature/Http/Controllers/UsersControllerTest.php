<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Events\NewUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use App\Notifications\NewUserNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $this->getJson(route('users.index'))->assertUnauthorized();

        $this->actingAs(User::factory()->create(), 'api');

        User::factory()->count(3)->create();

        $response = $this->getJson(route('users.index'));

        $response->assertOk();
        $response->assertJsonStructure(
            [
                "data" => [
                    [
                        "id",
                        "email",
                        "email_verified_at",
                    ]
                ]
            ]
        );
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
        $this->actingAs(User::factory()->create(), 'api');

        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

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
    }

    /**
     * @test
     */
    public function store_fire_event_new_user()
    {
        $this->actingAs(User::factory()->create(), 'api');

        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        Event::fake();
        Notification::fake();

        $response = $this->postJson(route('users.store'), [
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user = User::where('email', $email)->first();

        Event::assertDispatched(NewUser::class, function ($event) use ($user) {
            return $event->user->is($user);
        });
    }

    /**
     * @test
     */
    public function new_user_event_send_new_user_notification()
    {
        Notification::fake();

        $user = User::factory()->create();

        $event = new NewUser($user);

        event($event);

        Notification::assertSentTo($user, NewUserNotification::class, function ($notification) use ($user) {
            return $notification->user->is($user);
        });
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $this->actingAs(User::factory()->create(), 'api');

        $user = User::factory()->create();

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertJsonStructure(
            [
                "data" => [
                    "id",
                    "email",
                    "email_verified_at"
                ]
            ]
        );
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
        $this->actingAs(User::factory()->create(), 'api');

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
        $this->actingAs(User::factory()->create(), 'api');

        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', $user));

        $response->assertNoContent();

        $this->assertDeleted($user);
    }
}
