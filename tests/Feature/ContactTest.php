<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_valid(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'message' => 'Hello',
        ]);
        $response->assertRedirect('/contact');
        $this->assertDatabaseHas('contact_messages', ['email' => 'test@example.com']);
    }

    public function test_contact_invalid(): void
    {
        $response = $this->post('/contact', ['name' => '', 'email' => 'bad', 'message' => '']);
        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }
}
