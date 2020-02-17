<?php

namespace Tests\Feature;

use App\Dish;
use App\Event;
use App\Menu;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostMenuTest extends TestCase
{
    /**
     * @var Menu
     */
    private $menu;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menu = factory(Menu::class)->make();
        $this->menu->dishes = factory(Dish::class, 2)->create()->pluck('id')->toArray();
        $this->menu->events = factory(Event::class, 2)->create()->pluck('id')->toArray();

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('menus', ['name' => $this->menu->name]);
        $this->assertDatabaseHas('menu_dishes', ['menu_id' => $response->getOriginalContent()->id]);
        $this->assertDatabaseHas('event_menus', ['menu_id' => $response->getOriginalContent()->id]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->postJson(
            route('menus.store', ['api_token' => $user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->postJson(
            route('menus.store', ['api_token' => $user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->menu->name = null;

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->menu->name = true;

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->menu->name = Str::random(256);

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishesValidationRequired()
    {
        $this->menu->dishes = null;

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_REQUIRED');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishesValidationInvalid()
    {
        $this->menu->dishes = true;

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishValidationInvalid()
    {
        $this->menu->dishes = ['notAnId'];

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_INVALID');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testDishValidationNotFound()
    {
        $this->menu->dishes = [0];

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DISHES_NOT_FOUND');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventsValidationRequired()
    {
        $this->menu->events = null;

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_REQUIRED');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventsValidationInvalid()
    {
        $this->menu->events = true;

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventValidationInvalid()
    {
        $this->menu->events = ['notAnId'];

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }

    /**
     * @return void
     */
    public function testEventValidationNotFound()
    {
        $this->menu->events = [0];

        $response = $this->postJson(
            route('menus.store', ['api_token' => $this->user->api_token]),
            $this->menu->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_NOT_FOUND');
        $this->assertDatabaseMissing('menus', ['name' => $this->menu->name]);
    }
}
