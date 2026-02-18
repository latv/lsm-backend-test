<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;

class UpcomingGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentStart = now();

        $programs = [
            ['title' => 'Rīta Panorāma', 'minutes' => 30],
            ['title' => '4. studija', 'minutes' => 25],
            ['title' => 'Dienas ziņas', 'minutes' => 20],
            ['title' => 'Kultūršoks', 'minutes' => 45],
            ['title' => 'Aizliegtais paņēmiens', 'minutes' => 50],
            ['title' => 'Panorāma', 'minutes' => 60],
            ['title' => 'Sporta studija', 'minutes' => 30],
            ['title' => 'Viens pret vienu', 'minutes' => 55],
            ['title' => 'De facto', 'minutes' => 40],
        ];

        $guides = [];

        foreach ($programs as $program) {
            $scheduledEnd = $currentStart->copy()->addMinutes($program['minutes']);

            $actualEnd = $scheduledEnd->copy()->subSeconds(rand(10, 59));

            $guides[] = [
                'title' => $program['title'],
                'channel_nr' => 1,
                'starts_at' => $currentStart,
                'ends_at' => $actualEnd,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $currentStart = $scheduledEnd;
        }

        Guide::insert($guides);
    }
}
