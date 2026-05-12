<?php

declare(strict_types=1);



namespace App\Features\Auth;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

final class SimpleTokenAuth
{
    /**
     * Handle an incoming request.
     * 
     * Ten middleware demonstruje ręczną obsługę nagłówka Bearer (uproszczone API Auth).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token || ($token !== 'secret-token-123' && !str_starts_with($token, 'user-id-'))) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Brak lub nieprawidłowy token Bearer. Użyj: "secret-token-123" lub "user-id-{id}"'
            ], 401);
        }

        if (str_starts_with($token, 'user-id-')) {
            $id = (int) str_replace('user-id-', '', $token);
            $user = User::find($id);
            if ($user) {
                Auth::login($user);
            }
        } else {
            // Default user for secret-token-123
            $user = User::first();
            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}

