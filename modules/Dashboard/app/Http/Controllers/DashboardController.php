<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_roles' => Role::count(),
            'active_roles' => Role::where('active', true)->count(),
            'total_modules' => Module::count(),
            'active_modules' => Module::where('active', true)->count(),
        ];

        // Calculate total permissions assigned across all roles
        $totalPermissions = 0;
        Role::chunk(100, function ($roles) use (&$totalPermissions) {
            foreach ($roles as $role) {
                $read = is_array($role->read) ? (in_array('*', $role->read) ? Module::count() : count($role->read)) : 0;
                $create = is_array($role->create) ? (in_array('*', $role->create) ? Module::count() : count($role->create)) : 0;
                $update = is_array($role->update) ? (in_array('*', $role->update) ? Module::count() : count($role->update)) : 0;
                $delete = is_array($role->delete) ? (in_array('*', $role->delete) ? Module::count() : count($role->delete)) : 0;
                $totalPermissions += $read + $create + $update + $delete;
            }
        });
        $stats['total_permissions'] = $totalPermissions;

        $systemInfo = [
            'app_version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => App::version(),
            'vue_version' => '3.x',
        ];

        return Inertia::render('Dashboard::views/index', [
            'stats' => $stats,
            'systemInfo' => $systemInfo,
        ]);
    }
}
