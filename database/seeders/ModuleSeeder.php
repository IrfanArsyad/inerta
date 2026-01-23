<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleGroup;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Create module groups
        $groups = [
            [
                'id' => 1,
                'name' => 'settings',
                'label' => 'Settings',
                'icon' => 'ri-settings-3-line',
                'order' => 1,
            ],
        ];

        foreach ($groups as $group) {
            ModuleGroup::updateOrCreate(
                ['id' => $group['id']],
                $group
            );
        }

        // Create modules
        $modules = [
            [
                'id' => 1,
                'module_group_id' => 1,
                'permission' => 'user-management',
                'name' => 'user-management',
                'label' => 'User Management',
                'icon' => 'ri-user-line',
                'url' => '/users',
                'route_name' => 'users.index',
                'order' => 1,
            ],
            [
                'id' => 2,
                'module_group_id' => 1,
                'permission' => 'role-permission',
                'name' => 'role-permission',
                'label' => 'Role & Permission',
                'icon' => 'ri-shield-check-line',
                'url' => '/roles',
                'route_name' => 'roles.index',
                'order' => 2,
            ],
            [
                'id' => 3,
                'module_group_id' => 1,
                'permission' => 'module-management',
                'name' => 'module-management',
                'label' => 'Module Management',
                'icon' => 'ri-apps-line',
                'url' => '/modules',
                'route_name' => 'modules.index',
                'order' => 3,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['id' => $module['id']],
                $module
            );
        }
    }
}
