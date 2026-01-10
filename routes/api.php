<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;



// Route::get('/categories', [CategoryController::class, 'index']);
// Route::get('/products', [ProductController::class, 'index']);
// Route::get('/products/featured', [ProductController::class, 'featured']);
// Route::get('/products/page', [ProductController::class, 'show']);
// Route::get('/pets/page', [PetController::class, 'show']);
Route::get('/contact/page', [ContactController::class, 'show']);
Route::get('/privacy', [PrivacyController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/home', [HomeController::class, 'show']);
// Route::get('/about', [AboutController::class, 'show']);
// Route::post('/newsletter', [NewsLetterController::class, 'subscribe']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Preflight handler for CORS
Route::options('/{any}', function () {
    return response()->noContent();
})->where('any', '.*');
