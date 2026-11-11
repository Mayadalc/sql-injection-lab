<?php

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/vulnerable/search');

// الصفحة الضعيفة (قبل الإصلاح)
Route::get('/vulnerable/search', [LabController::class, 'vulnerable']);

// الصفحة المصلحة (بعد الإصلاح)
Route::get('/secure/search', [LabController::class, 'secure']);
