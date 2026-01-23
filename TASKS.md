# Implementation Tasks - Laravel Modular Architecture

Dokumen ini berisi daftar task yang harus dikerjakan untuk mengimplementasikan architecture dengan **Module-ID Based Permission System**.

---

## Permission System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│  PERMISSION STRUCTURE                                           │
├─────────────────────────────────────────────────────────────────┤
│  permissions: {                                                 │
│    "read":   ["*"] atau [1, 2, 3]                              │
│    "create": ["*"] atau [1, 2]                                 │
│    "update": [1]                                               │
│    "delete": []                                                │
│  }                                                             │
│                                                                 │
│  ["*"]     = Full access semua module                          │
│  [1,2,3]   = Akses ke module ID 1, 2, 3                        │
│  []        = Tidak ada akses                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## Phase 1: Project Setup & Foundation

### 1.1 Laravel Installation

| # | Task | Priority | Status |
|---|------|----------|--------|
| 1.1.1 | Create Laravel 11 project: `composer create-project laravel/laravel .` | HIGH | ⬜ |
| 1.1.2 | Setup `.env` dengan database configuration | HIGH | ⬜ |
| 1.1.3 | Install nwidart/laravel-modules: `composer require nwidart/laravel-modules` | HIGH | ⬜ |
| 1.1.4 | Publish module config: `php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"` | HIGH | ⬜ |
| 1.1.5 | Publish module stubs: `php artisan vendor:publish --tag=stubs` | HIGH | ⬜ |
| 1.1.6 | Replace stubs dengan custom stubs dari STUBS.md | HIGH | ⬜ |
| 1.1.7 | Install Inertia.js: `composer require inertiajs/inertia-laravel` | HIGH | ⬜ |
| 1.1.8 | Setup Inertia middleware: `php artisan inertia:middleware` | HIGH | ⬜ |
| 1.1.9 | Install Ziggy untuk routes: `composer require tightenco/ziggy` | HIGH | ⬜ |

### 1.2 Frontend Setup

| # | Task | Priority | Status |
|---|------|----------|--------|
| 1.2.1 | Install Vue.js 3 + Inertia client: `npm install vue @inertiajs/vue3` | HIGH | ⬜ |
| 1.2.2 | Install Vite Vue plugin: `npm install @vitejs/plugin-vue` | HIGH | ⬜ |
| 1.2.3 | Install Tailwind CSS: `npm install -D tailwindcss postcss autoprefixer` | HIGH | ⬜ |
| 1.2.4 | Initialize Tailwind: `npx tailwindcss init -p` | HIGH | ⬜ |
| 1.2.5 | Install dependencies: `npm install pinia lodash-es @heroicons/vue` | HIGH | ⬜ |

### 1.3 Configuration Files

| # | Task | Priority | Status |
|---|------|----------|--------|
| 1.3.1 | Configure `vite.config.js` dengan module aliases | HIGH | ⬜ |
| 1.3.2 | Configure `tailwind.config.js` dengan module paths | HIGH | ⬜ |
| 1.3.3 | Create `resources/views/app.blade.php` (Inertia root) | HIGH | ⬜ |
| 1.3.4 | Create `resources/js/app.js` (Vue + Inertia init) | HIGH | ⬜ |
| 1.3.5 | Configure `resources/css/app.css` dengan Tailwind | HIGH | ⬜ |
| 1.3.6 | Register Inertia middleware di `bootstrap/app.php` | HIGH | ⬜ |

---

## Phase 2: Database Schema

### 2.1 Migrations

| # | Task | Priority | Status |
|---|------|----------|--------|
| 2.1.1 | Create migration `create_modules_table` | HIGH | ⬜ |

