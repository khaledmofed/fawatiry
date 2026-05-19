<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        if (config('invoicing.allow_registration')) {
            $response->assertOk();
        } else {
            $response->assertNotFound();
        }
    }

    public function test_new_users_can_register(): void
    {
        if (! config('invoicing.allow_registration')) {
            $this->markTestSkipped('Registration is disabled for single-company mode.');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
