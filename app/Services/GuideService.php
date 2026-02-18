<?php

namespace App\Services;

use App\Models\Guide;
use Illuminate\Database\Eloquent\Collection;

class GuideService
{
    public function getAdjustedGuide(int $channelNr, string $date): Collection
    {
        $guides = Guide::forTvDay($date)
            ->where('channel_nr', $channelNr)
            ->orderBy('starts_at', 'asc')
            ->get();

        return $this->adjustEndTimes($guides);
    }

    private function adjustEndTimes(Collection $guides): Collection
    {
        $guides = $guides->values();
        $count = $guides->count();

        return $guides->map(function ($guide, $key) use ($guides, $count) {
            if ($key < $count - 1) {
                $nextShow = $guides[$key + 1];
                $guide->ends_at = $nextShow->starts_at;
            }

            return $guide;
        });
    }
}
