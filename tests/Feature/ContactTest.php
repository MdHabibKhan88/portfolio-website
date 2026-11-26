<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private function valid(array $override = []): array
    {
        return array_merge([
            'name' => 'Rahim', 'email' => 'rahim@example.com',
            'subject' => 'Hello', 'message' => 'This is a test message.',
        ], $override);
    }

    public function test_valid_message_is_stored(): void
    {
        $this->post('/contact', $this->valid())->assertRedirect()->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contact_messages', ['email' => 'rahim@example.com', 'is_read' => false]);
    }

    public function test_invalid_message_is_rejected(): void
    {
        $this->post('/contact', $this->valid(['email' => 'nope', 'message' => 'short']))
            ->assertSessionHasErrors(['email', 'message'], null, 'contact');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_honeypot_blocks_bots_silently(): void
    {
        $this->post('/contact', $this->valid(['website' => 'http://spam.example']))->assertRedirect();
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', $this->valid())->assertRedirect();
        }
        $this->post('/contact', $this->valid())->assertStatus(429);
    }
}
