<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\PromotionsController;
use App\Http\Controllers\Api\BundleController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\TermsController;
use App\Http\Controllers\Api\AccessibilityController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
// Route::get('/products/page', [ProductController::class, 'show']);
// Route::get('/pets/page', [PetController::class, 'show']);
Route::get('/contact/page', [ContactController::class, 'show']);
Route::get('/privacy', [PrivacyController::class, 'show']);
Route::get('/terms', [TermsController::class, 'show']);
Route::get('/accessibility', [AccessibilityController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/home', [HomeController::class, 'show']);
Route::get('/promotions', [PromotionsController::class, 'show']);
Route::get('/shop', [ShopController::class, 'show']);
Route::get('/bundles', [BundleController::class, 'index']);

// Cart
Route::get('/cart', [CartController::class, 'index']);
Route::get('/cart/page', [CartController::class, 'show']);
Route::post('/cart', [CartController::class, 'store']);
Route::post('/cart/summary', [CartController::class, 'summary']);
Route::put('/cart/items/{productId}', [CartController::class, 'update']);
Route::delete('/cart/items/{productId}', [CartController::class, 'destroy']);

// Checkout (CMS + config)
Route::get('/checkout', [CheckoutController::class, 'index']);
Route::get('/checkout/page', [CheckoutController::class, 'show']);

// Orders (place order + track; index/show require auth)
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/track', [OrderController::class, 'track']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me']);
Route::put('/me', [AuthController::class, 'updateProfile']);
Route::post('/me', [AuthController::class, 'updateProfile']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Preflight handler for CORS
Route::options('/{any}', function () {
    return response()->noContent();
})->where('any', '.*');