```php
Schema::create('modules', function (Blueprint $table) {
    $table->id();
    $table->string('name');                    // 'User Management'
    $table->string('slug')->unique();          // 'user-management'
    $table->string('icon')->nullable();        // 'users'
    $table->string('route_name')->nullable();  // 'users.index'
    $table->integer('order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

| # | Task | Priority | Status |
|---|------|----------|--------|
| 2.1.2 | Modify `create_users_table` migration | HIGH | ⬜ |

```php
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
});
```

| # | Task | Priority | Status |
|---|------|----------|--------|
| 2.1.3 | Create migration `create_roles_table` | HIGH | ⬜ |

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');                    // 'Super Admin'
    $table->string('slug')->unique();          // 'super-admin'
    $table->text('description')->nullable();
    $table->json('permissions');               // {"read":["*"],"create":[1,2],...}
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

| # | Task | Priority | Status |
|---|------|----------|--------|
| 2.1.4 | Create migration `create_user_has_roles_table` | HIGH | ⬜ |

```php
Schema::create('user_has_roles', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('role_id')->constrained()->cascadeOnDelete();
    $table->primary(['user_id', 'role_id']);
});
```

| # | Task | Priority | Status |
|---|------|----------|--------|
| 2.1.5 | Run migrations: `php artisan migrate` | HIGH | ⬜ |

---

## Phase 3: Core Models & Traits

### 3.1 Models

| # | Task | Priority | Status |
|---|------|----------|--------|
| 3.1.1 | Create `app/Models/Module.php` | HIGH | ⬜ |
| 3.1.2 | Create `app/Models/Role.php` dengan permission methods | HIGH | ⬜ |
| 3.1.3 | Create `app/Traits/HasRoles.php` trait | HIGH | ⬜ |
| 3.1.4 | Update `app/Models/User.php` - use HasRoles trait | HIGH | ⬜ |

### 3.2 Helpers

| # | Task | Priority | Status |
|---|------|----------|--------|
| 3.2.1 | Create `app/Helpers/PermissionHelper.php` | HIGH | ⬜ |
| 3.2.2 | Register helper di `composer.json` autoload | HIGH | ⬜ |
| 3.2.3 | Run `composer dump-autoload` | HIGH | ⬜ |

### 3.3 Services

| # | Task | Priority | Status |
|---|------|----------|--------|
| 3.3.1 | Create `app/Services/AuthService.php` | HIGH | ⬜ |

### 3.4 Middleware

| # | Task | Priority | Status |
|---|------|----------|--------|
| 3.4.1 | Update `app/Http/Middleware/HandleInertiaRequests.php` | HIGH | ⬜ |
| 3.4.2 | Create `app/Http/Middleware/CheckModuleAccess.php` | HIGH | ⬜ |
| 3.4.3 | Create `app/Http/Middleware/CheckModulePermission.php` | HIGH | ⬜ |
| 3.4.4 | Register middleware aliases di `bootstrap/app.php` | HIGH | ⬜ |

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);

    $middleware->alias([
        'module.access' => \App\Http\Middleware\CheckModuleAccess::class,
        'module.permission' => \App\Http\Middleware\CheckModulePermission::class,
    ]);
})
```

---

## Phase 4: Shared Components & Base Controllers

### 4.1 PHP Helpers

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.1.1 | Create `app/Helpers/helpers.php` (global permission helpers) | HIGH | ⬜ |
| 4.1.2 | Register helper di `composer.json` autoload | HIGH | ⬜ |

### 4.2 Base Controllers

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.2.1 | Create `app/Http/Controllers/BaseController.php` | HIGH | ⬜ |

### 4.3 Shared Vue - Layouts

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.3.1 | Create `resources/js/Layouts/AppLayout.vue` | HIGH | ⬜ |
| 4.3.2 | Create `resources/js/Layouts/GuestLayout.vue` | HIGH | ⬜ |

### 4.4 Shared Vue - Components

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.4.1 | Create `resources/js/Components/Sidebar.vue` | HIGH | ⬜ |
| 4.4.2 | Create `resources/js/Components/Navbar.vue` | HIGH | ⬜ |
| 4.4.3 | Create `resources/js/Components/Pagination.vue` | HIGH | ⬜ |
| 4.4.4 | Create `resources/js/Components/Modal.vue` | HIGH | ⬜ |
| 4.4.5 | Create `resources/js/Components/ConfirmDialog.vue` | MEDIUM | ⬜ |
| 4.4.6 | Create `resources/js/Components/FlashMessage.vue` | HIGH | ⬜ |

