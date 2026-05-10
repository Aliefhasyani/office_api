<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/login/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('/login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('/employees', EmployeeController::class);

    Route::get('/leaves', [LeaveController::class, 'index']);
    
    Route::middleware('role:employee')->group(function () {
        Route::post('/leaves', [LeaveController::class, 'store']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::patch('/leaves/{id}/status', [LeaveController::class, 'updateStatus']);
    });
});
