<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutEmployeeTest extends TestCase
{
    /**
     * @var User
     */
    private $employee;

    /**
     * @var User
     */
    private $updatedEmployee;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->updatedEmployee = factory(User::class)->state('user')->make();
        $this->updatedEmployee->makeVisible('password');
        $this->updatedEmployee->password = 'Madrid4$';
        $this->updatedEmployee->role = $this->updatedEmployee->role_id;
        $this->updatedEmployee->firstName = $this->updatedEmployee->first_name;
        $this->updatedEmployee->lastName = $this->updatedEmployee->last_name;

        $this->employee = factory(User::class)->state('employee')->create();
        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertOk();
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
        $this->assertDatabaseHas('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testProfileSuccess()
    {
        $user = factory(User::class)->state('user')->create();

        $response = $this->putJson(
            route('employees.update', ['api_token' => $user->api_token, 'employee' => $user->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertOk();
        $this->assertDatabaseMissing('users', ['email' => $user->email]);
        $this->assertDatabaseHas('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->employee->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();

        $response = $this->putJson(
            route('employees.update', ['api_token' => $user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationRequired()
    {
        $this->updatedEmployee->firstName = null;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationInvalid()
    {
        $this->updatedEmployee->firstName = true;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationTooLong()
    {
        $this->updatedEmployee->firstName = Str::random(256);

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationRequired()
    {
        $this->updatedEmployee->lastName = null;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationInvalid()
    {
        $this->updatedEmployee->lastName = true;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationTooLong()
    {
        $this->updatedEmployee->lastName = Str::random(256);

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationRequired()
    {
        $this->updatedEmployee->email = null;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationInvalid()
    {
        $this->updatedEmployee->email = 'ImNotAnEmail';

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationTooLong()
    {
        $this->updatedEmployee->email = 'random@' . Str::random(256);

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationExists()
    {
        $this->updatedEmployee->email = $this->user->email;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_EXISTS');
        $this->assertDatabaseHas('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationInvalid()
    {
        $this->updatedEmployee->password = 'wrongpassword';

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooShort()
    {
        $this->updatedEmployee->password = '$h0Rt';

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_SHORT');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooLong()
    {
        $this->updatedEmployee->password = 'L0&g' . Str::random(256);

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testRoleValidationRequired()
    {
        $this->updatedEmployee->role = null;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testRoleValidationInvalid()
    {
        $this->updatedEmployee->role = 'wrongrole';

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testRoleValidationNotFound()
    {
        $this->updatedEmployee->role = 0;

        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_NOT_FOUND');
        $this->assertDatabaseMissing('users', ['email' => $this->updatedEmployee->email]);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $response = $this->putJson(
            route('employees.update', ['api_token' => $this->user->api_token, 'employee' => 0]),
            $this->updatedEmployee->toArray()
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\User] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->putJson(
            route('employees.update', ['employee' => $this->employee->id]),
            $this->updatedEmployee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
