<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('surgeries', \App\Http\Controllers\SurgeryController::class);
    Route::post('surgeries/{surgery}/confirm', [\App\Http\Controllers\SurgeryController::class, 'confirm']);
    Route::post('surgeries/{surgery}/cancel', [\App\Http\Controllers\SurgeryController::class, 'cancel']);
});
