<?php

namespace Tests\Feature\Api\V1\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_accesstoken(): void
    {
        $response = $this->post('/api/v1/user-login', [
            'email' => 'kameron91@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $this->assertIsString($response->json('accessToken'));
        $result = $response->decodeResponseJson();
    }

    /**
     * Test get user by accessToken of login user
     * 
     */
    public function test_get_user(): void
    {
        $response = $this->get('/api/v1/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message'
        ]);
        $response->assertJson([
            'status' => 1,
            'message' => 'successfull'
        ]);
        //$response->assertRedirect(route('login'));

        /*$headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];
        $response = Http::withHeaders($headers)->get('http://localhost/api/v1/user');
        $result = $response->json();*/
    }
}
