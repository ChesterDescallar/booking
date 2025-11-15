<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->client = Client::factory()->create();
    }

    public function test_customer_can_create_booking(): void
    {
        $startTime = now()->addDay();
        $endTime = now()->addDay()->addHours(2);

        $response = $this->actingAs($this->user)
            ->postJson('/api/bookings', [
                'user_id' => $this->user->id,
                'title' => 'Test Booking',
                'description' => 'Test Description',
                'client_id' => $this->client->id,
                'start_time' => $startTime->toISOString(),
                'end_time' => $endTime->toISOString(),
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Test Booking',
                    'client_id' => $this->client->id,
                    'user_id' => $this->user->id,
                    'client' => [
                        'id' => $this->client->id,
                    ],
                    'user' => [
                        'id' => $this->user->id,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('bookings', [
            'title' => 'Test Booking',
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
        ]);
    }

    public function test_booking_prevents_overlapping_bookings(): void
    {
        // Create initial booking
        $startTime = now()->addDay()->setTime(10, 0);
        $endTime = now()->addDay()->setTime(11, 0);

        Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Try to create overlapping booking (starts during existing booking)
        $overlapStart = now()->addDay()->setTime(10, 30);
        $overlapEnd = now()->addDay()->setTime(11, 30);

        $response = $this->actingAs($this->user)
            ->postJson('/api/bookings', [
                'user_id' => $this->user->id,
                'title' => 'Overlapping Booking',
                'client_id' => $this->client->id,
                'start_time' => $overlapStart->toISOString(),
                'end_time' => $overlapEnd->toISOString(),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start_time']);
    }

    public function test_booking_allows_non_overlapping_bookings(): void
    {
        // Create initial booking
        $startTime = now()->addDay()->setTime(10, 0);
        $endTime = now()->addDay()->setTime(11, 0);

        Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Create non-overlapping booking (after existing booking)
        $nonOverlapStart = now()->addDay()->setTime(11, 30);
        $nonOverlapEnd = now()->addDay()->setTime(12, 30);

        $response = $this->actingAs($this->user)
            ->postJson('/api/bookings', [
                'user_id' => $this->user->id,
                'title' => 'Non-overlapping Booking',
                'client_id' => $this->client->id,
                'start_time' => $nonOverlapStart->toISOString(),
                'end_time' => $nonOverlapEnd->toISOString(),
            ]);

        $response->assertStatus(201);
    }

    public function test_index_returns_all_bookings_with_relationships(): void
    {
        $booking1 = Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(1),
        ]);

        $booking2 = Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(1),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/bookings');

        $response->assertStatus(200);

        $bookings = $response->json('data');
        $this->assertIsArray($bookings);
        $this->assertGreaterThanOrEqual(2, count($bookings));

        // Verify relationships are loaded
        $this->assertArrayHasKey('client', $bookings[0]);
        $this->assertArrayHasKey('user', $bookings[0]);
    }

    public function test_weekly_bookings_api_returns_bookings_for_specific_week(): void
    {
        $weekDate = Carbon::parse('2025-08-05'); // Tuesday
        $mondayStart = $weekDate->copy()->startOfWeek(Carbon::MONDAY);
        $sundayEnd = $weekDate->copy()->endOfWeek(Carbon::SUNDAY);

        // Create booking within the week
        $bookingInWeek = Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'start_time' => $mondayStart->copy()->addDays(2)->setTime(10, 0),
            'end_time' => $mondayStart->copy()->addDays(2)->setTime(11, 0),
        ]);

        // Create booking outside the week
        $bookingOutsideWeek = Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'start_time' => $mondayStart->copy()->addWeeks(2)->setTime(10, 0),
            'end_time' => $mondayStart->copy()->addWeeks(2)->setTime(11, 0),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/bookings?week=2025-08-05');

        $response->assertStatus(200);

        $bookings = $response->json('bookings');
        $weekStart = $response->json('week_start');
        $weekEnd = $response->json('week_end');

        $this->assertCount(1, $bookings);
        $this->assertEquals($bookingInWeek->id, $bookings[0]['id']);
        $this->assertNotNull($weekStart);
        $this->assertNotNull($weekEnd);

        // Verify relationships are loaded
        $this->assertArrayHasKey('client', $bookings[0]);
        $this->assertArrayHasKey('user', $bookings[0]);
    }

    public function test_customer_can_update_their_booking(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/bookings/{$booking->id}", [
                'user_id' => $this->user->id,
                'title' => 'Updated Booking',
                'client_id' => $this->client->id,
                'start_time' => now()->addDays(5)->toISOString(),
                'end_time' => now()->addDays(5)->addHours(2)->toISOString(),
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Booking',
                    'client' => [
                        'id' => $this->client->id,
                    ],
                    'user' => [
                        'id' => $this->user->id,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'title' => 'Updated Booking',
        ]);
    }

    public function test_customer_can_delete_their_booking(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
    }

    public function test_authenticated_user_can_access_any_booking(): void
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create();
        $otherBooking = Booking::factory()->create([
            'user_id' => $otherUser->id,
            'client_id' => $otherClient->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/bookings/{$otherBooking->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $otherBooking->id,
                    'user_id' => $otherUser->id,
                    'client_id' => $otherClient->id,
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_bookings(): void
    {
        $response = $this->getJson('/api/bookings');
        $response->assertStatus(401);
    }
}
