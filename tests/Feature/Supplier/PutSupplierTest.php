<?php

namespace Tests\Feature;

use App\Headquarter;
use App\Supplier;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutSupplierTest extends TestCase
{
    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * @var Supplier
     */
    private $updatedSupplier;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplier = factory(Supplier::class)->create();

        $this->updatedSupplier = factory(Supplier::class)->make();
        $this->updatedSupplier->headquarters = factory(Headquarter::class, 2)->create()->pluck('id')->toArray();

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->putJson(
            route('suppliers.update', ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]),
            $this->updatedSupplier->toArray()
        );

        $response->assertOk();
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
        $this->assertDatabaseHas('suppliers', ['name' => $this->updatedSupplier->name]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->putJson(
            route('suppliers.update', ['api_token' => $user->api_token, 'supplier' => $this->supplier->id]),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->putJson(
            route('suppliers.update', ['api_token' => $user->api_token, 'supplier' => $this->supplier->id]),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->updatedSupplier->name = null;

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->updatedSupplier->name = true;

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->updatedSupplier->name = Str::random(256);

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquartersValidationRequired()
    {
        $this->updatedSupplier->headquarters = null;

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_REQUIRED');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquartersValidationInvalid()
    {
        $this->updatedSupplier->headquarters = true;

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_INVALID');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquarterValidationInvalid()
    {
        $this->updatedSupplier->headquarters = ['notAnId'];

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_INVALID');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquarterValidationNotFound()
    {
        $this->updatedSupplier->headquarters = [0];

        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_NOT_FOUND');
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $response = $this->putJson(
            route(
                'suppliers.update',
                ['api_token' => $this->user->api_token, 'supplier' => 0]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No query results for model [App\Supplier] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->putJson(
            route(
                'suppliers.update',
                ['supplier' => $this->supplier->id]
            ),
            $this->updatedSupplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
