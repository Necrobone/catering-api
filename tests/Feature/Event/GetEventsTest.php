<?php

namespace Tests\Feature;

use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetEventsTest extends TestCase
{
    /**
     * @return void
     */
    public function testAdminSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->getJson(route('events.index', ['api_token' => $user->api_token]));

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testUserSuccess()
    {
        $user = factory(User::class)->state('user')->create();

        $response = $this->getJson(route('events.index', ['api_token' => $user->api_token]));

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();

        $response = $this->getJson(route('events.index', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('events.index'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
