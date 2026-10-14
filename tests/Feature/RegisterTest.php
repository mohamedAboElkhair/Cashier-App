<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_without_active_field_and_it_defaults_to_true(): void
    {
        $payload = [
            'name' => 'Mohamed AbouElkhair',
            'username' => 'mohamed',
            'email' => 'mohamed@example.com',
            'role' => 'cashier',
            'password' => 'secret123',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user',
                'username',
                'user_id',
                'role',
                'active',
            ])
            ->assertJson([
                'username' => 'mohamed',
                'role' => 'cashier',
                'active' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'mohamed',
            'email' => 'mohamed@example.com',
            'active' => true,
        ]);
    }

    public function test_user_can_register_with_active_false(): void
    {
        $payload = [
            'name' => 'Inactive User',
            'username' => 'inactive_user',
            'email' => 'inactive@example.com',
            'role' => 'cashier',
            'password' => 'secret123',
            'active' => false,
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'username' => 'inactive_user',
                'active' => false,
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'inactive_user',
            'email' => 'inactive@example.com',
            'active' => false,
        ]);
    }

    public function test_registration_fails_when_active_is_not_a_boolean(): void
    {
        $payload = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'cashier',
            'password' => 'secret123',
            'active' => 'not-a-boolean',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['active']);
    }
}
