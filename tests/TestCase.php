<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void 
    {
        parent::setUp();
        $userCredential = Constants::userApiCredential();
        $this->authorizeAccessToken($userCredential['email'], $userCredential['password']);
    }

    /**
     * @param string $accessToken
     */
    public function authorizeAccessToken(string $email, string $password): string
    {
        $response = $this->post('/api/v1/user-login', [
            'email' => $email,
            'password' => $password
        ]);

        $accessToken = $response->json('accessToken');

        $this->withHeader('Authorization', 'Bearer ' . $accessToken);

        return $accessToken;
    }
}
