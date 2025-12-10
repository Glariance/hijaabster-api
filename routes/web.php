<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/login');

Route::get('/media/{path}', function (string $path) {
    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('media.asset');

Route::get('/', function (Request $request, PrivacyController $controller) {
    $response = $controller->show($request);

    dd($response->getData(true));
});

Route::middleware(['auth', 'role:'.config('constants.USER')])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
