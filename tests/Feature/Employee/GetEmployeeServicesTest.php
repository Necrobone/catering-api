<?php

namespace Tests\Feature;

use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetEmployeeServicesTest extends TestCase
{
    /**
     * @return void
     */
    public function testEmployeeSuccess()
    {
        $user = factory(User::class)->state('employee')->create();

        $response = $this->getJson(
            route('employees.services', ['api_token' => $user->api_token, 'employee' => $user->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testUserSuccess()
    {
        $user = factory(User::class)->state('user')->create();

        $response = $this->getJson(
            route('employees.services', ['api_token' => $user->api_token, 'employee' => $user->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testAdminAuthorizationFail()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->getJson(
            route('employees.services', ['api_token' => $user->api_token, 'employee' => $user->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $employee = factory(User::class)->state('user')->create();

        $response = $this->getJson(
            route('employees.services', ['api_token' => $user->api_token, 'employee' => $employee->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $employee = factory(User::class)->state('employee')->create();

        $response = $this->getJson(
            route('employees.services', ['api_token' => $user->api_token, 'employee' => $employee->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('employees.services', ['employee' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
