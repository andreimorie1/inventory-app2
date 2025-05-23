<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;

Route::post('/login', [StaffController::class, 'login'])->middleware('session');
Route::post('/logout', [StaffController::class, 'logout'])->middleware('session');
Route::post('/register', [StaffController::class, 'register']);

Route::middleware(['session', 'login.required'])->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);          
    Route::get('{id}', [ProductController::class, 'show']);        
    Route::post('/', [ProductController::class, 'store']);         
    Route::put('{id}', [ProductController::class, 'update']);      
    Route::patch('{id}/reduce-stock', [ProductController::class, 'reduceStock']);
    Route::patch('{id}/add-stock', [ProductController::class, 'addStock']);
    Route::delete('{id}', [ProductController::class, 'destroy']); 
});