<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $model  Model name to check (e.g., 'absence', 'overtime')
     */
    public function handle(Request $request, Closure $next, ?string $model = null): Response
    {
        $user = $request->user();

        // Check if user can manage all sections (has appropriate permissions)
        if ($user && $user->canManageAllSections()) {
            return $next($request);
        }

        // Check if user has a section assigned
        if (!$user || !$user->section_id) {
            abort(403, 'You do not have access to any section.');
        }

        // If checking specific resource (like absence/1, overtime/5)
        if ($model && $request->route()) {
            $resourceId = $request->route()->parameter($model);
            
            if ($resourceId) {
                $modelClass = $this->getModelClass($model);
                
                if ($modelClass) {
                    $resource = $modelClass::find($resourceId);
                    
                    if ($resource && !$user->canAccessSection($resource->section_id)) {
                        abort(403, 'You do not have access to this resource.');
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Get model class from model name
     */
    private function getModelClass(string $model): ?string
    {
        $models = [
            'absence' => \App\Models\Absence::class,
            'overtime' => \App\Models\Overtime::class,
            'business_trip' => \App\Models\BusinessTrip::class,
            'consumable' => \App\Models\Consumable::class,
        ];

        return $models[$model] ?? null;
    }
}
