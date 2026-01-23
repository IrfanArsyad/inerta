# Database Architecture

Dokumentasi lengkap untuk database schema, relationships, dan design patterns.

---

## Overview

Database menggunakan pendekatan **Module-ID Based Permission** dengan struktur yang:
- **Normalized** - Menghindari data redundancy
- **Scalable** - Mudah menambah module baru
- **Performance** - Permission di-cache ke session
- **Flexible** - JSON column untuk permission yang dynamic

---

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           DATABASE SCHEMA                                    │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌─────────────────┐
                              │    modules      │
                              ├─────────────────┤
                              │ id              │◄──────────────────────────┐
                              │ name            │                           │
                              │ slug            │                           │
                              │ icon            │                           │
                              │ route_name      │                           │
                              │ order           │                           │
                              │ is_active       │                           │
                              │ created_at      │                           │
                              │ updated_at      │                           │
                              └─────────────────┘                           │
                                                                            │
                                                                   references
                                                                            │
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐ │
│     users       │         │ user_has_roles  │         │     roles       │ │
├─────────────────┤         ├─────────────────┤         ├─────────────────┤ │
│ id              │◄────────│ user_id    (FK) │         │ id              │ │
│ name            │         │ role_id    (FK) │────────►│ name            │ │
│ email           │         └─────────────────┘         │ slug            │ │
│ password        │                                     │ description     │ │
│ is_active       │                                     │ permissions     │─┘
│ last_login_at   │                                     │ is_active       │
│ remember_token  │                                     │ created_at      │
│ created_at      │                                     │ updated_at      │
│ updated_at      │                                     └─────────────────┘
│ deleted_at      │
└─────────────────┘                                     permissions JSON:
                                                        {
                                                          "read": ["*"] or [1,2,3],
                                                          "create": [1,2],
                                                          "update": [1],
                                                          "delete": []
                                                        }
