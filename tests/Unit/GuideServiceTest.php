<?php

namespace Tests\Unit;

use App\Services\GuideService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class GuideServiceTest extends TestCase
{
    public function test_it_adjusts_end_times_to_match_next_show_start_time()
    {
        $shows = new Collection([
            (object) ['title' => 'Show 1', 'starts_at' => '2026-02-21 10:00:00', 'ends_at' => '2026-02-21 10:40:00'],
            (object) ['title' => 'Show 2', 'starts_at' => '2026-02-21 10:45:00', 'ends_at' => '2026-02-21 11:59:00'],
            (object) ['title' => 'Show 3', 'starts_at' => '2026-02-21 12:00:00', 'ends_at' => '2026-02-21 13:00:00'],
        ]);

        $service = new GuideService;

        $adjustedShows = $service->adjustEndTimes($shows);

        $this->assertEquals('2026-02-21 10:45:00', $adjustedShows[0]->ends_at);
        $this->assertEquals('2026-02-21 12:00:00', $adjustedShows[1]->ends_at);
        $this->assertEquals('2026-02-21 13:00:00', $adjustedShows[2]->ends_at);
    }
}
