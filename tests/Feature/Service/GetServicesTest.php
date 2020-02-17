<?php

namespace Tests\Feature;

use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetServicesTest extends TestCase
{
    /**
     * @return void
     */
    public function testAdminSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->getJson(route('services.index', ['api_token' => $user->api_token]));

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeSuccess()
    {
        $user = factory(User::class)->state('employee')->create();

        $response = $this->getJson(route('services.index', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserSuccess()
    {
        $user = factory(User::class)->state('user')->create();

        $response = $this->getJson(route('services.index', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('services.index'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
