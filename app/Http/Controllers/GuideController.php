<?php

namespace App\Http\Controllers;

use App\Services\GuideService;
use Illuminate\Http\JsonResponse;

class GuideController extends Controller
{
    public function __construct(protected GuideService $guideService) {}

    public function channelGuideByDate(int $channel_nr, string $date): JsonResponse
    {
        if (! strtotime($date)) {
            return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
        }

        $guide = $this->guideService->getAdjustedGuide($channel_nr, $date);

        return response()->json([
            'data' => $guide,
        ]);
    }
}
