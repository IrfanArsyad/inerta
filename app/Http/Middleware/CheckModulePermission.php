<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    /**
     * Check specific permission on module
     * Usage: middleware('module.permission:1,create')
     */
    public function handle(Request $request, Closure $next, int $moduleId, string $action): Response
    {
        if (!session()->has('auth.user')) {
            return redirect()->route('login');
        }

        if (!can_access($action, $moduleId)) {
            abort(403, "You don't have {$action} permission for this module.");
        }

        return $next($request);
    }
}
