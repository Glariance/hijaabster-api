
<?php
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/login');

Route::get('/media/{path}', function (string $path) {
    // Clean the path - remove leading slashes
    $path = ltrim($path, '/');
    
    // File path in public/storage directory (where files are actually stored)
    // Based on your project structure: scarf-api/public/storage/cms_fields
    $publicFilePath = public_path('storage/' . $path);
    
    // Check if file exists in public/storage directory first
    if (file_exists($publicFilePath) && is_file($publicFilePath)) {
        $file = $publicFilePath;
        $mimeType = mime_content_type($file) ?: 'application/octet-stream';
    } else {
        // Fallback: check in storage/app/public directory (Laravel standard location)
        if (!Storage::disk('public')->exists($path)) {
            Log::warning("Media file not found: {$path} in both public/storage and storage/app/public");
            abort(404, "File not found: {$path}");
        }
        $file = Storage::disk('public')->path($path);
        // Use mime_content_type as fallback since mimeType() might not be available
        $mimeType = mime_content_type($file) ?: 'application/octet-stream';
    }
    
    return response()->file($file, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('media.asset');

Route::get('/', function (Request $request, ProductController $controller) {
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
