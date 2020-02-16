<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostEventTest extends TestCase
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = factory(Event::class)->make();
        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('events.store', ['api_token' => $this->user->api_token]),
            $this->event->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->postJson(
            route('events.store', ['api_token' => $user->api_token]),
            $this->event->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->postJson(
            route('events.store', ['api_token' => $user->api_token]),
            $this->event->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->event->name = null;

        $response = $this->postJson(
            route('events.store', ['api_token' => $this->user->api_token]),
            $this->event->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseMissing('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->event->name = true;

        $response = $this->postJson(
            route('events.store', ['api_token' => $this->user->api_token]),
            $this->event->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseMissing('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->event->name = Str::random(256);

        $response = $this->postJson(
            route('events.store', ['api_token' => $this->user->api_token]),
            $this->event->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseMissing('events', $this->event->toArray());
    }
}