```

---

## Tables Definition

### 1. modules

Menyimpan daftar semua module yang tersedia dalam sistem.

```sql
CREATE TABLE modules (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL UNIQUE,
    icon            VARCHAR(100) NULL,
    route_name      VARCHAR(255) NULL,
    `order`         INT DEFAULT 0,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    INDEX idx_modules_slug (slug),
    INDEX idx_modules_order (`order`),
    INDEX idx_modules_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key, digunakan untuk permission check |
| `name` | VARCHAR(255) | Nama module untuk display (e.g., "User Management") |
| `slug` | VARCHAR(255) | Unique identifier (e.g., "user-management") |
| `icon` | VARCHAR(100) | Nama icon untuk sidebar (e.g., "users") |
| `route_name` | VARCHAR(255) | Laravel route name (e.g., "users.index") |
| `order` | INT | Urutan tampil di sidebar |
| `is_active` | BOOLEAN | Status aktif/nonaktif module |

**Sample Data:**
```sql
INSERT INTO modules (id, name, slug, icon, route_name, `order`, is_active) VALUES
(1, 'User Management', 'user-management', 'users', 'users.index', 1, true),
(2, 'Role & Permission', 'role-permission', 'shield-check', 'roles.index', 2, true),
(3, 'Dashboard', 'dashboard', 'home', 'dashboard', 0, true),
(4, 'Reports', 'reports', 'chart-bar', 'reports.index', 3, true),
(5, 'Settings', 'settings', 'cog', 'settings.index', 99, true);
```

---

### 2. users

Menyimpan data user dengan soft delete support.

```sql
CREATE TABLE users (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    last_login_at   TIMESTAMP NULL,
    remember_token  VARCHAR(100) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    INDEX idx_users_email (email),
    INDEX idx_users_active (is_active),
    INDEX idx_users_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key |
| `name` | VARCHAR(255) | Nama lengkap user |
| `email` | VARCHAR(255) | Email (unique, untuk login) |
| `password` | VARCHAR(255) | Hashed password (bcrypt) |
| `is_active` | BOOLEAN | Status aktif (false = tidak bisa login) |
| `last_login_at` | TIMESTAMP | Waktu login terakhir |
| `remember_token` | VARCHAR(100) | Token untuk "Remember Me" |
| `deleted_at` | TIMESTAMP | Soft delete timestamp |

---

### 3. roles

Menyimpan role dengan permission dalam format JSON.

```sql
CREATE TABLE roles (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL UNIQUE,
    description     TEXT NULL,
    permissions     JSON NOT NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    INDEX idx_roles_slug (slug),
    INDEX idx_roles_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key |
| `name` | VARCHAR(255) | Nama role (e.g., "Super Admin") |
| `slug` | VARCHAR(255) | Unique identifier (e.g., "super-admin") |
| `description` | TEXT | Deskripsi role |
| `permissions` | JSON | Permission object (lihat format di bawah) |
| `is_active` | BOOLEAN | Status aktif role |

**Permission JSON Format:**
```json
{
  "read": ["*"],           // ["*"] = full access, [1,2,3] = specific modules
  "create": ["*"],         // [] = no access
  "update": [1, 2],
  "delete": [1]
}
```

**Sample Data:**
```sql
-- Super Admin (Full Access)
INSERT INTO roles (name, slug, description, permissions) VALUES
('Super Admin', 'super-admin', 'Full access to all modules',
 '{"read": ["*"], "create": ["*"], "update": ["*"], "delete": ["*"]}');

-- Admin (Manage Users Only)
INSERT INTO roles (name, slug, description, permissions) VALUES
('Admin', 'admin', 'Can manage users, view roles',
 '{"read": [1, 2, 3], "create": [1], "update": [1], "delete": [1]}');

-- Manager (View Only)
INSERT INTO roles (name, slug, description, permissions) VALUES
('Manager', 'manager', 'View only access to users and dashboard',
 '{"read": [1, 3], "create": [], "update": [], "delete": []}');

-- Viewer (Dashboard Only)
INSERT INTO roles (name, slug, description, permissions) VALUES
('Viewer', 'viewer', 'Dashboard access only',
 '{"read": [3], "create": [], "update": [], "delete": []}');
```

---

### 4. user_has_roles (Pivot)

Menyimpan relasi many-to-many antara users dan roles.

```sql
CREATE TABLE user_has_roles (
    user_id         BIGINT UNSIGNED NOT NULL,
    role_id         BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (user_id, role_id),

    CONSTRAINT fk_uhr_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_uhr_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE CASCADE,

    INDEX idx_uhr_user (user_id),
    INDEX idx_uhr_role (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

| Column | Type | Description |
|--------|------|-------------|
| `user_id` | BIGINT | FK ke users.id |
| `role_id` | BIGINT | FK ke roles.id |

**Sample Data:**
```sql
-- User 1 adalah Super Admin
INSERT INTO user_has_roles (user_id, role_id) VALUES (1, 1);

-- User 2 adalah Admin dan Manager
INSERT INTO user_has_roles (user_id, role_id) VALUES (2, 2), (2, 3);

-- User 3 hanya Viewer
INSERT INTO user_has_roles (user_id, role_id) VALUES (3, 4);
```

---

## Laravel Migrations

### Migration: create_modules_table

```php
<?php
// database/migrations/2024_01_01_000001_create_modules_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 100)->nullable();
            $table->string('route_name')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('order');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
```

### Migration: create_users_table

```php
<?php
// database/migrations/2024_01_01_000002_create_users_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Migration: create_roles_table

```php
<?php
// database/migrations/2024_01_01_000003_create_roles_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

### Migration: create_user_has_roles_table

```php
<?php
// database/migrations/2024_01_01_000004_create_user_has_roles_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();

            $table->primary(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_has_roles');
    }
};
```

---

## Permission System

### How Permission Check Works

```
┌─────────────────────────────────────────────────────────────────┐
│                    PERMISSION CHECK FLOW                         │
└─────────────────────────────────────────────────────────────────┘

1. User Login
   │
   ▼
2. Load all roles for user
   │
   ▼
3. Merge permissions from all roles
   │
   ├─ If any role has ["*"] → Full access
   │
   └─ Otherwise → Merge all module IDs
   │
   ▼
4. Store in session:
   session('auth.permissions') = {
     "read": ["*"] or [1, 2, 3],
     "create": [1, 2],
     "update": [1],
     "delete": []
   }
   │
   ▼
5. Permission Check:
   can_read(1)    → Check if 1 in read[] or read contains "*"
   can_create(2)  → Check if 2 in create[] or create contains "*"
   etc.
```

### Permission Merging Logic

Jika user memiliki multiple roles, permissions di-merge:

```php
// User has roles: Admin + Manager
// Admin:   {"read": [1,2,3], "create": [1], "update": [1], "delete": [1]}
// Manager: {"read": [1,3,4], "create": [],  "update": [],  "delete": []}

// Merged Result:
{
    "read":   [1, 2, 3, 4],  // Union of all
    "create": [1],            // Union of all
    "update": [1],            // Union of all
    "delete": [1]             // Union of all
}

// If any role has ["*"], result is ["*"]
// Admin:   {"read": ["*"], ...}
// Manager: {"read": [1,3], ...}
// Merged:  {"read": ["*"], ...}  // Full access wins
```

---

## Query Examples

### Get User with Roles and Permissions

```php
// Eloquent
$user = User::with('roles')->find($id);

// Get merged permissions
$permissions = $user->getMergedPermissions();
```

### Get Accessible Modules for Sidebar

```php
// If read = ["*"], get all active modules
// Otherwise, filter by module IDs

$readableIds = session('auth.permissions.read', []);

$modules = Module::query()
    ->active()
    ->ordered()
    ->when(!in_array('*', $readableIds), function ($query) use ($readableIds) {
        return $query->whereIn('id', $readableIds);
    })
    ->get();
```

### Check Permission

```php
// Helper function
function can_access(string $action, int $moduleId): bool
{
    $permissions = session('auth.permissions', []);
    $allowed = $permissions[$action] ?? [];

    if (in_array('*', $allowed, true)) {
        return true;
    }

    return in_array($moduleId, $allowed, true);
}

// Usage
if (can_access('create', 1)) {
    // User can create in module 1
}
```

---

## Indexing Strategy

```sql
-- modules
INDEX idx_modules_slug (slug)           -- untuk lookup by slug
INDEX idx_modules_order (`order`)       -- untuk sorting sidebar
INDEX idx_modules_active (is_active)    -- untuk filter active only

-- users
INDEX idx_users_email (email)           -- untuk login lookup
INDEX idx_users_active (is_active)      -- untuk filter active only
INDEX idx_users_deleted (deleted_at)    -- untuk soft delete queries

-- roles
INDEX idx_roles_slug (slug)             -- untuk lookup by slug
INDEX idx_roles_active (is_active)      -- untuk filter active only

-- user_has_roles
PRIMARY KEY (user_id, role_id)          -- composite primary key
INDEX idx_uhr_user (user_id)            -- untuk get roles by user
INDEX idx_uhr_role (role_id)            -- untuk get users by role
```

---

## Data Integrity

### Constraints

```sql
-- Foreign Keys with CASCADE DELETE
-- Jika user dihapus, user_has_roles record juga dihapus
-- Jika role dihapus, user_has_roles record juga dihapus

CONSTRAINT fk_uhr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
CONSTRAINT fk_uhr_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE

-- Unique Constraints
UNIQUE (modules.slug)
UNIQUE (users.email)
UNIQUE (roles.slug)
```

### Validation Rules

```php
// modules.slug
'slug' => ['required', 'string', 'max:255', 'unique:modules,slug', 'regex:/^[a-z0-9-]+$/']

// users.email
'email' => ['required', 'email', 'max:255', 'unique:users,email']

// roles.slug
'slug' => ['required', 'string', 'max:255', 'unique:roles,slug', 'regex:/^[a-z0-9-]+$/']

// roles.permissions (JSON validation)
'permissions' => ['required', 'array']
'permissions.read' => ['required', 'array']
'permissions.create' => ['required', 'array']
'permissions.update' => ['required', 'array']
'permissions.delete' => ['required', 'array']
'permissions.read.*' => ['required_unless:permissions.read.*,*', 'integer', 'exists:modules,id']
```

---

## Seeders

### ModuleSeeder

```php
<?php
// database/seeders/ModuleSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'id' => 1,
                'name' => 'User Management',
                'slug' => 'user-management',
                'icon' => 'users',
                'route_name' => 'users.index',
                'order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Role & Permission',
                'slug' => 'role-permission',
                'icon' => 'shield-check',
                'route_name' => 'roles.index',
                'order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'home',
                'route_name' => 'dashboard',
                'order' => 0,
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
```

### RoleSeeder

```php
<?php
// database/seeders/RoleSeeder.php

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
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to all modules',
                'permissions' => [
                    'read' => ['*'],
                    'create' => ['*'],
                    'update' => ['*'],
                    'delete' => ['*'],
                ],
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Can manage users, view roles and dashboard',
                'permissions' => [
                    'read' => [1, 2, 3],
                    'create' => [1],
                    'update' => [1],
                    'delete' => [1],
                ],
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'View only access to users and dashboard',
                'permissions' => [
                    'read' => [1, 3],
                    'create' => [],
                    'update' => [],
                    'delete' => [],
                ],
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Dashboard access only',
                'permissions' => [
                    'read' => [3],
                    'create' => [],
                    'update' => [],
                    'delete' => [],
                ],
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
```

### AdminUserSeeder

```php
<?php
// database/seeders/AdminUserSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if ($superAdminRole) {
            $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }
    }
}
```

### DatabaseSeeder

```php
<?php
// database/seeders/DatabaseSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleSeeder::class,      // 1. Create modules first
            RoleSeeder::class,        // 2. Create roles with permissions
            AdminUserSeeder::class,   // 3. Create admin user with role
        ]);
    }
}
```

---

## Performance Considerations

### 1. Session-based Permission Cache

Permission di-load sekali saat login dan disimpan di session:

```php
// Login: Load ke session (1x query)
session(['auth.permissions' => $user->getMergedPermissions()]);