### 4.5 Shared Vue - Form Components

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.5.1 | Create `resources/js/Components/Form/Input.vue` | HIGH | ⬜ |
| 4.5.2 | Create `resources/js/Components/Form/Select.vue` | HIGH | ⬜ |
| 4.5.3 | Create `resources/js/Components/Form/Checkbox.vue` | HIGH | ⬜ |
| 4.5.4 | Create `resources/js/Components/Form/Button.vue` | HIGH | ⬜ |
| 4.5.5 | Create `resources/js/Components/Form/TextArea.vue` | MEDIUM | ⬜ |

### 4.6 Shared Vue - Composables

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.6.1 | Create `resources/js/Composables/usePermission.js` | HIGH | ⬜ |
| 4.6.2 | Create `resources/js/Composables/useFlash.js` | HIGH | ⬜ |
| 4.6.3 | Create `resources/js/Composables/useConfirm.js` | MEDIUM | ⬜ |

### 4.7 Shared Vue - Helpers

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.7.1 | Create `resources/js/Helpers/formatters.js` | HIGH | ⬜ |

### 4.8 Module Pages

| # | Task | Priority | Status |
|---|------|----------|--------|
| 4.8.1 | Create `modules/Auth/resources/views/login.vue` | HIGH | ⬜ |
| 4.8.2 | Create `modules/Dashboard/resources/views/index.vue` | MEDIUM | ⬜ |
| 4.8.3 | Create `resources/js/Pages/errors/403.vue` | MEDIUM | ⬜ |
| 4.8.4 | Create `resources/js/Pages/errors/404.vue` | MEDIUM | ⬜ |

---

## Phase 5: User Management Module (ID: 1)

### 5.1 Module Creation

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.1.1 | Create module: `php artisan module:make UserManagement` | HIGH | ⬜ |
| 5.1.2 | Update `modules/UserManagement/module.json` | HIGH | ⬜ |
| 5.1.3 | Create `modules/UserManagement/config/config.php` dengan `module_id => 1` | HIGH | ⬜ |

### 5.2 Backend - Repository

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.2.1 | Create `modules/UserManagement/app/Repositories/Contracts/UserRepositoryInterface.php` | HIGH | ⬜ |
| 5.2.2 | Create `modules/UserManagement/app/Repositories/UserRepository.php` | HIGH | ⬜ |

### 5.3 Backend - Service

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.3.1 | Create `modules/UserManagement/app/Services/UserService.php` | HIGH | ⬜ |

### 5.4 Backend - Form Requests

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.4.1 | Create `modules/UserManagement/app/Http/Requests/StoreUserRequest.php` | HIGH | ⬜ |
| 5.4.2 | Create `modules/UserManagement/app/Http/Requests/UpdateUserRequest.php` | HIGH | ⬜ |

### 5.5 Backend - Resources

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.5.1 | Create `modules/UserManagement/app/Http/Resources/UserResource.php` | HIGH | ⬜ |

### 5.6 Backend - Controller

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.6.1 | Create `modules/UserManagement/app/Http/Controllers/UserController.php` | HIGH | ⬜ |

### 5.7 Backend - Routes

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.7.1 | Setup `modules/UserManagement/routes/web.php` dengan middleware | HIGH | ⬜ |

```php
Route::middleware(['auth', 'module.access:1'])
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');

        Route::middleware('module.permission:1,create')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        Route::middleware('module.permission:1,update')->group(function () {
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
        });

        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->name('destroy')
            ->middleware('module.permission:1,delete');
    });
```

### 5.8 Backend - Provider

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.8.1 | Update `modules/UserManagement/app/Providers/UserManagementServiceProvider.php` | HIGH | ⬜ |

