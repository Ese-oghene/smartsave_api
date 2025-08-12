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
            'name'            => 'John',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'phone'                 => '+2348012345678',
        ]);

        if ($response->status() !== 200) {
        $response->dump(); // body
        $response->dumpHeaders(); // headers if helpful
    }


        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'code',
                     'message',
                     'data' => [
                         'user' => ['id', 'first_name', 'last_name', 'email', 'phone'],
                         'token',
                     ],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_validation_errors()
    {
        // Optional while debugging:
        // $this->withoutExceptionHandling();

        $response = $this->postJson('/api/register', [
            // missing required fields and invalid values
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         // Laravel usually nests field keys here; you can be loose
                         'email',
                         'password',
                     ],
                 ]);

        $response->assertJsonValidationErrors(['email', 'password']);
    }
}
