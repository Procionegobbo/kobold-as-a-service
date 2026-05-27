<?php

use App\Http\Controllers\KoboldGeneratorController;
use Illuminate\Support\Facades\Route;

Route::post('/generate-kobold', [KoboldGeneratorController::class, 'generate'])
    ->middleware('throttle:kobold-api')
    ->name('kobold.generate');