### 5.9 Frontend - Pages

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.9.1 | Create `modules/UserManagement/resources/views/index.vue` | HIGH | ⬜ |
| 5.9.2 | Create `modules/UserManagement/resources/views/create.vue` | HIGH | ⬜ |
| 5.9.3 | Create `modules/UserManagement/resources/views/edit.vue` | HIGH | ⬜ |
| 5.9.4 | Create `modules/UserManagement/resources/views/show.vue` | MEDIUM | ⬜ |

### 5.10 Frontend - Components

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.10.1 | Create `modules/UserManagement/resources/components/UserForm.vue` | HIGH | ⬜ |
| 5.10.2 | Create `modules/UserManagement/resources/components/UserTable.vue` | HIGH | ⬜ |
| 5.10.3 | Create `modules/UserManagement/resources/components/UserFilters.vue` | MEDIUM | ⬜ |
| 5.10.4 | Create `modules/UserManagement/resources/components/RoleSelector.vue` | HIGH | ⬜ |

### 5.11 Frontend - Composables

| # | Task | Priority | Status |
|---|------|----------|--------|
| 5.11.1 | Create `modules/UserManagement/resources/composable/useUser.js` | MEDIUM | ⬜ |

---

## Phase 6: Role & Permission Module (ID: 2)

### 6.1 Module Creation

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.1.1 | Create module: `php artisan module:make RolePermission` | HIGH | ⬜ |
| 6.1.2 | Update `modules/RolePermission/module.json` | HIGH | ⬜ |
| 6.1.3 | Create `modules/RolePermission/config/config.php` dengan `module_id => 2` | HIGH | ⬜ |

### 6.2 Backend - Repository

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.2.1 | Create `modules/RolePermission/app/Repositories/Contracts/RoleRepositoryInterface.php` | HIGH | ⬜ |
| 6.2.2 | Create `modules/RolePermission/app/Repositories/RoleRepository.php` | HIGH | ⬜ |

### 6.3 Backend - Service

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.3.1 | Create `modules/RolePermission/app/Services/RoleService.php` | HIGH | ⬜ |

### 6.4 Backend - Form Requests

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.4.1 | Create `modules/RolePermission/app/Http/Requests/StoreRoleRequest.php` | HIGH | ⬜ |
| 6.4.2 | Create `modules/RolePermission/app/Http/Requests/UpdateRoleRequest.php` | HIGH | ⬜ |

### 6.5 Backend - Resources

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.5.1 | Create `modules/RolePermission/app/Http/Resources/RoleResource.php` | HIGH | ⬜ |

### 6.6 Backend - Controller

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.6.1 | Create `modules/RolePermission/app/Http/Controllers/RoleController.php` | HIGH | ⬜ |

### 6.7 Backend - Routes

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.7.1 | Setup `modules/RolePermission/routes/web.php` dengan middleware | HIGH | ⬜ |

```php
Route::middleware(['auth', 'module.access:2'])
    ->prefix('roles')
    ->name('roles.')
    ->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show');

        Route::middleware('module.permission:2,create')->group(function () {
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
        });

        Route::middleware('module.permission:2,update')->group(function () {
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        });

        Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->name('destroy')
            ->middleware('module.permission:2,delete');
    });
```

### 6.8 Backend - Provider

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.8.1 | Update `modules/RolePermission/app/Providers/RolePermissionServiceProvider.php` | HIGH | ⬜ |

### 6.9 Frontend - Pages

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.9.1 | Create `modules/RolePermission/resources/views/index.vue` | HIGH | ⬜ |
| 6.9.2 | Create `modules/RolePermission/resources/views/create.vue` | HIGH | ⬜ |
| 6.9.3 | Create `modules/RolePermission/resources/views/edit.vue` | HIGH | ⬜ |
| 6.9.4 | Create `modules/RolePermission/resources/views/show.vue` | MEDIUM | ⬜ |

