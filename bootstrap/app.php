<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )

    // --------------------------
    // CSRF EXCEPTION MIDDLEWARE
    // --------------------------
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'admin/cms/section/image/upload', // Image upload endpoint (base64 fallback)
        ]);

    })

    // --------------------------
    // OTHER MIDDLEWARE
    // --------------------------
    ->withMiddleware(function (Middleware $middleware) {


        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('admin/*')
                ? route('admin.login')
                : route('login');
        });

        
        $middleware->redirectUsersTo(function (Request $request) {
            return $request->user()->isAdmin()
                ? 'admin/dashboard'
                : '/home';
        });

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                $response = response()->json([
                    'message' => $e->getMessage(),
                    'exception' => config('app.debug') ? get_class($e) : null,
                ], $status);
                return $response->withHeaders([
                    'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Accept',
                    'Access-Control-Allow-Credentials' => 'true',
                ]);
            }
            return null;
        });
    })
    ->create();
