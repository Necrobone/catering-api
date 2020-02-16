<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostLoginTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => Hash::make('Madrid4$')
        ]);
        $this->user->plainPassword = 'Madrid4$';
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(route('login'), ['email' => $this->user->email, 'password' => $this->user->plainPassword]);

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->postJson(route('login'), ['email' => $this->user->email, 'password' => 'fakePassword$2']);

        $response->assertForbidden();
        $response->assertJsonPath('error', 'LOGIN_ERROR');
    }

    /**
     * @return void
     */
    public function testEmailValidationRequired()
    {
        $response = $this->postJson(route('login'), ['email' => null, 'password' => $this->user->plainPassword]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_REQUIRED');
    }

    /**
     * @return void
     */
    public function testEmailValidationInvalid()
    {
        $response = $this->postJson(route('login'), ['email' => 'ImNotAnEmail', 'password' => $this->user->plainPassword]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_INVALID');
    }

    /**
     * @return void
     */
    public function testEmailValidationTooLong()
    {
        $response = $this->postJson(
            route('login'),
            ['email' => 'random@'.Str::random(256), 'password' => $this->user->plainPassword]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_TOO_LONG');
    }

    /**
     * @return void
     */
    public function testEmailValidationNotFound()
    {
        $response = $this->postJson(route('login'), ['email' => 'I@dont.exist', 'password' => $this->user->plainPassword]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EMAIL_NOT_FOUND');
    }

    /**
     * @return void
     */
    public function testPasswordValidationRequired()
    {
        $response = $this->postJson(route('login'), ['email' => $this->user->email, 'password' => null]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_REQUIRED');
    }

    /**
     * @return void
     */
    public function testPasswordValidationInvalid()
    {
        $response = $this->postJson(route('login'), ['email' => $this->user->email, 'password' => true]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_INVALID');
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooShort()
    {
        $response = $this->postJson(route('login'), ['email' => $this->user->email, 'password' => 'short']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_SHORT');
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooLong()
    {
        $response = $this->postJson(route('login'), ['email' => $this->user->email, 'password' => Str::random(256)]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PASSWORD_TOO_LONG');
    }
}
