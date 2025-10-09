<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

  public function test_user_can_register_successfully()
{

    $response = $this->postJson('/api/register', [
        'name' => 'John',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone_no' => '+2348012345678',
        'role' => 'user',
    ]);

     $response->dump(); // shows body
    $response->dumpHeaders();// This will show exactly what's wrong

    $response->assertStatus(201)
        ->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'phone_no',
                    'role',
                ],
                'token',
                'permissions'
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
}

// public function test_registration_validation_errors()
// {
//     $response = $this->postJson('/api/register', [
//         'email' => 'invalid-email',
//         'password' => 'short',
//     ]);

//     $response->assertStatus(422)
//         ->assertJsonStructure([
//             'message',
//             'errors' => [
//                 'name',
//                 'email',
//                 'password',
//                 'phone_no',
//             ]
//         ]);

//     $response->assertJsonValidationErrors(['name', 'email', 'password', 'phone_no']);
// }


public function test_registration_validation_errors()
{
    $response = $this->postJson('/api/register', [
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'not-matching',
        'role' => 'invalid-role',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'code',
            'message',
            'errors' => [
                'name',
                'email',
                'password',
                'phone_no',
                'role',
            ]
        ]);

    $response->assertJsonValidationErrors([
        'name',
        'email',
        'password',
        'phone_no',
        'role'
    ]);
}

}
