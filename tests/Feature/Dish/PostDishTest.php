<?php

namespace Tests\Feature;

use App\Event;
use App\Dish;
use App\Supplier;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostDishTest extends TestCase
{
    /**
     * @var Dish
     */
    private $dish;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dish = factory(Dish::class)->make();
        $this->dish->suppliers = factory(Supplier::class, 2)->create()->pluck('id')->toArray();
        $this->dish->events = factory(Event::class, 2)->create()->pluck('id')->toArray();

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('dishes', [
             'name'        => $this->dish->name,
             'description' => $this->dish->description,
             'image'       => $response->getOriginalContent()->image
        ]);
        $this->assertDatabaseHas('supplier_dishes', ['dish_id' => $response->getOriginalContent()->id]);
        $this->assertDatabaseHas('event_dishes', ['dish_id' => $response->getOriginalContent()->id]);

        unlink('storage/app/public/' . pathinfo($response->getOriginalContent()->image)['basename']);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->postJson(
            route('dishes.store', ['api_token' => $user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->postJson(
            route('dishes.store', ['api_token' => $user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->dish->name = null;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->dish->name = true;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->dish->name = Str::random(256);

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testDescriptionValidationRequired()
    {
        $this->dish->description = null;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DESCRIPTION_REQUIRED');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testDescriptionValidationInvalid()
    {
        $this->dish->description = true;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DESCRIPTION_INVALID');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testDescriptionValidationTooLong()
    {
        $this->dish->description = Str::random(65536);

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DESCRIPTION_TOO_LONG');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testImageValidationRequired()
    {
        $this->dish->image = null;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'IMAGE_REQUIRED');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testImageValidationInvalid()
    {
        $this->dish->image = true;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'IMAGE_INVALID');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testSuppliersValidationRequired()
    {
        $this->dish->suppliers = null;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_REQUIRED');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testSuppliersValidationInvalid()
    {
        $this->dish->suppliers = true;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_INVALID');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testSupplierValidationInvalid()
    {
        $this->dish->suppliers = ['notAnId'];

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_INVALID');
        $this->assertDatabaseMissing('dishes', ['name' => $this->dish->name]);
    }

    /**
     * @return void
     */
    public function testSupplierValidationNotFound()
    {
        $this->dish->suppliers = [0];

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_NOT_FOUND');
        $this->assertDatabaseMissing('dishes', ['name' => $this->dish->name]);
    }

    /**
     * @return void
     */
    public function testEventsValidationRequired()
    {
        $this->dish->events = null;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_REQUIRED');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testEventsValidationInvalid()
    {
        $this->dish->events = true;

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testEventValidationInvalid()
    {
        $this->dish->events = ['notAnId'];

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseMissing('dishes', ['name' => $this->dish->name]);
    }

    /**
     * @return void
     */
    public function testEventValidationNotFound()
    {
        $this->dish->events = [0];

        $response = $this->postJson(
            route('dishes.store', ['api_token' => $this->user->api_token]),
            $this->dish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_NOT_FOUND');
        $this->assertDatabaseMissing('dishes', ['name' => $this->dish->name]);
    }
}
