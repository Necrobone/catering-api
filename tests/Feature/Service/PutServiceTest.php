<?php

namespace Tests\Feature;

use App\Dish;
use App\Service;
use App\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutServiceTest extends TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var Service
     */
    private $updatedService;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = factory(Service::class)->create();

        $this->updatedService = factory(Service::class)->make();
        $this->updatedService->dishes = factory(Dish::class, 2)->create()->pluck('id')->toArray();
        $this->updatedService->users = factory(User::class, 2)->create()->pluck('id')->toArray();
        $this->updatedService->startDate = $this->updatedService->start_date->format(DATE_ISO8601);
        $this->updatedService->province = $this->updatedService->province_id;
        $this->updatedService->event = $this->updatedService->event_id;

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertOk();

        $this->assertDatabaseMissing('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => $this->service->approved,
            'province_id' => $this->service->province_id,
            'event_id'    => $this->service->event_id,
        ]);

        $this->assertDatabaseHas('services', [
            'address'     => $this->updatedService->address,
            'zip'         => $this->updatedService->zip,
            'city'        => $this->updatedService->city,
            'start_date'  => $this->updatedService->start_date,
            'approved'    => $this->updatedService->approved,
            'province_id' => $this->updatedService->province_id,
            'event_id'    => $this->updatedService->event_id,
        ]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->putJson(
            route('services.update', ['api_token' => $user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('services', [
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
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->putJson(
            route('services.update', ['api_token' => $user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->address = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ADDRESS_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->address = true;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ADDRESS_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->address = Str::random(256);

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ADDRESS_TOO_LONG');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->zip = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ZIP_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->zip = true;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ZIP_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->zip = Str::random(256);

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'ZIP_TOO_LONG');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->city = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'CITY_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->city = true;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'CITY_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->city = Str::random(256);

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'CITY_TOO_LONG');
        $this->assertDatabaseHas('services', [
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
        unset($this->updatedService->approved);

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'APPROVED_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->approved = 123;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'APPROVED_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->startDate = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'START_DATE_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->startDate = true;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'START_DATE_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->startDate = Date::yesterday()->toIso8601String();

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'START_DATE_PAST');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->province = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PROVINCE_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->province = 'wrongprovince';

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PROVINCE_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->province = 0;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'PROVINCE_NOT_FOUND');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->event = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENT_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->event = 'wrongevent';

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENT_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->event = 0;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENT_NOT_FOUND');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->dishes = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->dishes = true;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->dishes = ['notAnId'];

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseHas('services', [
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
    public function testDishValidationNotFound()
    {
        $this->updatedService->dishes = [0];

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_NOT_FOUND');
        $this->assertDatabaseHas('services', [
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
    public function testUsersValidationRequired()
    {
        $this->updatedService->users = null;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_REQUIRED');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->users = true;

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->users = ['notAnId'];

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_INVALID');
        $this->assertDatabaseHas('services', [
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
        $this->updatedService->users = [0];

        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'USERS_NOT_FOUND');
        $this->assertDatabaseHas('services', [
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
    public function testNotFound()
    {
        $response = $this->putJson(
            route('services.update', ['api_token' => $this->user->api_token, 'service' => 0]),
            $this->updatedService->toArray()
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Service] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->putJson(
            route('services.update', ['service' => $this->service->id]),
            $this->updatedService->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
