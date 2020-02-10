<?php

namespace Tests\Feature;

use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetProvincesTest extends TestCase
{
    /**
     * @return void
     */
    public function testProvincesSuccess()
    {
        $user = factory(User::class)->create();

        $response = $this->getJson(route('provinces', ['api_token' => $user->api_token]));

        $response->assertOk();

        $response->assertJsonCount(52);
    }

    /**
     * @return void
     */
    public function testProvincesFail()
    {
        $response = $this->getJson(route('provinces'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response->assertJsonPath('message', 'Unauthenticated.');
    }
}
