<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuideRequest;
use App\Models\Guide;
use App\Services\GuideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Enums\Channel;

class GuideController extends Controller
{
    public function __construct(protected GuideService $guideService) {}

    public function channelGuideByDate(int $channel_nr, string $date): JsonResponse
    {
        if (!in_array($channel_nr, Channel::values())) {
            return response()->json(['error' => 'Invalid channel number.'], 422);
        }

        if (! strtotime($date)) {
            return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
        }

        $cacheKey = "guide_channel_{$channel_nr}_date_{$date}";

        // cache for 1 hour
        $guide = Cache::remember($cacheKey, 3600, function () use ($channel_nr, $date) {
            return $this->guideService->getAdjustedGuide($channel_nr, $date);
        });

        return response()->json([
            'data' => $guide,
        ]);
    }

    public function currentGuide(int $channel_nr): JsonResponse
    {
        if (!in_array($channel_nr, Channel::values())) {
            return response()->json(['error' => 'Invalid channel number.'], 422);
        }

        $guide = $this->guideService->getCurrentAndUpcomingGuides($channel_nr, 1);

        if (! $guide) {
            return response()->json(['message' => 'No broadcast is currently on air.'], 404);
        }

        return response()->json([
            'data' => $guide,
        ]);
    }

    public function upcomingGuides(int $channel_nr): JsonResponse
    {
        $upcomingGuides = $this->guideService->getCurrentAndUpcomingGuides($channel_nr);

        if (! $upcomingGuides) {
            return response()->json(['message' => 'No upcoming broadcasts found.'], 404);
        }

        return response()->json([
            'data' => $upcomingGuides,
        ]);
    }

    public function store(StoreGuideRequest $request): JsonResponse
    {
        $guide = Guide::create($request->validated());

        return response()->json([
            'data' => $guide,
        ], 201);
    }
}
