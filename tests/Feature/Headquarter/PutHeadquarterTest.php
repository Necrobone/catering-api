<?php

namespace Tests\Feature;

use App\Headquarter;
use App\Province;
use App\Role;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutHeadquarterTest extends TestCase
{
    /**
     * @var Headquarter
     */
    private $headquarter;

    /**
     * @var Headquarter
     */
    private $updatedHeadquarter;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->headquarter = factory(Headquarter::class)->create(
            [
                'province_id' => Province::first()
            ]
        );

        $this->updatedHeadquarter = factory(Headquarter::class)->make(
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
        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertOk();

        $this->assertDatabaseMissing(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->updatedHeadquarter->name = null;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'NAME_REQUIRED');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->updatedHeadquarter->name = true;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'NAME_INVALID');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->updatedHeadquarter->name = Str::random(256);

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'NAME_TOO_LONG');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAddressValidationRequired()
    {
        $this->updatedHeadquarter->address = null;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ADDRESS_REQUIRED');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAddressValidationInvalid()
    {
        $this->updatedHeadquarter->address = true;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ADDRESS_INVALID');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAddressValidationTooLong()
    {
        $this->updatedHeadquarter->address = Str::random(256);

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ADDRESS_TOO_LONG');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testZipValidationRequired()
    {
        $this->updatedHeadquarter->zip = null;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ZIP_REQUIRED');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testZipValidationInvalid()
    {
        $this->updatedHeadquarter->zip = true;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ZIP_INVALID');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testZipValidationTooLong()
    {
        $this->updatedHeadquarter->zip = Str::random(256);

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'ZIP_TOO_LONG');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCityValidationRequired()
    {
        $this->updatedHeadquarter->city = null;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'CITY_REQUIRED');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCityValidationInvalid()
    {
        $this->updatedHeadquarter->city = true;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'CITY_INVALID');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCityValidationTooLong()
    {
        $this->updatedHeadquarter->city = Str::random(256);

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'CITY_TOO_LONG');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testProvinceValidationRequired()
    {
        $this->updatedHeadquarter->province = null;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PROVINCE_REQUIRED');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testProvinceValidationInvalid()
    {
        $this->updatedHeadquarter->province = 'wrongprovince';

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PROVINCE_INVALID');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }

    /**
     * @return void
     */
    public function testProvinceValidationNotFound()
    {
        $this->updatedHeadquarter->province = 0;

        $response = $this->putJson(
            route(
                'headquarters.update',
                ['api_token' => $this->user->api_token, 'headquarters' => $this->headquarter->id]
            ),
            $this->updatedHeadquarter->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonPath('error', 'PROVINCE_NOT_FOUND');

        $this->assertDatabaseHas(
            'headquarters',
            [
                'name'        => $this->headquarter->name,
                'address'     => $this->headquarter->address,
                'zip'         => $this->headquarter->zip,
                'city'        => $this->headquarter->city,
                'province_id' => $this->headquarter->province_id,
            ]
        );
    }
}