### 6.10 Frontend - Components

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.10.1 | Create `modules/RolePermission/resources/components/RoleForm.vue` | HIGH | ⬜ |
| 6.10.2 | Create `modules/RolePermission/resources/components/RoleTable.vue` | HIGH | ⬜ |
| 6.10.3 | Create `modules/RolePermission/resources/components/PermissionMatrix.vue` | HIGH | ⬜ |

### 6.11 Frontend - Composables

| # | Task | Priority | Status |
|---|------|----------|--------|
| 6.11.1 | Create `modules/RolePermission/resources/composable/useRole.js` | MEDIUM | ⬜ |

---

## Phase 7: Seeders

### 7.1 Database Seeders

| # | Task | Priority | Status |
|---|------|----------|--------|
| 7.1.1 | Create `database/seeders/ModuleSeeder.php` | HIGH | ⬜ |

```php
$modules = [
    ['id' => 1, 'name' => 'User Management', 'slug' => 'user-management', 'icon' => 'users', 'route_name' => 'users.index', 'order' => 1],
    ['id' => 2, 'name' => 'Role & Permission', 'slug' => 'role-permission', 'icon' => 'shield-check', 'route_name' => 'roles.index', 'order' => 2],
    ['id' => 3, 'name' => 'Dashboard', 'slug' => 'dashboard', 'icon' => 'home', 'route_name' => 'dashboard', 'order' => 0],
];
```

| # | Task | Priority | Status |
|---|------|----------|--------|
| 7.1.2 | Create `database/seeders/RoleSeeder.php` | HIGH | ⬜ |

```php
// Super Admin - Full Access
[
    'name' => 'Super Admin',
    'slug' => 'super-admin',
    'permissions' => [
        'read' => ['*'],
        'create' => ['*'],
        'update' => ['*'],
        'delete' => ['*'],
    ],
]

// Admin - Manage users only
[
    'name' => 'Admin',
    'slug' => 'admin',
    'permissions' => [
        'read' => [1, 2, 3],    // User, Role, Dashboard
        'create' => [1],         // Only User
        'update' => [1],         // Only User
        'delete' => [1],         // Only User
    ],
]

// Manager - View only
[
    'name' => 'Manager',
    'slug' => 'manager',
    'permissions' => [
        'read' => [1, 3],       // User, Dashboard
        'create' => [],
        'update' => [],
        'delete' => [],
    ],
]
```

| # | Task | Priority | Status |
|---|------|----------|--------|
| 7.1.3 | Create `database/seeders/AdminUserSeeder.php` | HIGH | ⬜ |
| 7.1.4 | Update `database/seeders/DatabaseSeeder.php` | HIGH | ⬜ |
| 7.1.5 | Run seeders: `php artisan db:seed` | HIGH | ⬜ |

---

## Phase 8: Testing & Verification

### 8.1 Build & Test

| # | Task | Priority | Status |
|---|------|----------|--------|
| 8.1.1 | Run `npm run build` - verify frontend | HIGH | ⬜ |
| 8.1.2 | Run `php artisan route:list` - verify routes | HIGH | ⬜ |
| 8.1.3 | Test login dengan Super Admin | HIGH | ⬜ |
| 8.1.4 | Verify sidebar menampilkan semua module | HIGH | ⬜ |
| 8.1.5 | Test login dengan Manager (view only) | HIGH | ⬜ |
| 8.1.6 | Verify tombol Add/Edit/Delete tersembunyi | HIGH | ⬜ |
| 8.1.7 | Test middleware block unauthorized access | HIGH | ⬜ |

### 8.2 Code Quality

| # | Task | Priority | Status |
|---|------|----------|--------|
| 8.2.1 | Run `./vendor/bin/pint` - format PHP | MEDIUM | ⬜ |

---

## Quick Commands Reference

```bash
# Phase 1: Setup
composer create-project laravel/laravel .
composer require nwidart/laravel-modules inertiajs/inertia-laravel tightenco/ziggy
npm install vue @inertiajs/vue3 @vitejs/plugin-vue
npm install -D tailwindcss postcss autoprefixer
npm install pinia lodash-es @heroicons/vue
npx tailwindcss init -p
php artisan inertia:middleware

# Module commands
php artisan module:make Core
php artisan module:make UserManagement
php artisan module:make RolePermission

# Database
php artisan migrate
php artisan db:seed

# Development
php artisan serve
npm run dev

# Code quality
./vendor/bin/pint
```

