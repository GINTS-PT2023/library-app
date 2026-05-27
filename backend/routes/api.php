<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('books', BookController::class);
Route::apiResource('readers', ReaderController::class);
Route::get('rentals/overdue', [RentalController::class, 'overdue']);
Route::apiResource('rentals', RentalController::class);
