# Laravel Modular Architecture with Inertia.js + Vue.js

## Overview

Architecture ini menggunakan pendekatan **Modular Monolith** dengan:
- **Laravel 11+** sebagai backend
- **Inertia.js** sebagai bridge antara Laravel dan Vue.js
- **Vue.js 3** dengan Composition API sebagai frontend
- **nwidart/laravel-modules** untuk modularisasi
- **Module-ID Based Permission** - Permission disimpan sebagai array module ID
- **Role-based Access Control** dengan granular CRUD per module

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11+ |
| Frontend | Vue.js 3 + Composition API |
| Bridge | Inertia.js |
| Styling | Tailwind CSS |
| State Management | Pinia |
| Module System | nwidart/laravel-modules |
| Auth | Laravel Session + Module-ID RBAC |

---

## Permission System Concept

### Struktur Permission dalam Role

```php
// Contoh data role
[
    'name' => 'Manager',
    'slug' => 'manager',
    'permissions' => [
        'read'   => [1, 2, 3],      // Bisa lihat module 1, 2, 3
        'create' => [1, 2],          // Bisa create di module 1, 2
        'update' => [1],             // Hanya bisa update di module 1
        'delete' => [],              // Tidak bisa delete apapun
    ]
]

// Full access menggunakan [*]
[
    'name' => 'Super Admin',
    'slug' => 'super-admin',
    'permissions' => [
        'read'   => ['*'],           // Full access read semua module
        'create' => ['*'],           // Full access create semua module
        'update' => ['*'],           // Full access update semua module
        'delete' => ['*'],           // Full access delete semua module
    ]
]
```

### Bagaimana Permission Bekerja

```
┌─────────────────────────────────────────────────────────────────┐
│                        MODULES TABLE                            │
├────┬──────────────────┬─────────────────────────────────────────┤
│ ID │ Name             │ Slug                                    │
├────┼──────────────────┼─────────────────────────────────────────┤
│ 1  │ User Management  │ user-management                         │
│ 2  │ Role & Permission│ role-permission                         │
│ 3  │ Dashboard        │ dashboard                               │
│ 4  │ Reports          │ reports                                 │
│ 5  │ Settings         │ settings                                │
└────┴──────────────────┴─────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    ROLE: Manager                                │
├─────────────────────────────────────────────────────────────────┤
│ read:   [1, 2, 3]  → Sidebar: User Mgmt, Role, Dashboard        │
│ create: [1]        → Tombol "Add" hanya di User Management      │
│ update: [1]        → Tombol "Edit" hanya di User Management     │
│ delete: []         → Tidak ada tombol "Delete" di manapun       │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    ROLE: Viewer                                 │
├─────────────────────────────────────────────────────────────────┤
│ read:   [1, 3]     → Sidebar: User Mgmt, Dashboard (no Role)    │
│ create: []         → Tidak ada tombol "Add" di manapun          │
│ update: []         → Tidak ada tombol "Edit" di manapun         │
│ delete: []         → Tidak ada tombol "Delete" di manapun       │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                 ROLE: Super Admin                               │
├─────────────────────────────────────────────────────────────────┤
│ read:   ['*']      → Sidebar: SEMUA module terlihat             │
│ create: ['*']      → Tombol "Add" di SEMUA module               │
│ update: ['*']      → Tombol "Edit" di SEMUA module              │
│ delete: ['*']      → Tombol "Delete" di SEMUA module            │
└─────────────────────────────────────────────────────────────────┘
```

### UI Behavior

