<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attempt(array $credentials, bool $remember = false): bool
    {
        $user = User::where('email', $credentials['email'])
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        Auth::login($user, $remember);

        $this->loadUserSession($user);

        $user->update(['last_login_at' => now()]);

        return true;
    }

    public function loadUserSession(User $user): void
    {
        $user->load('role');

        $permissions = $user->getPermissions();
        $modules = $this->getAccessibleModules($permissions['read']);
        $menu = $this->buildMenu($permissions['read']);

        $userSessionData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'display_name' => $user->role->display_name,
                ] : null,
            ],
            'permissions' => $permissions,
            'modules' => $modules,
            'menu' => $menu,
        ];

        session(['auth' => $userSessionData]);
    }

    protected function getAccessibleModules(array $readableIds): array
    {
        $query = Module::where('active', true)->orderBy('order');

        if (!in_array('*', $readableIds, true)) {
            if (empty($readableIds)) {
                return [];
            }
            $query->whereIn('id', $readableIds);
        }

        return $query->get()->toArray();
    }

    protected function buildMenu(array $readableIds): array
    {
        // Load all modules with children in single query
        $groups = ModuleGroup::where('active', true)
            ->orderBy('order')
            ->with(['modules' => function ($query) use ($readableIds) {
                $query->where('active', true)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->with(['children' => function ($q) use ($readableIds) {
                        $q->where('active', true)->orderBy('order');
                        if (!in_array('*', $readableIds, true)) {
                            $q->whereIn('id', $readableIds);
                        }
                    }]);

                if (!in_array('*', $readableIds, true)) {
                    $query->whereIn('id', $readableIds);
                }
            }])
            ->get();

        $menu = [];

        foreach ($groups as $group) {
            if ($group->modules->isEmpty()) {
                continue;
            }

            $menu[] = [
                'id' => $group->id,
                'name' => $group->name,
                'label' => $group->label,
                'icon' => $group->icon,
                'items' => $group->modules->map(function ($module) {
                    $item = [
                        'id' => $module->id,
                        'name' => $module->name,
                        'label' => $module->label,
                        'icon' => $module->icon,
                        'url' => $module->url,
                        'route' => $module->route_name,
                        'external' => $module->external,
                        'badge_source' => $module->badge_source,
                    ];

                    if ($module->children->isNotEmpty()) {
                        $item['children'] = $module->children->map(fn ($child) => [
                            'id' => $child->id,
                            'name' => $child->name,
                            'label' => $child->label,
                            'icon' => $child->icon,
                            'url' => $child->url,
                            'route' => $child->route_name,
                            'external' => $child->external,
                        ])->toArray();
                    }

                    return $item;
                })->toArray(),
            ];
        }

        return $menu;
    }

    public function logout(): void
    {
        session()->forget('auth');
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
