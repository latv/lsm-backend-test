<?php

use App\Http\Controllers\GuideController;
use Illuminate\Support\Facades\Route;

Route::get('/guide/{channel_nr}/{date}', [GuideController::class, 'channelGuideByDate']);
Route::get('/on-air/{channel_nr}', [GuideController::class, 'currentGuide']);
Route::get('/upcoming/{channel_nr}', [GuideController::class, 'upcomingGuides']);
Route::post('/guide', [GuideController::class, 'store']);
