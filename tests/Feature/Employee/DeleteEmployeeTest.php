<?php

namespace Tests\Feature;

use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteEmployeeTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        $employee = factory(User::class)->state('employee')->create();

        $response = $this->deleteJson(
            route('employees.destroy', ['api_token' => $user->api_token, 'employee' => $employee->id])
        );

        $response->assertOk();
        $this->assertSoftDeleted('users', $employee->toArray());
    }

    /**
     * @return void
     */
    public function testAdminAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $employee = factory(User::class)->state('user')->create();

        $response = $this->deleteJson(
            route('employees.destroy', ['api_token' => $user->api_token, 'employee' => $employee->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('users', $employee->toArray());
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $employee = factory(User::class)->state('administrator')->create();

        $response = $this->deleteJson(
            route('employees.destroy', ['api_token' => $user->api_token, 'employee' => $employee->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('users', $employee->toArray());
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $employee = factory(User::class)->state('administrator')->create();

        $response = $this->deleteJson(
            route('employees.destroy', ['api_token' => $user->api_token, 'employee' => $employee->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('users', $employee->toArray());
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->deleteJson(
            route('employees.destroy', ['api_token' => $user->api_token, 'employee' => 0])
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\User] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->deleteJson(route('employees.destroy', ['employee' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
