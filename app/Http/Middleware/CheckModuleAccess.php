<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Check if user can READ (access) the module
     * Usage: middleware('module.access:1')
     */
    public function handle(Request $request, Closure $next, int $moduleId): Response
    {
        if (!session()->has('auth.user')) {
            return redirect()->route('login');
        }

        if (!can_read($moduleId)) {
            abort(403, "You don't have access to this module.");
        }

        return $next($request);
    }
}
