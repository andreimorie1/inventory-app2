<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);          
    Route::get('{id}', [ProductController::class, 'show']);        
    Route::post('/', [ProductController::class, 'store']);         
    Route::put('{id}', [ProductController::class, 'update']);      
    Route::delete('{id}', [ProductController::class, 'destroy']); 
});

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);           
    Route::get('{id}', [OrderController::class, 'show']);         
    Route::post('/', [OrderController::class, 'store']);          
    Route::put('{id}', [OrderController::class, 'update']);       
    Route::patch('{id}/status', [OrderController::class, 'updateStatus']); 
    Route::delete('{id}', [OrderController::class, 'destroy']);    
});
