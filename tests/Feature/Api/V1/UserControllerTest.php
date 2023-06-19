<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_create(): void
    {
        $user = User::factory()->make();
        $response = $this->post('/api/v1/users', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result' => [
                'user' => [
                    'name',
                    'email',
                    'updated_at',
                    'created_at',
                    'id'
                ]
            ]
        ]);
    }
}
