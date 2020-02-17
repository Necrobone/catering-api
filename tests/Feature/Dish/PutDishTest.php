<?php

namespace Tests\Feature;

use App\Event;
use App\Supplier;
use App\Dish;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutDishTest extends TestCase
{
    /**
     * @var Dish
     */
    private $dish;

    /**
     * @var Dish
     */
    private $updatedDish;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dish = factory(Dish::class)->create();

        $this->updatedDish = factory(Dish::class)->make();
        $this->updatedDish->suppliers = factory(Supplier::class, 2)->create()->pluck('id')->toArray();
        $this->updatedDish->events = factory(Event::class, 2)->create()->pluck('id')->toArray();

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('dishes.update', ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]),
            $this->updatedDish->toArray()
        );

        $this->updatedDish->image = $response->getOriginalContent()->image;

        $response->assertOk();

        $this->assertDatabaseMissing('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);

        $this->assertDatabaseHas('dishes', [
            'name'        => $this->updatedDish->name,
            'description' => $this->updatedDish->description,
            'image'       => $this->updatedDish->image
        ]);

        unlink('storage/app/public/' . pathinfo($this->updatedDish->image)['basename']);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->putJson(
            route('dishes.update', ['api_token' => $user->api_token, 'dish' => $this->dish->id]),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('dishes', [
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
        $response = $this->putJson(
            route('dishes.update', ['api_token' => $user->api_token, 'dish' => $this->dish->id]),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->name = null;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->name = true;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->name = Str::random(256);

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->description = null;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DESCRIPTION_REQUIRED');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->description = true;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DESCRIPTION_INVALID');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->description = Str::random(65536);

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'DESCRIPTION_TOO_LONG');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->image = null;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'IMAGE_REQUIRED');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->image = true;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'IMAGE_INVALID');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->suppliers = null;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_REQUIRED');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->suppliers = true;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_INVALID');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->suppliers = ['notAnId'];

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_INVALID');
        $this->assertDatabaseHas('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testSupplierValidationNotFound()
    {
        $this->updatedDish->suppliers = [0];

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'SUPPLIERS_NOT_FOUND');
        $this->assertDatabaseHas('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testEventsValidationRequired()
    {
        $this->updatedDish->events = null;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_REQUIRED');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->events = true;

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseHas('dishes', [
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
        $this->updatedDish->events = ['notAnId'];

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_INVALID');
        $this->assertDatabaseHas('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }

    /**
     * @return void
     */
    public function testEventValidationNotFound()
    {
        $this->updatedDish->events = [0];

        $response = $this->putJson(
            route(
                'dishes.update',
                ['api_token' => $this->user->api_token, 'dish' => $this->dish->id]
            ),
            $this->updatedDish->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'EVENTS_NOT_FOUND');
        $this->assertDatabaseHas('dishes', [
            'name'        => $this->dish->name,
            'description' => $this->dish->description,
            'image'       => $this->dish->image
        ]);
    }
}
