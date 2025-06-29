<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('/transactions', TransactionController::class)
    ->only(['index', 'show', 'store']);

Route::apiResource('/cart', CartController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::apiResource('products', ProductController::class)->only([
    'index',
    'show',
]);
