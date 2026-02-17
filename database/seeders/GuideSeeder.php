<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guide;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Guide::insert([
        [
            'title' => 'Panorāma',
            'channel_nr' => 1,
            'starts_at' => '2024-01-01 20:00:00',
            'ends_at' => '2024-01-01 20:36:00',
        ],
        [
            'title' => 'Šodienas jautājums',
            'channel_nr' => 1,
            'starts_at' => '2024-01-01 20:37:00',
            'ends_at' => '2024-01-01 20:56:00',
        ],
        [
            'title' => 'Sporta ziņas',
            'channel_nr' => 1,
            'starts_at' => '2024-01-01 20:56:10',
            'ends_at' => '2024-01-01 21:02:00',
        ],
        ]);
    }
}
