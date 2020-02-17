<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutEventTest extends TestCase
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var Event
     */
    private $updatedEvent;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = factory(Event::class)->create();
        $this->updatedEvent = factory(Event::class)->make();
        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('events.update', ['api_token' => $this->user->api_token, 'event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertOk();
        $this->assertDatabaseMissing('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->putJson(
            route('events.update', ['api_token' => $user->api_token, 'event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->putJson(
            route('events.update', ['api_token' => $user->api_token, 'event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->updatedEvent->name = null;

        $response = $this->putJson(
            route('events.update', ['api_token' => $this->user->api_token, 'event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseHas('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->updatedEvent->name = true;

        $response = $this->putJson(
            route('events.update', ['api_token' => $this->user->api_token, 'event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseHas('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->updatedEvent->name = Str::random(256);

        $response = $this->putJson(
            route('events.update', ['api_token' => $this->user->api_token, 'event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseHas('events', $this->event->toArray());
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $response = $this->putJson(
            route('events.update', ['api_token' => $this->user->api_token, 'event' => 0]),
            $this->updatedEvent->toArray()
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Event] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->putJson(
            route('events.update', ['event' => $this->event->id]),
            $this->updatedEvent->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
