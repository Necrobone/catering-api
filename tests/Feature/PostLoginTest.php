<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\MigrationTestCase;

class PostLoginTest extends MigrationTestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(route('login'), ['email' => 'admin@gmail.com', 'password' => 'administrator']);

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->postJson(route('login'), ['email' => 'admin@gmail.com', 'password' => 'fakePassword$2']);

        $response->assertForbidden();

        $response->assertJsonPath('error', 'LOGIN_ERROR');
    }

    /**
     * @return void
     */
    public function testEmailValidationRequired()
    {
        $response = $this->postJson(route('login'), ['email' => null, 'password' => 'fakePassword$2']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_REQUIRED');
    }

    /**
     * @return void
     */
    public function testEmailValidationInvalid()
    {
        $response = $this->postJson(route('login'), ['email' => 'ImNotAnEmail', 'password' => 'fakePassword$2']);

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
            ['email' => 'random@'.Str::random(256), 'password' => 'fakePassword$2']
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_TOO_LONG');
    }

    /**
     * @return void
     */
    public function testEmailValidationNotFound()
    {
        $response = $this->postJson(route('login'), ['email' => 'I@dont.exist', 'password' => 'fakePassword$2']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'EMAIL_NOT_FOUND');
    }

    /**
     * @return void
     */
    public function testPasswordValidationRequired()
    {
        $response = $this->postJson(route('login'), ['email' => 'admin@gmail.com', 'password' => null]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_REQUIRED');
    }

    /**
     * @return void
     */
    public function testPasswordValidationInvalid()
    {
        $response = $this->postJson(route('login'), ['email' => 'admin@gmail.com', 'password' => true]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_INVALID');
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooShort()
    {
        $response = $this->postJson(route('login'), ['email' => 'admin@gmail.com', 'password' => 'short']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_TOO_SHORT');
    }

    /**
     * @return void
     */
    public function testPasswordValidationTooLong()
    {
        $response = $this->postJson(route('login'), ['email' => 'admin@gmail.com', 'password' => Str::random(256)]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PASSWORD_TOO_LONG');
    }
}
