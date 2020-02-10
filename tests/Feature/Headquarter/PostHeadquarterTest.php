<?php

namespace Tests\Feature;

use App\Headquarter;
use App\Province;
use App\Role;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostHeadquarterTest extends TestCase
{
    /**
     * @var Headquarter
     */
    private $headquarter;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->headquarter = factory(Headquarter::class)->make(
            [
                'province' => Province::first(),
            ]
        );

        $this->user = factory(User::class)->create(
            [
                'role_id' => Role::ADMINISTRATOR,
            ]
        );
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->headquarter->name = null;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'NAME_REQUIRED');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->headquarter->name = true;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'NAME_INVALID');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->headquarter->name = Str::random(256);

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'NAME_TOO_LONG');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAddressValidationRequired()
    {
        $this->headquarter->address = null;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ADDRESS_REQUIRED');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAddressValidationInvalid()
    {
        $this->headquarter->address = true;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ADDRESS_INVALID');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAddressValidationTooLong()
    {
        $this->headquarter->address = Str::random(256);

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ADDRESS_TOO_LONG');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testZipValidationRequired()
    {
        $this->headquarter->zip = null;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ZIP_REQUIRED');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testZipValidationInvalid()
    {
        $this->headquarter->zip = true;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ZIP_INVALID');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testZipValidationTooLong()
    {
        $this->headquarter->zip = Str::random(256);

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ZIP_TOO_LONG');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCityValidationRequired()
    {
        $this->headquarter->city = null;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'CITY_REQUIRED');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCityValidationInvalid()
    {
        $this->headquarter->city = true;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'CITY_INVALID');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCityValidationTooLong()
    {
        $this->headquarter->city = Str::random(256);

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'CITY_TOO_LONG');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testProvinceValidationRequired()
    {
        $this->headquarter->province = null;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PROVINCE_REQUIRED');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testProvinceValidationInvalid()
    {
        $this->headquarter->province = 'wrongprovince';

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PROVINCE_INVALID');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }

    /**
     * @return void
     */
    public function testProvinceValidationNotFound()
    {
        $this->headquarter->province = 0;

        $response = $this->postJson(
            route('headquarters.store', ['api_token' => $this->user->api_token]),
            $this->headquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PROVINCE_NOT_FOUND');

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province,
            ]
        );
    }
}
