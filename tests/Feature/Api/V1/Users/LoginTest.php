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
        $email = 'user.test@gmail.com';
        $pass = '12345678';

        $accessToken = $this->authorizeAccessToken($email, $pass);

        $response = $this->get('/api/v1/user');

        $response->assertRedirect(route('login'));

        /*$headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];
        $response = Http::withHeaders($headers)->get('http://localhost/api/v1/user');
        $result = $response->json();*/
    }
}
