<?php

namespace Database\Seeders;

use App\Enums\Channel;
use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::today();
        $daysToSeed = 3;

        $showsCh1 = [
            ['title' => 'Rīta Panorāma', 'start_hour' => 6, 'start_min' => 30, 'duration_min' => 150],
            ['title' => 'Dienas ziņas', 'start_hour' => 18, 'start_min' => 0, 'duration_min' => 30],
            ['title' => 'Kultūras ziņas', 'start_hour' => 18, 'start_min' => 30, 'duration_min' => 15],
            ['title' => 'Panorāma', 'start_hour' => 20, 'start_min' => 0, 'duration_min' => 36],
            ['title' => 'Šodienas jautājums', 'start_hour' => 20, 'start_min' => 37, 'duration_min' => 19],
            ['title' => 'Sporta ziņas', 'start_hour' => 20, 'start_min' => 56, 'duration_min' => 10],
        ];

        $showsCh2 = [
            ['title' => 'Sporta studija', 'start_hour' => 10, 'start_min' => 0, 'duration_min' => 60],
            ['title' => 'Ķepa uz sirds', 'start_hour' => 15, 'start_min' => 30, 'duration_min' => 30],
            ['title' => 'Province', 'start_hour' => 19, 'start_min' => 0, 'duration_min' => 45],
            ['title' => 'Aizliegtais paņēmiens', 'start_hour' => 21, 'start_min' => 0, 'duration_min' => 60],
            ['title' => 'Hokejs: Virslīga', 'start_hour' => 22, 'start_min' => 0, 'duration_min' => 120],
        ];

        $records = [];

        for ($i = 0; $i < $daysToSeed; $i++) {
            $currentDate = $startDate->copy()->addDays($i);

            // Populate Channel 1 (LTV1)
            foreach ($showsCh1 as $show) {
                $start = $currentDate->copy()->setTime($show['start_hour'], $show['start_min'], 0);
                // Subtract 5 to 10 seconds from the end time
                $end = $start->copy()->addMinutes($show['duration_min'])->subSeconds(rand(5, 10));

                $records[] = [
                    'title' => $show['title'],
                    'channel_nr' => Channel::LTV1->value,
                    'starts_at' => $start->format('Y-m-d H:i:s'),
                    'ends_at' => $end->format('Y-m-d H:i:s'),
                ];
            }

            foreach ($showsCh2 as $show) {
                $start = $currentDate->copy()->setTime($show['start_hour'], $show['start_min'], 0);
                // Subtract 5 to 10 seconds to ensure starts_at is after the ends_at
                $end = $start->copy()->addMinutes($show['duration_min'])->subSeconds(rand(5, 10));

                $records[] = [
                    'title' => $show['title'],
                    'channel_nr' => Channel::LTV2->value,
                    'starts_at' => $start->format('Y-m-d H:i:s'),
                    'ends_at' => $end->format('Y-m-d H:i:s'),
                ];
            }
        }

        Guide::insert($records);
    }
}
