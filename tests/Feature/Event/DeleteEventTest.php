<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteEventTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        $event = factory(Event::class)->create();

        $response = $this->deleteJson(
            route('events.destroy', ['api_token' => $user->api_token, 'event' => $event->id])
        );

        $response->assertOk();
        $this->assertSoftDeleted('events', $event->toArray());
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $event = factory(Event::class)->create();

        $response = $this->deleteJson(
            route('events.destroy', ['api_token' => $user->api_token, 'event' => $event->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('events', $event->toArray());
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $event = factory(Event::class)->create();

        $response = $this->deleteJson(
            route('events.destroy', ['api_token' => $user->api_token, 'event' => $event->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('events', $event->toArray());
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->deleteJson(
            route('events.destroy', ['api_token' => $user->api_token, 'event' => 0])
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Event] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->deleteJson(route('events.destroy', ['event' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
