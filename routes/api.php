<?php

use App\Http\Controllers\GuideController;
use Illuminate\Support\Facades\Route;

Route::get('/guide/{channel_nr}/{date}', [GuideController::class, 'index']);
