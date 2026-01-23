<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Admin',
                'description' => 'Full access to all modules',
                'read' => ['*'],
                'create' => ['*'],
                'update' => ['*'],
                'delete' => ['*'],
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Limited admin access',
                'read' => [1, 2, 3],
                'create' => [1],
                'update' => [1],
                'delete' => [],
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Basic user access',
                'read' => [],
                'create' => [],
                'update' => [],
                'delete' => [],
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
