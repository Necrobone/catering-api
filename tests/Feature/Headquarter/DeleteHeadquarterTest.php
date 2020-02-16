<?php

namespace Tests\Feature;

use App\Headquarter;
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
        $user = factory(User::class)->state('administrator')->create();
        $headquarter = factory(Headquarter::class)->create();

        $response = $this->deleteJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => $headquarter->id])
        );

        $response->assertOk();
        $this->assertSoftDeleted('headquarters', $headquarter->toArray());
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $headquarter = factory(Headquarter::class)->create();

        $response = $this->deleteJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => $headquarter->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('headquarters', $headquarter->toArray());
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $headquarter = factory(Headquarter::class)->create();

        $response = $this->deleteJson(
            route('headquarters.destroy', ['api_token' => $user->api_token, 'headquarters' => $headquarter->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('headquarters', $headquarter->toArray());
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->deleteJson(
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
        $response = $this->deleteJson(route('headquarters.destroy', ['headquarters' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
