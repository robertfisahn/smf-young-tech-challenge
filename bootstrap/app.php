<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Features\Auth\SimpleTokenAuth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Rejestrujemy nasz middleware z nowej lokalizacji
        $middleware->alias([
            'simple.token' => SimpleTokenAuth::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Globalna obsługa wyjątków dla API (zamiast middleware)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Validation Error',
                    'message' => 'Przesłane dane są nieprawidłowe.',
                    'errors' => $e->errors()
                ], 422);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'Szukany zasób nie istnieje.'
                ], 404);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if (config('app.debug')) {
                    return response()->json([
                        'error' => 'Server Error',
                        'message' => $e->getMessage(),
                        'trace' => $e->getTrace()
                    ], 500);
                }

                return response()->json([
                    'error' => 'Internal Server Error',
                    'message' => 'Wystąpił nieoczekiwany błąd serwera.'
                ], 500);
            }
        });
    })->create();
