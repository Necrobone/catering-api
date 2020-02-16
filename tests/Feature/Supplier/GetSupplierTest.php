<?php

namespace Tests\Feature;

use App\Supplier;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetSupplierTest extends TestCase
{
    /**
     * @return void
     */
    public function testSuccess()
    {
        $user = factory(User::class)->state('administrator')->create();
        $supplier = factory(Supplier::class)->create();

        $response = $this->getJson(
            route('suppliers.show', ['api_token' => $user->api_token, 'supplier' => $supplier->id])
        );

        $response->assertOk();
    }

    /**
     * @return void
     */
    public function testEmployeeAuthorizationFail()
    {
        $user = factory(User::class)->state('employee')->create();
        $supplier = factory(Supplier::class)->create();

        $response = $this->getJson(
            route('suppliers.show', ['api_token' => $user->api_token, 'supplier' => $supplier->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testUserAuthorizationFail()
    {
        $user = factory(User::class)->state('user')->create();
        $supplier = factory(Supplier::class)->create();

        $response = $this->getJson(
            route('suppliers.show', ['api_token' => $user->api_token, 'supplier' => $supplier->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     */
    public function testNotFound()
    {
        $user = factory(User::class)->state('administrator')->create();

        $response = $this->getJson(route('suppliers.show', ['api_token' => $user->api_token, 'supplier' => 0]));

        $response->assertNotFound();

        $response->assertJsonPath('message', 'No query results for model [App\Supplier] 0');
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $response = $this->getJson(route('suppliers.show', ['supplier' => 0]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
