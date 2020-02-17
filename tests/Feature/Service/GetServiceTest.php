<?php

namespace Tests\Feature;

use App\Service;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testAdminSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        $service = factory(Service::class)->create();

        $response = $this->getJson(
            route('services.show', ['api_token' => $user->api_token, 'service' => $service->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeSuccess()
    {
        $user = factory(User::class)->state('employee')->create();
        $service = factory(Service::class)->create();

        $service->users()->save($user);

        $response = $this->getJson(
            route('services.show', ['api_token' => $user->api_token, 'service' => $service->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testUserSuccess()
    {
        $user = factory(User::class)->state('user')->create();
        $service = factory(Service::class)->create();

        $service->users()->save($user);

        $response = $this->getJson(
            route('services.show', ['api_token' => $user->api_token, 'service' => $service->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $service = factory(Service::class)->create();

        $response = $this->getJson(
            route('services.show', ['api_token' => $user->api_token, 'service' => $service->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $service = factory(Service::class)->create();

        $response = $this->getJson(
            route('services.show', ['api_token' => $user->api_token, 'service' => $service->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->getJson(route('services.show', ['api_token' => $user->api_token, 'service' => 0]));

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Service] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('services.show', ['service' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
