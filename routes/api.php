<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DiscountController;

Route::prefix('discounts')->group(function () {
    Route::get('/', [DiscountController::class, 'index']); // Lists discount
    Route::post('/store', [DiscountController::class, 'store']); // Create discount
    Route::post('/apply', [DiscountController::class, 'applyDiscount']); // Apply discount
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
