<?php

namespace Tests\Feature;

use App\Headquarter;
use App\Supplier;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostSupplierTest extends TestCase
{
    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * @var User
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplier = factory(Supplier::class)->make();
        $this->supplier->headquarters = factory(Headquarter::class, 2)->create()->pluck('id')->toArray();

        $this->user = factory(User::class)->state('administrator')->create();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('suppliers', ['name' => $this->supplier->name]);
        $this->assertDatabaseHas('supplier_headquarters', ['supplier_id' => $response->getOriginalContent()->id]);
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationRequired()
    {
        $this->supplier->name = null;

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_REQUIRED');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationInvalid()
    {
        $this->supplier->name = true;

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_INVALID');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testNameValidationTooLong()
    {
        $this->supplier->name = Str::random(256);

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'NAME_TOO_LONG');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquartersValidationRequired()
    {
        $this->supplier->headquarters = null;

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_REQUIRED');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquartersValidationInvalid()
    {
        $this->supplier->headquarters = true;

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_INVALID');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquarterValidationInvalid()
    {
        $this->supplier->headquarters = ['notAnId'];

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_INVALID');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }

    /**
     * @return void
     */
    public function testHeadquarterValidationNotFound()
    {
        $this->supplier->headquarters = [0];

        $response = $this->postJson(
            route('suppliers.store', ['api_token' => $this->user->api_token]),
            $this->supplier->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonPath('error', 'HEADQUARTERS_NOT_FOUND');
        $this->assertDatabaseMissing('suppliers', ['name' => $this->supplier->name]);
    }
}
