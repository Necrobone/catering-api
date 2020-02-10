<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetHeadquartersTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::ADMINISTRATOR
            ]
        );

        $response = $this->getJson(route('headquarters.index', ['api_token' => $user->api_token]));

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::EMPLOYEE
            ]
        );

        $response = $this->getJson(route('headquarters.index', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::USER
            ]
        );

        $response = $this->getJson(route('headquarters.index', ['api_token' => $user->api_token]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('headquarters.index'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
