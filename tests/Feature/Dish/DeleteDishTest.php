<?php

namespace Tests\Feature;

use App\Dish;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteDishTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        $dish = factory(Dish::class)->create();

        $response = $this->deleteJson(
            route('dishes.destroy', ['api_token' => $user->api_token, 'dish' => $dish->id])
        );

        $response->assertOk();
        $this->assertSoftDeleted('dishes', $dish->toArray());
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $dish = factory(Dish::class)->create();

        $response = $this->deleteJson(
            route('dishes.destroy', ['api_token' => $user->api_token, 'dish' => $dish->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('dishes', $dish->toArray());
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $dish = factory(Dish::class)->create();

        $response = $this->deleteJson(
            route('dishes.destroy', ['api_token' => $user->api_token, 'dish' => $dish->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('dishes', $dish->toArray());
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->deleteJson(
            route('dishes.destroy', ['api_token' => $user->api_token, 'dish' => 0])
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Dish] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->deleteJson(route('dishes.destroy', ['dish' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
