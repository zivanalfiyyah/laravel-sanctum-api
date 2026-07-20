<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TicketTypeController;
use App\Http\Controllers\Api\RegistrationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/events', [EventController::class, 'index']);

    Route::post('/users', [UserController::class, 'store']);
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/ticketTypes', [TicketTypeController::class, 'store']);
    Route::post('/registrations', [RegistrationController::class, 'store']);

    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});