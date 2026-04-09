<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    /**
     * Ten middleware przechwytuje wyjątki i zamienia je na jednolite JSON-y.
     * Podobne do ExceptionHandlingMiddleware w .NET.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => 'Przesłane dane są nieprawidłowe.',
                'errors' => $e->errors()
            ], 422);
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'Szukany zasób nie istnieje.'
            ], 404);
        } catch (Throwable $e) {
            // Logujemy błąd wewnętrzny (opcjonalnie)
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
    }
}
