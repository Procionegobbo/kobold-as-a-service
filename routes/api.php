<?php

use App\Http\Controllers\KoboldGeneratorController;
use Illuminate\Support\Facades\Route;

Route::post('/generate-kobold', [KoboldGeneratorController::class, 'generate'])
    ->name('kobold.generate');
