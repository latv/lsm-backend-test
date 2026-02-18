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

    public function getCurrentAndUpcomingGuides(int $channelNr, int $limit = 10): Collection
    {
        $currentShowStart = Guide::select('starts_at')
            ->where('channel_nr', $channelNr)
            ->where('starts_at', '<=', now())
            ->orderBy('starts_at', 'desc')
            ->limit(1);

        $guides = Guide::where('channel_nr', $channelNr)
            ->where('starts_at', '>=', $currentShowStart)
            ->orderBy('starts_at', 'asc')
            ->limit($limit + 1) // added '+1' so can adjust end time of current show to next show's start
            ->get();

        if ($guides->isEmpty()) {
            return null;
        }

        return $this->adjustEndTimes($guides)->take($limit);
    }
}
