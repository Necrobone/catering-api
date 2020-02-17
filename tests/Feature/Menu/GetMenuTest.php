<?php

namespace Tests\Feature;

use App\Menu;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetMenuTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        $menu = factory(Menu::class)->create();

        $response = $this->getJson(
            route('menus.show', ['api_token' => $user->api_token, 'menu' => $menu->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $menu = factory(Menu::class)->create();

        $response = $this->getJson(
            route('menus.show', ['api_token' => $user->api_token, 'menu' => $menu->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $menu = factory(Menu::class)->create();

        $response = $this->getJson(
            route('menus.show', ['api_token' => $user->api_token, 'menu' => $menu->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->getJson(route('menus.show', ['api_token' => $user->api_token, 'menu' => 0]));

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Menu] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('menus.show', ['menu' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
