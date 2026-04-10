<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage: middleware('role:admin,gestionnaire')
     *
     * Handles both variadic args AND comma-separated strings
     * to be safe across all Laravel versions.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Flatten: 'admin,gestionnaire' → ['admin', 'gestionnaire']
        $allowed = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowed[] = trim($r);
            }
        }

        if (! $request->user() || ! in_array($request->user()->role, $allowed)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}