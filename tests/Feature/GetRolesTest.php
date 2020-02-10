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
    public function testRolesSuccess()
    {
        $user = factory(User::class)->create([
            'role_id' => Role::ADMINISTRATOR
        ]);

        $response = $this->getJson(route('roles', ['api_token' => $user->api_token]));

        $response->assertOk();

        $response->assertJsonCount(2);
    }

    /**
     * @return void
     */
    public function testRolesEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->create([
            'role_id' => Role::EMPLOYEE
        ]);

        $response = $this->getJson(route('roles', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testRolesUserAuthorizationFail()
    {
        $user = factory(User::class)->create([
            'role_id' => Role::USER
        ]);

        $response = $this->getJson(route('roles', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testRolesFail()
    {
        $response = $this->getJson(route('roles'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
