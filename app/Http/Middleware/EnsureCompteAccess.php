<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Compte;
use App\Exceptions\CompteNotFoundException;

class EnsureCompteAccess
{
    /**
     * Handle an incoming request.
     * If user is Admin => allow. If user is Client => ensure client_id matches user's client id or email.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $compte = $request->route('compte');
        if (!$compte || !$compte instanceof Compte) {
            throw new CompteNotFoundException();
        }

        // Admin can access any
        if ($user && ($user->role ?? null) === 'Admin') {
            return $next($request);
        }

        // For client role, ensure ownership: match client_id to user->id or match email
        if ($user) {
            // if our User is linked to a client id
            if (isset($user->client_id) && $user->client_id === $compte->client_id) {
                return $next($request);
            }

            // fallback: if user's email matches compte's client email
            $client = $compte->client;
            if ($client && $client->email === ($user->email ?? null)) {
                return $next($request);
            }
        }

        // not authorized to view
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'FORBIDDEN',
                'message' => 'Accès refusé au compte demandé',
            ],
        ], 403);
    }
}
