<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request, int $channel_nr, string $date): JsonResponse
    {
        $channelProgramm = Guide::query()
            ->where('channel_nr', $channel_nr)
            ->forTvDay($date)
            ->get();

        return response()->json([
            'date' => $date,
            'channel_nr' => $channel_nr,
            'channel_programm' => $channelProgramm,
        ]);
    }
}