```
┌─────────────────────────────────────────────────────────────────┐
│ User dengan read: [1,2] dan update: [1]                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  SIDEBAR                    │  CONTENT (Module 2)               │
│  ─────────                  │  ───────────────────              │
│  ✓ User Management          │  ┌─────────────────────────────┐  │
│  ✓ Role & Permission        │  │ Role List                   │  │
│  ✗ Dashboard (hidden)       │  │                             │  │
│  ✗ Reports (hidden)         │  │ [Add] ← HIDDEN (no create)  │  │
│  ✗ Settings (hidden)        │  │                             │  │
│                             │  │ ┌─────┬────────┬─────────┐  │  │
│                             │  │ │Name │ Slug   │ Actions │  │  │
│                             │  │ ├─────┼────────┼─────────┤  │  │
│                             │  │ │Admin│ admin  │ [View]  │  │  │
│                             │  │ │     │        │ NO EDIT │  │  │
│                             │  │ │     │        │ NO DEL  │  │  │
│                             │  │ └─────┴────────┴─────────┘  │  │
│                             │  └─────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Directory Structure

```
laravel-codebases/
├── app/
│   ├── Helpers/
│   │   ├── PermissionHelper.php      # Check permission by module ID
│   │   └── ModuleHelper.php          # Get modules, check access
│   ├── Http/
│   │   ├── Middleware/
│   │   │   ├── HandleInertiaRequests.php
│   │   │   ├── CheckModuleAccess.php    # Check read permission
│   │   │   └── CheckModulePermission.php # Check create/update/delete
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Module.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   └── PermissionService.php
│   └── Traits/
│       ├── HasRoles.php
│       └── ApiResponse.php
├── modules/                          # lowercase
│   ├── Auth/                         # Auth module
│   │   ├── app/
│   │   │   ├── Http/Controllers/
│   │   │   │   └── AuthController.php
│   │   │   └── Providers/
│   │   ├── resources/                # lowercase
│   │   │   └── views/                # Vue pages (lowercase)
│   │   │       └── login.vue         # lowercase filenames
│   │   └── routes/
│   ├── Dashboard/                    # Dashboard module
│   │   ├── app/
│   │   │   ├── Http/Controllers/
│   │   │   │   └── DashboardController.php
│   │   │   └── Providers/
│   │   ├── resources/                # lowercase
│   │   │   └── views/
│   │   │       └── index.vue
│   │   └── routes/
│   ├── UserManagement/               # Module ID: 1
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/
│   │   │   │   │   └── UserController.php
│   │   │   │   ├── Requests/
│   │   │   │   │   ├── StoreUserRequest.php
│   │   │   │   │   └── UpdateUserRequest.php
│   │   │   │   └── Resources/
│   │   │   │       └── UserResource.php
│   │   │   ├── Models/
│   │   │   ├── Repositories/
│   │   │   │   ├── Contracts/
│   │   │   │   │   └── UserRepositoryInterface.php
│   │   │   │   └── UserRepository.php
│   │   │   ├── Services/
│   │   │   │   └── UserService.php
│   │   │   └── Providers/
│   │   ├── config/
│   │   │   └── config.php
│   │   ├── database/
│   │   │   ├── factories/
│   │   │   ├── migrations/
│   │   │   └── seeders/
│   │   ├── resources/                # lowercase
│   │   │   ├── views/                # Vue pages (lowercase filenames)
│   │   │   │   ├── index.vue
│   │   │   │   ├── create.vue
│   │   │   │   ├── edit.vue
│   │   │   │   └── show.vue
│   │   │   ├── components/           # Vue components
│   │   │   │   ├── UserForm.vue
│   │   │   │   ├── UserTable.vue
│   │   │   │   └── UserFilters.vue
│   │   │   └── composable/           # Vue composables
│   │   │       └── useUser.js
│   │   ├── routes/
│   │   │   └── web.php
│   │   └── module.json
│   └── RolePermission/               # Module ID: 2
│       ├── app/
│       │   ├── Http/
│       │   │   ├── Controllers/
│       │   │   │   └── RoleController.php
│       │   │   ├── Requests/
│       │   │   │   ├── StoreRoleRequest.php
│       │   │   │   └── UpdateRoleRequest.php
│       │   │   └── Resources/
│       │   │       └── RoleResource.php
│       │   ├── Models/
│       │   ├── Repositories/
│       │   │   ├── Contracts/
│       │   │   │   └── RoleRepositoryInterface.php
│       │   │   └── RoleRepository.php
│       │   ├── Services/
│       │   │   └── RoleService.php
│       │   └── Providers/
│       ├── config/
│       │   └── config.php
│       ├── database/
│       │   ├── factories/
│       │   ├── migrations/
│       │   └── seeders/
│       ├── resources/                # lowercase
│       │   ├── views/                # Vue pages (lowercase filenames)
│       │   │   ├── index.vue
│       │   │   ├── create.vue
│       │   │   ├── edit.vue
│       │   │   └── show.vue
│       │   ├── components/           # Vue components
│       │   │   ├── RoleForm.vue
│       │   │   ├── RoleTable.vue
│       │   │   └── PermissionMatrix.vue
│       │   └── composable/           # Vue composables
│       │       └── useRole.js
│       ├── routes/
│       │   └── web.php
│       └── module.json
├── resources/
│   ├── js/                           # Shared Vue (alias: @)
│   │   ├── Layouts/                  # AppLayout.vue, GuestLayout.vue
│   │   ├── Components/               # Shared UI components
│   │   │   ├── Sidebar.vue
│   │   │   ├── Navbar.vue
│   │   │   ├── Pagination.vue
│   │   │   ├── Modal.vue
│   │   │   ├── ConfirmDialog.vue
│   │   │   ├── FlashMessage.vue
│   │   │   └── Form/
│   │   │       ├── Input.vue
│   │   │       ├── Select.vue
│   │   │       ├── Checkbox.vue
│   │   │       └── Button.vue
│   │   ├── Composables/              # usePermission.js, useFlash.js
│   │   ├── Helpers/                  # formatters.js
│   │   ├── app.js
│   │   └── bootstrap.js
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── app.blade.php
├── config/
│   └── modules.php
├── database/
│   └── migrations/
├── routes/
│   └── web.php
├── vite.config.js
├── tailwind.config.js
└── package.json
```

---

## Database Schema

### ERD

```
┌─────────────┐         ┌─────────────────┐
│   users     │────────<│  user_has_roles │
└─────────────┘         └─────────────────┘
                               │
                               │
                        ┌──────▼──────┐
                        │   roles     │
                        │─────────────│
                        │ permissions │◄─── JSON column
                        │ (JSON)      │     [read, create,
                        └─────────────┘      update, delete]
                                                  │
                                                  │ references
                                                  ▼
                                          ┌─────────────┐
                                          │  modules    │
                                          │─────────────│
                                          │ id: 1,2,3.. │
                                          └─────────────┘
