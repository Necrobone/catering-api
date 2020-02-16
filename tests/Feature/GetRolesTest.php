<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetRolesTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        factory(Role::class)->state('employee')->create();
        factory(Role::class)->state('user')->create();

        $response = $this->getJson(route('roles', ['api_token' => $user->api_token]));

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();

        $response = $this->getJson(route('roles', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();

        $response = $this->getJson(route('roles', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('roles'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
