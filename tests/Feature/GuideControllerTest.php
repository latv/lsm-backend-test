<?php

namespace Tests\Feature;

use App\Models\Guide;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuideControllerTest extends TestCase
{
    use RefreshDatabase;

    protected int $validChannel = 1;

    public function test_can_create_a_guide_successfully(): void
    {
        $payload = [
            'title' => 'Morning News',
            'channel_nr' => $this->validChannel,
            'starts_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'ends_at' => Carbon::now()->addHour()->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/guide', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'channel_nr',
                    'starts_at',
                    'ends_at',
                ],
            ])
            ->assertJsonPath('data.title', 'Morning News');

        $this->assertDatabaseHas('guide', [
            'title' => 'Morning News',
            'channel_nr' => $this->validChannel,
        ]);
    }

    public function test_fails_to_create_guide_with_invalid_channel(): void
    {
        $payload = [
            'title' => 'Invalid Channel Show',
            'channel_nr' => 999,
            'starts_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'ends_at' => Carbon::now()->addHour()->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/guide', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('channel_nr');
    }

    public function test_fails_to_create_guide_with_ends_at_before_starts_at(): void
    {
        $payload = [
            'title' => 'Time Travel Show',
            'channel_nr' => $this->validChannel,
            'starts_at' => '2026-02-20 12:00:00',
            'ends_at' => '2026-02-20 10:00:00',
        ];

        $response = $this->postJson('/api/guide', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('ends_at');
    }

    public function test_fails_to_create_overlapping_guide(): void
    {
        Guide::create([
            'title' => 'First Show',
            'channel_nr' => $this->validChannel,
            'starts_at' => '2026-02-20 10:00:00',
            'ends_at' => '2026-02-20 12:00:00',
        ]);

        $payload = [
            'title' => 'Overlapping Show',
            'channel_nr' => $this->validChannel,
            'starts_at' => '2026-02-20 11:00:00',
            'ends_at' => '2026-02-20 13:00:00',
        ];

        $response = $this->postJson('/api/guide', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('starts_at');

        $this->assertEquals(
            'The time range overlaps with an existing entry on this channel.',
            $response->json('errors.starts_at.0')
        );
    }

    public function test_can_fetch_channel_guide_by_date(): void
    {
        $response = $this->getJson("/api/guide/{$this->validChannel}/2026-02-20");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_returns_422_for_invalid_channel_on_guide_by_date(): void
    {
        $response = $this->getJson('/api/guide/999/2026-02-20');

        $response->assertStatus(422)
            ->assertJson(['error' => 'Invalid channel number.']);
    }

    public function test_returns_400_for_invalid_date_format(): void
    {
        $response = $this->getJson("/api/guide/{$this->validChannel}/not-a-date");

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid date format. Use YYYY-MM-DD.']);
    }

    public function test_can_fetch_current_on_air_guide(): void
    {
        $response = $this->getJson("/api/on-air/{$this->validChannel}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'No broadcast is currently on air.']);
    }

    public function test_returns_422_for_invalid_channel_on_current_guide(): void
    {
        $response = $this->getJson('/api/on-air/999');

        $response->assertStatus(422)
            ->assertJson(['error' => 'Invalid channel number.']);
    }

    public function test_can_fetch_upcoming_guides(): void
    {
        $response = $this->getJson("/api/upcoming/{$this->validChannel}");

        $response->assertStatus(404)
            ->assertJson(['message' => 'No upcoming broadcasts found.']);
    }
}