// Permission check: No query, just session lookup
$canCreate = in_array(1, session('auth.permissions.create'));
```

### 2. Eager Loading

```php
// Avoid N+1
$users = User::with('roles')->paginate(15);

// Bad: N+1 queries
foreach ($users as $user) {
    echo $user->roles; // Query per user
}
```

### 3. Module List Caching

```php
// Cache modules list (rarely changes)
$modules = Cache::remember('modules.active', 3600, function () {
    return Module::active()->ordered()->get();
});
```

---

## Adding New Module

Untuk menambah module baru:

### 1. Insert ke Database

```sql
INSERT INTO modules (id, name, slug, icon, route_name, `order`)
VALUES (4, 'Reports', 'reports', 'chart-bar', 'reports.index', 3);
```

### 2. Update Role Permissions

```sql
-- Beri akses ke Super Admin (sudah otomatis karena ["*"])

-- Beri akses read ke Admin
UPDATE roles
SET permissions = JSON_SET(permissions, '$.read', JSON_ARRAY(1, 2, 3, 4))
WHERE slug = 'admin';
```

### 3. Create Module dengan Artisan

```bash
php artisan module:make Reports
```

### 4. Set Module ID di Config

```php
// modules/Reports/config/config.php
return [
    'module_id' => 4,
    'name' => 'Reports',
];
```

---

## Summary

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `modules` | Daftar module sistem | id, slug, route_name |
| `users` | Data user | email, is_active, deleted_at |
| `roles` | Role dengan permissions | slug, permissions (JSON) |
| `user_has_roles` | Relasi user-role | user_id, role_id |

### Key Points

1. **Module ID** adalah kunci untuk permission check
2. **JSON permissions** memberikan flexibility tanpa join table tambahan
3. **Session cache** menghindari query berulang
4. **Soft delete** pada users untuk audit trail
5. **Cascade delete** pada pivot table untuk data integrity

---

## Related Documentation

| File | Description |
|------|-------------|
| [ARCHITECTURE.md](./ARCHITECTURE.md) | Full architecture design, code patterns, security layers |
| [STUBS.md](./STUBS.md) | Module stub templates untuk generate module baru |
| [TASKS.md](./TASKS.md) | Implementation task list dengan 103+ tasks |
