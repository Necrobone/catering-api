<?php

namespace Tests\Feature;

use App\Dish;
use App\Service;
use App\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ToggleServiceTest extends TestCase
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

        $this->service = factory(Service::class)->create();
        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('services.toggle', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            ['approved' => 1]
        );

        $response->assertOk();

        $this->assertDatabaseHas('services', [
            'address'     => $this->service->address,
            'zip'         => $this->service->zip,
            'city'        => $this->service->city,
            'start_date'  => $this->service->start_date,
            'approved'    => 1,
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
        $response = $this->putJson(
            route('services.toggle', ['api_token' => $user->api_token, 'service' => $this->service->id]),
            ['approved' => 1]
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
            route('services.toggle', ['api_token' => $user->api_token, 'service' => $this->service->id]),
            ['approved' => 1]
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
    public function testApprovedValidationRequired()
    {
        $response = $this->putJson(
            route('services.toggle', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            ['approved' => null]
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
        $response = $this->putJson(
            route('services.toggle', ['api_token' => $this->user->api_token, 'service' => $this->service->id]),
            ['approved' => 123]
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
    public function testNotFound()
    {
        $response = $this->putJson(
            route('services.toggle', ['api_token' => $this->user->api_token, 'service' => 0]),
            ['approved' => 1]
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
            route('services.toggle', ['service' => $this->service->id]),
            ['approved' => 1]
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
