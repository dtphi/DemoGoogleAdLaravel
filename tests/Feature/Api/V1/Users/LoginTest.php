<?php

namespace Tests\Feature\Api\V1\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_accesstoken(): void
    {
        $response = $this->post('/api/v1/user-login', [
            'email' => 'user.test@gmail.com',
            'password' => '12345678'
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
        $response = $this->post('/api/v1/user-login', [
            'email' => 'user.test@gmail.com',
            'password' => '12345678'
        ]);

        $accessToken = $response->json('accessToken');

        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        //$response = $this->get('/api/user', [], $headers);
    }
}
