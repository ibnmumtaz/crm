<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MeetingType;
use App\Models\User;
use Database\Seeders\MeetingTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MeetingTypeSeeder::class);
    }

    public function test_booking_page_loads(): void
    {
        $response = $this->get('/book');
        $response->assertStatus(200);
    }

    public function test_booking_can_be_created(): void
    {
        $mt = MeetingType::first();
        $slot = now()->addDay()->setTime(10, 0);
        while ($slot->isWeekend()) {
            $slot->addDay();
        }
        $response = $this->post('/book', [
            'meeting_type_id' => $mt->id,
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'starts_at' => $slot->toIso8601String(),
            'timezone' => 'UTC',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', ['email' => 'visitor@example.com']);
    }

    public function test_booking_rejects_past_slot(): void
    {
        $mt = MeetingType::first();
        $slot = now()->subDay()->setTime(10, 0);
        while ($slot->isWeekend()) {
            $slot->subDay();
        }

        $response = $this->post('/book', [
            'meeting_type_id' => $mt->id,
            'name' => 'Past Visitor',
            'email' => 'past@example.com',
            'starts_at' => $slot->toIso8601String(),
            'timezone' => 'UTC',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', ['email' => 'past@example.com']);
    }

    public function test_booking_rejects_conflicting_slot(): void
    {
        $mt = MeetingType::first();
        $slot = now()->addDays(2)->setTime(10, 0);
        while ($slot->isWeekend()) {
            $slot->addDay();
        }

        Booking::create([
            'meeting_type_id' => $mt->id,
            'name' => 'Existing',
            'email' => 'existing@example.com',
            'starts_at' => $slot,
            'ends_at' => $slot->copy()->addMinutes($mt->duration_minutes),
            'timezone' => 'UTC',
            'status' => 'confirmed',
            'cancellation_token' => 'token',
        ]);

        $response = $this->post('/book', [
            'meeting_type_id' => $mt->id,
            'name' => 'Conflict Visitor',
            'email' => 'conflict@example.com',
            'starts_at' => $slot->toIso8601String(),
            'timezone' => 'UTC',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', ['email' => 'conflict@example.com']);
    }

    public function test_booking_admin_requires_authorization(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.bookings.admin'));
        $response->assertStatus(403);
    }
}
