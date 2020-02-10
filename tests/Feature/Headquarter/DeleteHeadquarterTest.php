<?php

namespace Tests\Feature;

use App\Headquarter;
use App\Province;
use App\Role;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteHeadquarterTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::ADMINISTRATOR
            ]
        );

        $headquarter = factory(Headquarter::class)->create(
            [
                'province_id' => Province::first()
            ]
        );

        $response = $this->getJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => $headquarter->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::EMPLOYEE
            ]
        );

        $headquarter = factory(Headquarter::class)->create(
            [
                'province_id' => Province::first()
            ]
        );

        $response = $this->getJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => $headquarter->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::USER
            ]
        );

        $headquarter = factory(Headquarter::class)->create(
            [
                'province_id' => Province::first()
            ]
        );

        $response = $this->getJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => $headquarter->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->create(
            [
                'role_id' => Role::ADMINISTRATOR
            ]
        );

        $response = $this->getJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => 0])
        );

        $response->assertNotFound();

        $response->assertJsonPath('message', 'No query results for model [App\Headquarter] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('headquarters.destroy', ['headquarters' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
