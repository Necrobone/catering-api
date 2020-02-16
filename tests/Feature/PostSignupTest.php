<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostSignupTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->make();

        $this->user->makeVisible('password');
        $this->user->password = 'Madrid4$';
        $this->user->password_confirmation = 'Madrid4$';
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationRequired()
    {
        $this->user->first_name = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationInvalid()
    {
        $this->user->first_name = true;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testFirstNameValidationTooLong()
    {
        $this->user->first_name = Str::random(256);

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'FIRST_NAME_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationRequired()
    {
        $this->user->last_name = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationInvalid()
    {
        $this->user->last_name = true;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testLastNameValidationTooLong()
    {
        $this->user->last_name = Str::random(256);

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'LAST_NAME_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationRequired()
    {
        $this->user->email = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationInvalid()
    {
        $this->user->email = 'ImNotAnEmail';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationTooLong()
    {
        $this->user->email = 'random@' . Str::random(256);

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testEmailValidationExists()
    {
        $user = factory(User::class)->state('administrator')->create();
        $this->user->email = $user->email;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_EXISTS');
        $this->assertDatabaseHas('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationRequired()
    {
        $this->user->password = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_REQUIRED');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationUnmatched()
    {
        $this->user->password = 'wrongpassword';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_UNMATCHED');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationInvalid()
    {
        $this->user->password = 'wrongpassword';
        $this->user->password_confirmation = 'wrongpassword';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_INVALID');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooShort()
    {
        $this->user->password = '$h0Rt';
        $this->user->password_confirmation = '$h0Rt';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_SHORT');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooLong()
    {
        $this->user->password = 'L0&g' . Str::random(256);
        $this->user->password_confirmation = $this->user->password;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_LONG');
        $this->assertDatabaseMissing('users', ['email' => $this->user->email]);
    }
}
