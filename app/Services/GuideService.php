<?php

namespace App\Services;

use App\Enums\ChannelTVShowCount;
use App\Models\Guide;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GuideService
{
    public function getAdjustedGuide(int $channelNr, string $date): ?Collection
    {
        $cacheKey = "guide_channel_{$channelNr}_date_{$date}";

        $tvDayEnd = Carbon::parse($date)->addDay()->setTime(6, 0, 0);

        return Cache::remember($cacheKey, 86400, function () use ($channelNr, $date, $tvDayEnd) {
            $guides = Guide::forTvDay($date)
                ->where('channel_nr', $channelNr)
                ->oldest('starts_at')
                ->get();

            $adjustedGuides = $this->adjustEndTimes($guides);

            $lastShow = $adjustedGuides->last();

            // Check if the last show's start time is after new TV day end time, if so, remove it from the list
            if ($lastShow && Carbon::parse($lastShow->starts_at)->gte($tvDayEnd)) {
                $adjustedGuides->pop();
            }

            return $adjustedGuides;
        });
    }

    public function getCurrentAndUpcomingGuides(int $channelNr, int $limit = ChannelTVShowCount::upcoming->value): ?Collection
    {
        $cacheKey = "upcoming_guides_channel_{$channelNr}_limit_{$limit}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $currentShowStart = Guide::select('starts_at')
            ->where('channel_nr', $channelNr)
            ->where('starts_at', '<=', now())
            ->latest('starts_at')
            ->limit(1);

        $guides = Guide::where('channel_nr', $channelNr)
            ->where('starts_at', '>=', $currentShowStart)
            ->oldest('starts_at')
            ->limit($limit + 1) // add 1 to include more extra show, so can caluclate last show's end time
            ->get();

        if ($guides->isEmpty() || ($guides->count() === 1 && $guides[0]->ends_at <= now())) {
            Cache::put($cacheKey, null, 60);

            return null;
        }

        $adjustedGuides = $this->adjustEndTimes($guides)->take($limit);

        $currentShow = $adjustedGuides->first();
        $endsAt = Carbon::parse($currentShow->ends_at);
        $secondsUntilNextShow = now()->diffInSeconds($endsAt, false);

        Cache::put($cacheKey, $adjustedGuides, $secondsUntilNextShow);

        return $adjustedGuides;
    }

    public function adjustEndTimes(Collection $guides): Collection
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