```

### SQL Schema

```sql
-- modules
CREATE TABLE modules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,           -- 'User Management'
    slug VARCHAR(255) NOT NULL UNIQUE,    -- 'user-management'
    icon VARCHAR(100) NULL,               -- 'users' (heroicon name)
    route_name VARCHAR(255) NULL,         -- 'users.index'
    order INT DEFAULT 0,                  -- Urutan di sidebar
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- users
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- roles
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,           -- 'Super Admin'
    slug VARCHAR(255) NOT NULL UNIQUE,    -- 'super-admin'
    description TEXT NULL,
    permissions JSON NOT NULL,            -- {"read":["*"],"create":[1,2],...}
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

/*
  permissions JSON structure:
  {
    "read": ["*"],           // atau [1, 2, 3]
    "create": ["*"],         // atau [1, 2]
    "update": [1, 2],
    "delete": [1]
  }

  ["*"] = full access semua module
  [1,2,3] = akses ke module ID tertentu
  [] = tidak ada akses
*/

-- user_has_roles (pivot)
CREATE TABLE user_has_roles (
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

---

## Session Structure

Setelah login, permission di-load ke session:

```php
// Session structure
session([
    'auth' => [
        'user' => [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ],
        'roles' => ['super-admin', 'manager'],
        'permissions' => [
            'read'   => ['*'],        // atau [1, 2, 3]
            'create' => [1, 2],
            'update' => [1],
            'delete' => [],
        ],
        'modules' => [
            // Merged dari semua role, hanya module yang bisa di-read
            ['id' => 1, 'name' => 'User Management', 'slug' => 'user-management', 'icon' => 'users', 'route' => 'users.index'],
            ['id' => 2, 'name' => 'Role & Permission', 'slug' => 'role-permission', 'icon' => 'shield-check', 'route' => 'roles.index'],
        ],
    ],
]);
```

---

## Code Implementation

### 1. Module Model

```php
// app/Models/Module.php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'route_name',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
```

### 2. Role Model

```php
// app/Models/Role.php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_roles');
    }

    /**
     * Check if role has permission for specific action on module
     */
    public function hasPermission(string $action, int $moduleId): bool
    {
        $permissions = $this->permissions[$action] ?? [];

        // Full access
        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array($moduleId, $permissions, true);
    }

    /**
     * Check if role can read specific module
     */
    public function canRead(int $moduleId): bool
    {
        return $this->hasPermission('read', $moduleId);
    }

    /**
     * Check if role can create in specific module
     */
    public function canCreate(int $moduleId): bool
    {
        return $this->hasPermission('create', $moduleId);
    }

    /**
     * Check if role can update in specific module
     */
    public function canUpdate(int $moduleId): bool
    {
        return $this->hasPermission('update', $moduleId);
    }

    /**
     * Check if role can delete in specific module
     */
    public function canDelete(int $moduleId): bool
    {
        return $this->hasPermission('delete', $moduleId);
    }

    /**
     * Get all readable module IDs
     */
    public function getReadableModuleIds(): array
    {
        $read = $this->permissions['read'] ?? [];

        if (in_array('*', $read, true)) {
            return Module::active()->pluck('id')->toArray();
        }

        return $read;
    }
}
```

### 3. User Model with HasRoles Trait

```php
// app/Traits/HasRoles.php
<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    /**
     * Get merged permissions from all roles
     */
    public function getMergedPermissions(): array
    {
        $merged = [
            'read' => [],
            'create' => [],
            'update' => [],
            'delete' => [],
        ];

        foreach ($this->roles as $role) {
            foreach (['read', 'create', 'update', 'delete'] as $action) {
                $perms = $role->permissions[$action] ?? [];

                // If any role has full access, set full access
                if (in_array('*', $perms, true)) {
                    $merged[$action] = ['*'];
                    continue;
                }

                // Skip if already full access
                if (in_array('*', $merged[$action], true)) {
                    continue;
                }

                // Merge module IDs
                $merged[$action] = array_unique(
                    array_merge($merged[$action], $perms)
                );
            }
        }

        return $merged;
    }

    /**
     * Check if user has permission for action on module
     */
    public function hasPermission(string $action, int $moduleId): bool
    {
        $permissions = session('auth.permissions', $this->getMergedPermissions());
        $allowed = $permissions[$action] ?? [];

        if (in_array('*', $allowed, true)) {
            return true;
        }

        return in_array($moduleId, $allowed, true);
    }

    /**
     * Check shortcuts
     */
    public function canRead(int $moduleId): bool
    {
        return $this->hasPermission('read', $moduleId);
    }

    public function canCreate(int $moduleId): bool
    {
        return $this->hasPermission('create', $moduleId);
    }

    public function canUpdate(int $moduleId): bool
    {
        return $this->hasPermission('update', $moduleId);
    }

    public function canDelete(int $moduleId): bool
    {
        return $this->hasPermission('delete', $moduleId);
    }

    /**
     * Get accessible modules for sidebar
     */
    public function getAccessibleModules(): array
    {
        $permissions = session('auth.permissions', $this->getMergedPermissions());
        $readableIds = $permissions['read'] ?? [];

        $query = Module::active()->ordered();

        // If not full access, filter by IDs
        if (!in_array('*', $readableIds, true)) {
            if (empty($readableIds)) {
                return [];
            }
            $query->whereIn('id', $readableIds);
        }

        return $query->get()->toArray();
    }
}
```

```php
// app/Models/User.php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];
}
```

### 4. Permission Helper

```php
// app/Helpers/PermissionHelper.php
<?php

declare(strict_types=1);

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Check if current user can perform action on module
     */
    public static function can(string $action, int $moduleId): bool
    {
        $permissions = session('auth.permissions', []);
        $allowed = $permissions[$action] ?? [];

        // Full access check
        if (in_array('*', $allowed, true)) {
            return true;
        }

        return in_array($moduleId, $allowed, true);
    }

    /**
     * Shortcuts
     */
    public static function canRead(int $moduleId): bool
    {
        return self::can('read', $moduleId);
    }

    public static function canCreate(int $moduleId): bool
    {
        return self::can('create', $moduleId);
    }

    public static function canUpdate(int $moduleId): bool
    {
        return self::can('update', $moduleId);
    }

    public static function canDelete(int $moduleId): bool
    {
        return self::can('delete', $moduleId);
    }

    /**
     * Get permission object for Vue
     */
    public static function getModulePermissions(int $moduleId): array
    {
        return [
            'read' => self::canRead($moduleId),
            'create' => self::canCreate($moduleId),
            'update' => self::canUpdate($moduleId),
            'delete' => self::canDelete($moduleId),
        ];
    }

    /**
     * Get current user permissions
     */
    public static function all(): array
    {
        return session('auth.permissions', []);
    }

    /**
     * Get accessible modules for sidebar
     */
    public static function getAccessibleModules(): array
    {
        return session('auth.modules', []);
    }
}
```

### 5. Global Helper Functions

```php
// app/Helpers/helpers.php
<?php

declare(strict_types=1);

use App\Helpers\PermissionHelper;

if (!function_exists('can_access')) {
    /**
     * Check if user can perform action on module
     */
    function can_access(string $action, int $moduleId): bool
    {
        return PermissionHelper::can($action, $moduleId);
    }
}

if (!function_exists('can_read')) {
    function can_read(int $moduleId): bool
    {
        return PermissionHelper::canRead($moduleId);
    }
}

if (!function_exists('can_create')) {
    function can_create(int $moduleId): bool
    {
        return PermissionHelper::canCreate($moduleId);
    }
}

if (!function_exists('can_update')) {
    function can_update(int $moduleId): bool
    {
        return PermissionHelper::canUpdate($moduleId);
    }
}

if (!function_exists('can_delete')) {
    function can_delete(int $moduleId): bool
    {
        return PermissionHelper::canDelete($moduleId);
    }
}

if (!function_exists('module_permissions')) {
    /**
     * Get permission object for specific module (for Vue)
     */
    function module_permissions(int $moduleId): array
    {
        return PermissionHelper::getModulePermissions($moduleId);
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        return session('auth.user');
    }
}

if (!function_exists('sidebar_modules')) {
    function sidebar_modules(): array
    {
        return PermissionHelper::getAccessibleModules();
    }
}
```

### 6. Auth Service

```php
// app/Services/AuthService.php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attempt(array $credentials): bool
    {
        $user = User::where('email', $credentials['email'])
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        Auth::login($user, $credentials['remember'] ?? false);

        $this->loadUserSession($user);

        $user->update(['last_login_at' => now()]);

        return true;
    }

    public function loadUserSession(User $user): void
    {
        $user->load('roles');

        $permissions = $user->getMergedPermissions();
        $modules = $this->getAccessibleModules($permissions['read']);

        session([
            'auth' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'roles' => $user->roles->pluck('slug')->toArray(),
                'permissions' => $permissions,
                'modules' => $modules,
            ],
        ]);
    }

    protected function getAccessibleModules(array $readableIds): array
    {
        $query = Module::active()->ordered();

        // If not full access, filter by IDs
        if (!in_array('*', $readableIds, true)) {
            if (empty($readableIds)) {
                return [];
            }
            $query->whereIn('id', $readableIds);
        }

        return $query->get(['id', 'name', 'slug', 'icon', 'route_name'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'slug' => $m->slug,
                'icon' => $m->icon,
                'route' => $m->route_name,
            ])
            ->toArray();
    }

    public function logout(): void
    {
        session()->forget('auth');
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
```

### 7. Middleware - Check Module Access

```php
// app/Http/Middleware/CheckModuleAccess.php
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
     * Usage: middleware('module.access:1') or middleware('module.access:2')
     */
    public function handle(Request $request, Closure $next, int $moduleId): Response
    {
        if (!session()->has('auth.user')) {
            return redirect()->route('login');
        }

        if (!can_read($moduleId)) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        return $next($request);
    }
}
```

### 8. Middleware - Check Module Permission

```php
// app/Http/Middleware/CheckModulePermission.php
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
            abort(403, "Anda tidak memiliki izin {$action} di modul ini.");
        }

        return $next($request);
    }
}
```

### 9. Handle Inertia Requests

```php
// app/Http/Middleware/HandleInertiaRequests.php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => fn () => [
                'user' => session('auth.user'),
                'roles' => session('auth.roles', []),
                'permissions' => session('auth.permissions', []),
                'modules' => session('auth.modules', []),
            ],
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }
}
```

### 10. Base Controller

```php
// app/Http/Controllers/BaseController.php
<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

abstract class BaseController extends Controller
{
    protected int $moduleId;
    protected string $moduleName;

    protected function render(string $page, array $props = []): Response
    {
        return Inertia::render("{$this->moduleName}::{$page}", array_merge($props, [
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]));
    }
}
```

### 11. User Controller Example

```php
// modules/UserManagement/app/Http/Controllers/UserController.php
<?php

declare(strict_types=1);

namespace Modules\UserManagement\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Core\Http\Controllers\BaseController;
use Modules\UserManagement\Http\Requests\StoreUserRequest;
use Modules\UserManagement\Http\Requests\UpdateUserRequest;
use Modules\UserManagement\Http\Resources\UserResource;
use Modules\UserManagement\Services\UserService;

class UserController extends BaseController
{
    protected int $moduleId = 1;  // User Management module ID
    protected string $moduleName = 'UserManagement';

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): Response
    {
        $users = $this->userService->paginate(
            request()->only(['search', 'is_active']),
            request()->integer('per_page', 15)
        );

        return $this->render('views/index', [
            'users' => UserResource::collection($users),
            'filters' => request()->only(['search', 'is_active']),
        ]);
    }

    public function create(): Response
    {
        return $this->render('views/create', [
            'roles' => $this->userService->getRolesForSelect(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        $user = $this->userService->find($id);

        return $this->render('views/show', [
            'user' => new UserResource($user),
        ]);
    }

    public function edit(int $id): Response
    {
        $user = $this->userService->find($id);

        return $this->render('views/edit', [
            'user' => new UserResource($user),
            'roles' => $this->userService->getRolesForSelect(),
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->update($id, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->userService->delete($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
```

### 12. Routes with Middleware

```php
// modules/UserManagement/routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\UserController;

Route::middleware(['auth', 'module.access:1'])
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        // Read - list & show
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');

        // Create
        Route::middleware('module.permission:1,create')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        // Update
        Route::middleware('module.permission:1,update')->group(function () {
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
        });

        // Delete
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->name('destroy')
            ->middleware('module.permission:1,delete');
    });
```

---

## Vue.js Implementation

### 1. usePermission Composable

```javascript
// resources/js/Composables/usePermission.js
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermission() {
  const page = usePage()

  // Get permissions from current page props (module-specific)
  const can = computed(() => page.props.can || {})

  // Get global permissions from auth
  const permissions = computed(() => page.props.auth?.permissions || {})

  // Check if user can perform action on specific module
  const canAccess = (action, moduleId) => {
    const allowed = permissions.value[action] || []

    // Full access check
    if (allowed.includes('*')) {
      return true
    }

    return allowed.includes(moduleId)
  }

  // Module-specific shortcuts (uses current page's can prop)
  const canRead = computed(() => can.value.read ?? false)
  const canCreate = computed(() => can.value.create ?? false)
  const canUpdate = computed(() => can.value.update ?? false)
  const canDelete = computed(() => can.value.delete ?? false)

  return {
    can,
    permissions,
    canAccess,
    canRead,
    canCreate,
    canUpdate,
    canDelete,
  }
}
```

### 2. Sidebar Component

```vue
<!-- resources/js/Components/Sidebar.vue -->
<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
  UsersIcon,
  ShieldCheckIcon,
  HomeIcon,
  ChartBarIcon,
  CogIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

// Get accessible modules from auth session
const modules = computed(() => page.props.auth?.modules || [])

// Icon mapping
const iconMap = {
  'users': UsersIcon,
  'shield-check': ShieldCheckIcon,
  'home': HomeIcon,
  'chart-bar': ChartBarIcon,
  'cog': CogIcon,
}

const getIcon = (iconName) => iconMap[iconName] || HomeIcon

// Check if route is active
const isActive = (routeName) => {
  return route().current(routeName + '*')
}
</script>

<template>
  <aside class="w-64 bg-gray-800 min-h-screen">
    <div class="p-4">
      <h1 class="text-white text-xl font-bold">Admin Panel</h1>
    </div>

    <nav class="mt-4">
      <!-- Only show modules that user has READ permission -->
      <Link
        v-for="mod in modules"
        :key="mod.id"
        :href="route(mod.route)"
        class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition"
        :class="{ 'bg-gray-700 text-white': isActive(mod.route.replace('.index', '')) }"
      >
        <component :is="getIcon(mod.icon)" class="w-5 h-5 mr-3" />
        {{ mod.name }}
      </Link>
    </nav>
  </aside>
</template>
```

### 3. User Index Page

```vue
<!-- modules/UserManagement/resources/views/index.vue -->
<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { debounce } from 'lodash-es'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import UserTable from '../Components/UserTable.vue'
import UserFilters from '../Components/UserFilters.vue'
import { usePermission } from '@/Composables/usePermission'

const props = defineProps({
  users: Object,
  filters: Object,
  can: Object,        // { read: true, create: true, update: false, delete: false }
  moduleId: Number,
})

const { canCreate, canUpdate, canDelete } = usePermission()

const search = ref(props.filters?.search || '')

const applyFilters = debounce(() => {
  router.get(route('users.index'), {
    search: search.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch(search, applyFilters)

const deleteUser = (id) => {
  if (confirm('Yakin ingin menghapus user ini?')) {
    router.delete(route('users.destroy', id))
  }
}
</script>

<template>
  <AppLayout>
    <Head title="User Management" />

    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold text-gray-900">Users</h1>

          <!-- Only show Add button if user has CREATE permission -->
          <Link
            v-if="canCreate"
            :href="route('users.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Tambah User
          </Link>
        </div>

        <UserFilters v-model:search="search" />

        <UserTable
          :users="users.data"
          :can-update="canUpdate"
          :can-delete="canDelete"
          @delete="deleteUser"
        />

        <Pagination :links="users.meta.links" class="mt-4" />
      </div>
    </div>
  </AppLayout>
</template>
```

### 4. User Table Component

```vue
<!-- modules/UserManagement/resources/components/UserTable.vue -->
<script setup>
import { Link } from '@inertiajs/vue3'
import { PencilIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/outline'

defineProps({
  users: Array,
  canUpdate: Boolean,
  canDelete: Boolean,
})

const emit = defineEmits(['delete'])
</script>

<template>
  <div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="user in users" :key="user.id">
          <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span
              class="px-2 py-1 text-xs rounded-full"
              :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            >
              {{ user.is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center space-x-2">
              <!-- View - always visible (user already has read access) -->
              <Link
                :href="route('users.show', user.id)"
                class="text-gray-500 hover:text-gray-700"
              >
                <EyeIcon class="w-5 h-5" />
              </Link>

              <!-- Edit - only if canUpdate -->
              <Link
                v-if="canUpdate"
                :href="route('users.edit', user.id)"
                class="text-blue-500 hover:text-blue-700"
              >
                <PencilIcon class="w-5 h-5" />
              </Link>

              <!-- Delete - only if canDelete -->
              <button
                v-if="canDelete"
                @click="$emit('delete', user.id)"
                class="text-red-500 hover:text-red-700"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
```

### 5. Role Form with Permission Matrix

```vue
<!-- modules/RolePermission/resources/components/PermissionMatrix.vue -->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  modules: Array,      // All available modules
  modelValue: Object,  // { read: [], create: [], update: [], delete: [] }
})

const emit = defineEmits(['update:modelValue'])

const actions = ['read', 'create', 'update', 'delete']

const isFullAccess = (action) => {
  return props.modelValue[action]?.includes('*')
}

const isChecked = (action, moduleId) => {
  if (isFullAccess(action)) return true
  return props.modelValue[action]?.includes(moduleId)
}

const togglePermission = (action, moduleId) => {
  const current = [...(props.modelValue[action] || [])]

  // If currently full access, switch to specific modules
  if (current.includes('*')) {
    const allIds = props.modules.map(m => m.id).filter(id => id !== moduleId)
    emit('update:modelValue', {
      ...props.modelValue,
      [action]: allIds,
    })
    return
  }

  // Toggle specific module
  const index = current.indexOf(moduleId)
  if (index > -1) {
    current.splice(index, 1)
  } else {
    current.push(moduleId)
  }

  emit('update:modelValue', {
    ...props.modelValue,
    [action]: current,
  })
}

const toggleFullAccess = (action) => {
  const current = props.modelValue[action] || []

  if (current.includes('*')) {
    // Remove full access
    emit('update:modelValue', {
      ...props.modelValue,
      [action]: [],
    })
  } else {
    // Set full access
    emit('update:modelValue', {
      ...props.modelValue,
      [action]: ['*'],
    })
  }
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
            Module
          </th>
          <th
            v-for="action in actions"
            :key="action"
            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"
          >
            {{ action }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <!-- Full Access Row -->
        <tr class="bg-blue-50">
          <td class="px-4 py-3 font-medium text-blue-700">
            Full Access [*]
          </td>
          <td
            v-for="action in actions"
            :key="`full-${action}`"
            class="px-4 py-3 text-center"
          >
            <input
              type="checkbox"
              :checked="isFullAccess(action)"
              @change="toggleFullAccess(action)"
              class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
            />
          </td>
        </tr>

        <!-- Module Rows -->
        <tr v-for="mod in modules" :key="mod.id">
          <td class="px-4 py-3">
            {{ mod.name }}
            <span class="text-gray-400 text-sm">(ID: {{ mod.id }})</span>
          </td>
          <td
            v-for="action in actions"
            :key="`${mod.id}-${action}`"
            class="px-4 py-3 text-center"
          >
            <input
              type="checkbox"
              :checked="isChecked(action, mod.id)"
              :disabled="isFullAccess(action)"
              @change="togglePermission(action, mod.id)"
              class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 disabled:opacity-50"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
```

---

## Seeders

### Permission Seeder

```php
// database/seeders/ModuleSeeder.php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['id' => 1, 'name' => 'User Management', 'slug' => 'user-management', 'icon' => 'users', 'route_name' => 'users.index', 'order' => 1],
            ['id' => 2, 'name' => 'Role & Permission', 'slug' => 'role-permission', 'icon' => 'shield-check', 'route_name' => 'roles.index', 'order' => 2],
            ['id' => 3, 'name' => 'Dashboard', 'slug' => 'dashboard', 'icon' => 'home', 'route_name' => 'dashboard', 'order' => 0],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(['id' => $module['id']], $module);
        }
    }
}
```

### Role Seeder

```php
// database/seeders/RoleSeeder.php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin - Full Access
        Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full access to all modules',
                'permissions' => [
                    'read' => ['*'],
                    'create' => ['*'],
                    'update' => ['*'],
                    'delete' => ['*'],
                ],
            ]
        );

        // Admin - Can manage users, view roles
        Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Can manage users, view roles',
                'permissions' => [
                    'read' => [1, 2, 3],      // User, Role, Dashboard
                    'create' => [1],          // Only User
                    'update' => [1],          // Only User
                    'delete' => [1],          // Only User
                ],
            ]
        );

        // Manager - View only
        Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'View only access',
                'permissions' => [
                    'read' => [1, 3],         // User, Dashboard
                    'create' => [],
                    'update' => [],
                    'delete' => [],
                ],
            ]
        );

        // Viewer - Dashboard only
        Role::updateOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Viewer',
                'description' => 'Dashboard access only',
                'permissions' => [
                    'read' => [3],            // Dashboard only
                    'create' => [],
                    'update' => [],
                    'delete' => [],
                ],
            ]
        );
    }
}
```

---

## Summary

### Permission Format

| Format | Meaning | Example |
|--------|---------|---------|
| `['*']` | Full access semua module | Super Admin |
| `[1, 2, 3]` | Akses ke module ID 1, 2, 3 | Specific access |
| `[]` | Tidak ada akses | No permission |

### How It Works

1. **Login** → Load permissions ke session
2. **Sidebar** → Hanya tampilkan module yang ada di `read[]`
3. **Page Access** → Middleware cek `read` permission
4. **Actions** → Component cek `create/update/delete` permission
5. **UI** → Tombol Add/Edit/Delete hanya muncul jika punya permission

### Key Benefits

- Simple data structure (array of IDs)
- Easy to understand (1 = module 1, [*] = all)
- Fast permission check (just array lookup)
- Flexible role assignment (user can have multiple roles)
- Clean UI (buttons auto-hide based on permission)

---

## Architecture Principles

### Core Principles

1. **Modularitas** - Setiap fitur terisolasi dalam modulenya sendiri
2. **Scalability** - Mudah menambah module baru tanpa mengubah core
3. **Maintainability** - Kode terorganisir dengan pattern yang konsisten
4. **Security** - Multi-layer access control (Module Access + Permission)
5. **Testability** - Struktur yang mudah di-test (Repository pattern, DI)
6. **Type Safety** - Strict types PHP + TypeScript/JSDoc support
7. **DX (Developer Experience)** - Pattern yang jelas dan konsisten

### Design Patterns Used

| Pattern | Purpose | Location |
|---------|---------|----------|
| Repository | Data access abstraction | `modules/*/app/Repositories/` |
| Service | Business logic encapsulation | `modules/*/app/Services/` |
| Form Request | Validation & authorization | `modules/*/app/Http/Requests/` |
| Resource | API response transformation | `modules/*/app/Http/Resources/` |
| Trait | Reusable model behaviors | `app/Traits/` |
| Composable | Reusable Vue logic | `modules/*/resources/Composables/` |

### Key Architecture Benefits

- **Clean separation of concerns** - Controller → Service → Repository → Model
- **Reusable components dan composables** - Shared UI di Core module
- **Session-based module access** - Performance (no DB query per request)
- **Role-based permission** - Fine-grained control per action per module
- **Consistent API response format** - Standardized JSON structure
- **Full-stack type hints** - PHP strict types + Vue props validation

---

## Module Independence

Setiap module bersifat independen dan self-contained:

```
modules/ModuleName/              # lowercase modules folder
├── app/                         # PHP code
│   ├── Http/
│   │   ├── Controllers/         → Handle requests
│   │   ├── Requests/            → Validation rules
│   │   └── Resources/           → JSON transformation
│   ├── Models/                  → Eloquent models
│   ├── Repositories/            → Data access layer
│   │   └── Contracts/           → Repository interfaces
│   ├── Services/                → Business logic
│   └── Providers/               → Service provider
├── config/                      → Module configuration
├── database/
│   ├── factories/               → Model factories
│   ├── migrations/              → Database migrations
│   └── seeders/                 → Data seeders
├── resources/                   # lowercase resources folder
│   ├── views/                   → Vue pages (lowercase: index.vue, create.vue)
│   ├── components/              → Vue components (PascalCase: UserTable.vue)
│   └── composable/              → Vue composables (useUser.js)
├── routes/                      → Module routes
├── tests/
│   ├── Feature/                 → Feature tests
│   └── Unit/                    → Unit tests
└── module.json                  → Module metadata
```

### Adding New Module Checklist

1. ⬜ Create module: `php artisan module:make ModuleName`
2. ⬜ Add to `modules` table dengan unique ID
3. ⬜ Set `module_id` di config
4. ⬜ Create Repository (Interface + Implementation)
5. ⬜ Create Service
6. ⬜ Create Form Requests
7. ⬜ Create Resource
8. ⬜ Create Controller (extend BaseController)
9. ⬜ Setup routes dengan middleware
10. ⬜ Create Vue pages dan components
11. ⬜ Update role permissions jika perlu

---

## Security Layers

```
┌─────────────────────────────────────────────────────────────────┐
│                      SECURITY LAYERS                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Layer 1: Authentication                                         │
│  ─────────────────────────                                       │
│  → Laravel session-based auth                                    │
│  → is_active check on login                                      │
│  → Session regeneration on login/logout                          │
│                                                                  │
│  Layer 2: Module Access                                          │
│  ─────────────────────────                                       │
│  → middleware('module.access:1')                                 │
│  → Check if user can READ module                                 │
│  → 403 if not allowed                                            │
│                                                                  │
│  Layer 3: Action Permission                                      │
│  ─────────────────────────                                       │
│  → middleware('module.permission:1,create')                      │
│  → Check specific action (create/update/delete)                  │
│  → 403 if not allowed                                            │
│                                                                  │
│  Layer 4: Form Request Authorization                             │
│  ─────────────────────────                                       │
│  → authorize() method in Form Request                            │
│  → Additional business logic checks                              │
│                                                                  │
│  Layer 5: UI Conditional Rendering                               │
│  ─────────────────────────                                       │
│  → v-if="canCreate" / v-if="canUpdate" / v-if="canDelete"       │
│  → Buttons hidden if no permission                               │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Performance Optimizations

### 1. Session-based Permission

```php
// BAD: Query database setiap request
$user->roles()->with('permissions')->get(); // Query setiap check

// GOOD: Load sekali ke session saat login
session(['auth.permissions' => $user->getMergedPermissions()]);

// Check permission dari session (no query)
can_read(1); // Just array lookup
```

### 2. Eager Loading

```php
// BAD: N+1 queries
$users = User::all();
foreach ($users as $user) {
    $user->roles; // Query per user!
}

// GOOD: Eager load
$users = User::with('roles')->paginate(15);
```

### 3. Module List Caching

```php
// Modules rarely change, cache them
$modules = Cache::remember('modules.active', 3600, function () {
    return Module::active()->ordered()->get();
});
```

### 4. Inertia Partial Reloads

```javascript
// Only reload specific props
router.reload({ only: ['users'] })
```

---

## Scalability Considerations

### Horizontal Scaling

- **Stateless** - Session bisa disimpan di Redis untuk multi-server
- **Database** - Support read replicas untuk scaling read operations
- **Assets** - Vite build untuk CDN deployment

### Vertical Scaling (Adding Features)

- **New Module** - Just create new folder, no core changes
- **New Permission Action** - Add to JSON structure (e.g., "export", "import")
- **New Role** - Just insert to database, no code changes

### Module Communication

```php
// Modules bisa communicate via Services
// UserManagement module needs roles list
$roles = app(RoleService::class)->getRolesForSelect();

// Atau via Events (loosely coupled)
event(new UserCreated($user));
// RolePermission module listens and assigns default role
```

---

## Testing Strategy

### Unit Tests

```php
// Test Service
class UserServiceTest extends TestCase
{
    public function test_create_user_hashes_password(): void
    {
        $service = new UserService($this->mockRepository);
        $user = $service->create(['password' => 'plain']);

        $this->assertNotEquals('plain', $user->password);
    }
}
```

### Feature Tests

```php
// Test Controller with Permission
class UserControllerTest extends TestCase
{
    public function test_user_without_permission_cannot_create(): void
    {
        $this->actingAsUserWithPermissions([
            'read' => [1],
            'create' => [], // No create permission
        ]);

        $response = $this->post(route('users.store'), [...]);

        $response->assertStatus(403);
    }
}
```

### Vue Component Tests

```javascript
// Test permission-based rendering
import { mount } from '@vue/test-utils'
import UserTable from './UserTable.vue'

test('hides delete button without permission', () => {
  const wrapper = mount(UserTable, {
    props: {
      users: [...],
      canDelete: false,
    },
  })

  expect(wrapper.find('[data-test="delete-btn"]').exists()).toBe(false)
})
```

---

## File Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| PHP Class | PascalCase | `UserController.php` |
| PHP Trait | PascalCase | `HasRoles.php` |
| PHP Helper | snake_case | `core_helpers.php` |
| Vue Page | lowercase | `index.vue`, `create.vue`, `edit.vue`, `show.vue` |
| Vue Component | PascalCase | `UserTable.vue`, `UserForm.vue` |
| Vue Composable | camelCase with "use" | `usePermission.js`, `useUser.js` |
| Vue Helper | camelCase | `formatters.js` |
| Migration | snake_case with timestamp | `2024_01_01_000001_create_users_table.php` |
| Config | snake_case | `config.php` |
| Route | kebab-case | `/user-management` |
| Database Table | snake_case plural | `users`, `user_has_roles` |
| Database Column | snake_case | `created_at`, `is_active` |

---

## Related Documentation

| File | Description |
|------|-------------|
| [DATABASE.md](./DATABASE.md) | Database schema, migrations, seeders, ERD |
| [STUBS.md](./STUBS.md) | Module stub templates untuk generate module baru |
| [TASKS.md](./TASKS.md) | Implementation task list dengan 103+ tasks |