---

## Task Summary by Phase

| Phase | Description | HIGH | MEDIUM | Total |
|-------|-------------|------|--------|-------|
| 1 | Project Setup | 12 | 0 | 12 |
| 2 | Database Schema | 5 | 0 | 5 |
| 3 | Core Models & Traits | 10 | 0 | 10 |
| 4 | Core Module | 21 | 6 | 27 |
| 5 | User Management | 16 | 3 | 19 |
| 6 | Role & Permission | 15 | 2 | 17 |
| 7 | Seeders | 5 | 0 | 5 |
| 8 | Testing | 7 | 1 | 8 |
| **TOTAL** | | **91** | **12** | **103** |

---

## Permission Flow Summary

```
LOGIN
  │
  ▼
┌─────────────────────────────────────────┐
│ AuthService::loadUserSession()          │
│ ─────────────────────────────────────── │
│ 1. Get user's roles                     │
│ 2. Merge permissions from all roles     │
│ 3. Get readable modules for sidebar     │
│ 4. Store in session:                    │
│    - auth.user                          │
│    - auth.roles                         │
│    - auth.permissions                   │
│    - auth.modules                       │
└─────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────┐
│ SIDEBAR                                 │
│ ─────────────────────────────────────── │
│ Loop session('auth.modules')            │
│ Only shows modules with READ permission │
└─────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────┐
│ PAGE ACCESS                             │
│ ─────────────────────────────────────── │
│ middleware('module.access:1')           │
│ Check: can_read(1) === true?            │
│ If false → 403 Forbidden                │
└─────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────┐
│ ACTION ACCESS                           │
│ ─────────────────────────────────────── │
│ middleware('module.permission:1,create')│
│ Check: can_create(1) === true?          │
│ If false → 403 Forbidden                │
└─────────────────────────────────────────┘
  │
  ▼
┌─────────────────────────────────────────┐
│ UI BUTTONS                              │
│ ─────────────────────────────────────── │
│ v-if="canCreate" → Show Add button      │
│ v-if="canUpdate" → Show Edit button     │
│ v-if="canDelete" → Show Delete button   │
└─────────────────────────────────────────┘
```

---

## Notes untuk Claude Code

1. **Module ID** adalah kunci - setiap module punya ID unik yang digunakan untuk permission
2. **Permission check** menggunakan array lookup - sangat cepat
3. **Session-based** - permission di-load sekali saat login, tidak query DB setiap request
4. **Sidebar dynamic** - hanya tampilkan module yang user bisa READ
5. **UI conditional** - tombol action di-hide berdasarkan permission
6. **Middleware protection** - backend juga check permission, bukan hanya UI

---

## Related Documentation

| File | Description |
|------|-------------|
| [ARCHITECTURE.md](./ARCHITECTURE.md) | Full architecture design, code patterns, security layers |
| [DATABASE.md](./DATABASE.md) | Database schema, migrations, seeders, ERD |
| [STUBS.md](./STUBS.md) | Module stub templates untuk generate module baru |

---

## Implementation Guidelines

### Code Quality Standards

- PHP: `declare(strict_types=1)` di setiap file
- PHP: PSR-12 coding standard, gunakan `./vendor/bin/pint`
- Vue: Composition API dengan `<script setup>`
- Vue: Props validation dengan types
- Comments: Hanya untuk "why", bukan "what"
- No hardcoded credentials

### Git Commit Format

```bash
# Format: <type>: <description>
feat: Add user management module
fix: Resolve permission check issue
refactor: Extract permission logic to helper
docs: Update architecture documentation
```

### Testing Requirements

- Setiap Service harus punya Unit Test
- Setiap Controller action harus punya Feature Test
- Permission check harus di-test
