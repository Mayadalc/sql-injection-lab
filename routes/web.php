<?php

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/vulnerable/search');

Route::get('/vulnerable/search', [LabController::class, 'vulnerable']);
Route::get('/secure/search', [LabController::class, 'secure']);

// Brute-force lab: vulnerable endpoint has no rate limit.
Route::get('/brute-force', [LabController::class, 'bruteForcePage'])
    ->name('bruteforce.vulnerable');
Route::post('/brute-force/login', [LabController::class, 'vulnerableLogin']);

// Protected endpoint: maximum 5 requests per minute per IP.
Route::get('/brute-force/secure', [LabController::class, 'secureBruteForcePage'])
    ->name('bruteforce.secure');
Route::post('/brute-force/secure/login', [LabController::class, 'secureLogin'])
    ->middleware('throttle:5,1');
