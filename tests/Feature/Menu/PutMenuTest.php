<?php

namespace Tests\Feature;

use App\Dish;
use App\Event;
use App\Menu;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutMenuTest extends TestCase
{
    /**
     * @var Menu
     */
    private $menu;

    /**
     * @var Menu
     */
    private $updatedMenu;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menu = factory(Menu::class)->create();

        $this->updatedMenu = factory(Menu::class)->make();
        $this->updatedMenu->dishes = factory(Dish::class, 2)->create()->pluck('id')->toArray();
        $this->updatedMenu->events = factory(Event::class, 2)->create()->pluck('id')->toArray();

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('menus.update', ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]),
            $this->updatedMenu->toArray()
        );

        $response->assertOk();
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
        $this->assertDatabaseHas('menus', ['name' => $this->updatedMenu->name]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->putJson(
            route('menus.update', ['api_token' => $user->api_token, 'menu' => $this->menu->id]),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->putJson(
            route('menus.update', ['api_token' => $user->api_token, 'menu' => $this->menu->id]),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->updatedMenu->name = null;

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->updatedMenu->name = true;

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->updatedMenu->name = Str::random(256);

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishesValidationRequired()
    {
        $this->updatedMenu->dishes = null;

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_REQUIRED');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishesValidationInvalid()
    {
        $this->updatedMenu->dishes = true;

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishValidationInvalid()
    {
        $this->updatedMenu->dishes = ['notAnId'];

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishValidationNotFound()
    {
        $this->updatedMenu->dishes = [0];

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_NOT_FOUND');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventsValidationRequired()
    {
        $this->updatedMenu->events = null;

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_REQUIRED');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventsValidationInvalid()
    {
        $this->updatedMenu->events = true;

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventValidationInvalid()
    {
        $this->updatedMenu->events = ['notAnId'];

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventValidationNotFound()
    {
        $this->updatedMenu->events = [0];

        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_NOT_FOUND');
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $response = $this->putJson(
            route(
                'menus.update',
                ['api_token' => $this->user->api_token, 'menu' => 0]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Menu] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->putJson(
            route(
                'menus.update',
                ['menu' => $this->menu->id]
            ),
            $this->updatedMenu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
