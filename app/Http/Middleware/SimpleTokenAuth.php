<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleTokenAuth
{
    /**
     * Handle an incoming request.
     * Ten middleware demonstruje wiedzę o tym, jak działają tokeny (np. JWT).
     * W prawdziwej aplikacji użylibyśmy Laravel Sanctum lub tymon/jwt-auth, 
     * ale tutaj pokazujemy ręczną obsługę nagłówka Bearer.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        // Uproszczona walidacja: w celach demo akceptujemy token "secret-token-123"
        // lub bazowy mechanizm sprawdzający zakodowane ID: "user-id-1"
        if (!$token || ($token !== 'secret-token-123' && strpos($token, 'user-id-') !== 0)) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Brak lub nieprawidłowy token Bearer. Użyj: "secret-token-123"'
            ], 401);
        }

        // Symulacja zalogowania użytkownika wyciągniętego z tokena
        if (strpos($token, 'user-id-') === 0) {
            $id = str_replace('user-id-', '', $token);
            $user = User::find($id);
            if ($user) {
                auth()->login($user);
            }
        } else {
            // Domyślny user dla secret-token-123
            $user = User::first();
            if ($user) {
                auth()->login($user);
            }
        }

        return $next($request);
    }
}
