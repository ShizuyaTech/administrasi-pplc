<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more permission slugs (comma-separated in route definition).
     * Access is granted if the user has AT LEAST ONE of the listed permissions (OR logic).
     *
     * Usage: ->middleware('permission:view-users')
     *        ->middleware('permission:approve-overtime-supervisor,approve-overtime-manager')
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Anda harus login untuk mengakses halaman ini.');
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
