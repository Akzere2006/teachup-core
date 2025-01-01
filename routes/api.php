<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/profile', [UserController::class, 'getProfile']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/offers', [OfferController::class, 'create']);
    Route::put('/offers/{id}', [OfferController::class, 'update']);
    Route::delete('/offers/{id}', [OfferController::class, 'delete']);
    Route::get('/offers/{id}', [OfferController::class, 'get']);
    Route::get('/offers', [OfferController::class, 'list']);
    Route::get('/my-offers', [OfferController::class, 'listMyOffers']);
});

Route::prefix('schedules')->group(function () {
    Route::post('/', [ScheduleController::class, 'create']);
    Route::put('/{id}', [ScheduleController::class, 'update']);
    Route::get('/{id}', [ScheduleController::class, 'get']);
    Route::delete('/{id}', [ScheduleController::class, 'delete']);
    Route::get('/offer/{offerId}', [ScheduleController::class, 'listByOfferId']);
});

Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'create']);
    Route::put('/{id}', [BookingController::class, 'update']);
    Route::delete('/{id}', [BookingController::class, 'delete']);
    Route::get('/offer/{offerId}', [BookingController::class, 'getAvailableBookingsByOffer']);
    Route::get('/my-bookings', [BookingController::class, 'getMyBookings']);
});
