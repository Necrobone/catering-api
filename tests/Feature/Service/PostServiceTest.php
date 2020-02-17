<?php

namespace Tests\Feature;

use App\Dish;
use App\Service;
use App\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = factory(Service::class)->make();
        $this->service->dishes = factory(Dish::class, 2)->create()->pluck('id')->toArray();
        $this->service->users = factory(User::class, 2)->create()->pluck('id')->toArray();
        $this->service->startDate = $this->service->start_date->format(DATE_ISO8601);
        $this->service->province = $this->service->province_id;
        $this->service->event = $this->service->event_id;

        $this->user = factory(User::class)->state('user')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
        $this->assertDatabaseHas('service_dishes', ['service_id' => $response->getOriginalContent()->id]);
        $this->assertDatabaseHas('user_services', ['service_id' => $response->getOriginalContent()->id]);
    }

    /**
     * @return void
     */
    public function testAdminAuthorizationFail()
    {
        $user = factory(User::class)->state('administrator')->create();
        $response = $this->postJson(
            route('services.store', ['api_token' => $user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->postJson(
            route('services.store', ['api_token' => $user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testAddressValidationRequired()
    {
        $this->service->address = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ADDRESS_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testAddressValidationInvalid()
    {
        $this->service->address = true;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ADDRESS_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testAddressValidationTooLong()
    {
        $this->service->address = Str::random(256);

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ADDRESS_TOO_LONG');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testZipValidationRequired()
    {
        $this->service->zip = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ZIP_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testZipValidationInvalid()
    {
        $this->service->zip = true;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ZIP_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testZipValidationTooLong()
    {
        $this->service->zip = Str::random(256);

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ZIP_TOO_LONG');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testCityValidationRequired()
    {
        $this->service->city = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'CITY_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testCityValidationInvalid()
    {
        $this->service->city = true;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'CITY_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testCityValidationTooLong()
    {
        $this->service->city = Str::random(256);

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'CITY_TOO_LONG');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testApprovedValidationRequired()
    {
        unset($this->service->approved);

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'APPROVED_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testApprovedValidationInvalid()
    {
        $this->service->approved = 123;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'APPROVED_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testStartDateValidationRequired()
    {
        $this->service->startDate = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'START_DATE_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testStartDateValidationInvalid()
    {
        $this->service->startDate = true;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'START_DATE_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testStartDateValidationPast()
    {
        $this->service->startDate = Date::yesterday()->toIso8601String();

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'START_DATE_PAST');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testProvinceValidationRequired()
    {
        $this->service->province = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PROVINCE_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testProvinceValidationInvalid()
    {
        $this->service->province = 'wrongprovince';

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PROVINCE_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testProvinceValidationNotFound()
    {
        $this->service->province = 0;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PROVINCE_NOT_FOUND');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testEventValidationRequired()
    {
        $this->service->event = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENT_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testEventValidationInvalid()
    {
        $this->service->event = 'wrongevent';

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENT_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testEventValidationNotFound()
    {
        $this->service->event = 0;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENT_NOT_FOUND');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testDishesValidationRequired()
    {
        $this->service->dishes = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testDishesValidationInvalid()
    {
        $this->service->dishes = true;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testDishValidationInvalid()
    {
        $this->service->dishes = ['notAnId'];

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseMissing('services', ['name' => $this->service->name]);
    }

    /**
     * @return void
     */
    public function testDishValidationNotFound()
    {
        $this->service->dishes = [0];

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_NOT_FOUND');
        $this->assertDatabaseMissing('services', ['name' => $this->service->name]);
    }

    /**
     * @return void
     */
    public function testUsersValidationRequired()
    {
        $this->service->users = null;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_REQUIRED');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testUsersValidationInvalid()
    {
        $this->service->users = true;

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testUserValidationInvalid()
    {
        $this->service->users = ['notAnId'];

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_INVALID');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testUserValidationNotFound()
    {
        $this->service->users = [0];

        $response = $this->postJson(
            route('services.store', ['api_token' => $this->user->api_token]),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_NOT_FOUND');
        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->postJson(
            route('services.store'),
            $this->service->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
