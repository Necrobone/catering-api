<?php

namespace Tests\Feature;

use App\Role;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostEmployeeTest extends TestCase
{
    /**
     * @var User
     */
    private $employee;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employee = factory(User::class)->state('employee')->make();
        $this->employee->makeVisible('password');
        $this->employee->password = 'Madrid4$';
        $this->employee->role = $this->employee->role_id;

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->postJson(
            route('employees.store', ['api_token' => $user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->postJson(
            route('employees.store', ['api_token' => $user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationRequired()
    {
        $this->employee->first_name = null;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationInvalid()
    {
        $this->employee->first_name = true;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationTooLong()
    {
        $this->employee->first_name = Str::random(256);

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationRequired()
    {
        $this->employee->last_name = null;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationInvalid()
    {
        $this->employee->last_name = true;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationTooLong()
    {
        $this->employee->last_name = Str::random(256);

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationRequired()
    {
        $this->employee->email = null;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationInvalid()
    {
        $this->employee->email = 'ImNotAnEmail';

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationTooLong()
    {
        $this->employee->email = 'random@' . Str::random(256);

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationExists()
    {
        $this->employee->email = $this->user->email;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_EXISTS');
        $this->assertDatabaseHas('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationRequired()
    {
        $this->employee->password = null;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationInvalid()
    {
        $this->employee->password = 'wrongpassword';

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooShort()
    {
        $this->employee->password = '$h0Rt';

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_SHORT');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooLong()
    {
        $this->employee->password = 'L0&g' . Str::random(256);

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testRoleValidationRequired()
    {
        $this->employee->role = null;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testRoleValidationInvalid()
    {
        $this->employee->role = 'wrongrole';

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testRoleUserValidationInvalid()
    {
        $this->employee->role = Role::USER;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testRoleValidationNotFound()
    {
        $this->employee->role = 0;

        $response = $this->postJson(
            route('employees.store', ['api_token' => $this->user->api_token]),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ROLE_NOT_FOUND');
        $this->assertDatabaseMissing('users', ['email' => $this->employee->email]);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->postJson(
            route('employees.store'),
            $this->employee->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
