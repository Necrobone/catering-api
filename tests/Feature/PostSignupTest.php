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
        $this->user->password_confirmation = $this->user->password;
    }

    /**
     * @return void
     */
    public function testSignupSuccess()
    {
        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('users', ['email' => $this->user->email]);
    }

    /**
     * @return void
     */
    public function testSignupFirstNameValidationRequired()
    {
        $this->user->first_name = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'FIRST_NAME_REQUIRED');
    }

    /**
     * @return void
     */
    public function testSignupFirstNameValidationInvalid()
    {
        $this->user->first_name = true;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'FIRST_NAME_INVALID');
    }

    /**
     * @return void
     */
    public function testSignupFirstNameValidationTooLong()
    {
        $this->user->first_name = Str::random(256);

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'FIRST_NAME_TOO_LONG');
    }

    /**
     * @return void
     */
    public function testSignupLastNameValidationRequired()
    {
        $this->user->last_name = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'LAST_NAME_REQUIRED');
    }

    /**
     * @return void
     */
    public function testSignupLastNameValidationInvalid()
    {
        $this->user->last_name = true;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'LAST_NAME_INVALID');
    }

    /**
     * @return void
     */
    public function testSignupLastNameValidationTooLong()
    {
        $this->user->last_name = Str::random(256);

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'LAST_NAME_TOO_LONG');
    }

    /**
     * @return void
     */
    public function testSignupEmailValidationRequired()
    {
        $this->user->email = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_REQUIRED');
    }

    /**
     * @return void
     */
    public function testSignupEmailValidationInvalid()
    {
        $this->user->email = 'ImNotAnEmail';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_INVALID');
    }

    /**
     * @return void
     */
    public function testSignupEmailValidationTooLong()
    {
        $this->user->email = 'random@' . Str::random(256);

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_TOO_LONG');
    }

    /**
     * @return void
     */
    public function testSignupEmailValidationNotFound()
    {
        $user = factory(User::class)->create();

        $this->user->email = $user->email;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_EXISTS');
    }

    /**
     * @return void
     */
    public function testSignupPasswordValidationRequired()
    {
        $this->user->password = null;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_REQUIRED');
    }

    /**
     * @return void
     */
    public function testSignupPasswordValidationUnmatched()
    {
        $this->user->password = 'wrongpassword';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_UNMATCHED');
    }

    /**
     * @return void
     */
    public function testSignupPasswordValidationInvalid()
    {
        $this->user->password = 'wrongpassword';
        $this->user->password_confirmation = 'wrongpassword';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_INVALID');
    }

    /**
     * @return void
     */
    public function testSignupPasswordValidationTooShort()
    {
        $this->user->password = '$h0Rt';
        $this->user->password_confirmation = '$h0Rt';

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_TOO_SHORT');
    }

    /**
     * @return void
     */
    public function testSignupPasswordValidationTooLong()
    {
        $this->user->password = 'L0&g' . Str::random(256);
        $this->user->password_confirmation = $this->user->password;

        $response = $this->postJson(route('signup'), $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_TOO_LONG');
    }
}
