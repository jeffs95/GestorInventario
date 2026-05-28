<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $userSlugs = $request->user()->roles->pluck('slug')->toArray();

        foreach ($roles as $role) {
            if (in_array($role, $userSlugs)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
