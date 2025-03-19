<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\StudentAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// routes/api.php
Route::group(['prefix' => 'students'], function () {
    Route::post('register', [StudentAuthController::class, 'register']);
    Route::post('login', [StudentAuthController::class, 'login']);
    Route::post('logout', [StudentAuthController::class, 'logout']);
    Route::post('refresh', [StudentAuthController::class, 'refresh']);
    Route::get('me', [StudentAuthController::class, 'me']);
});

Route::group(['prefix' => 'students'], function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout']);
    Route::post('refresh', [AdminAuthController::class, 'refresh']);
    Route::get('me', [AdminAuthController::class, 'me']);
});



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
