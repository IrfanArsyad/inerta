# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 12 modular monolith with Inertia.js + Vue.js 3, using **Module-ID Based Permission System**. This is an admin panel template based on Velzon theme.

**Tech Stack:**
- Backend: Laravel 12, PHP 8.2+
- Frontend: Vue.js 3 (Composition API), Inertia.js
- Styling: Tailwind CSS 4
- Module System: nwidart/laravel-modules
- State Management: Pinia

## Essential Reference Files

**Always read these files before implementing features:**
- `ARCHITECTURE.md` - Full architecture design, code patterns, security layers
- `DATABASE.md` - Database schema, migrations, ERD, permission system
- `STUBS.md` - Module stub templates for generating new modules
- `TASKS.md` - Implementation task list (103+ tasks)

## Commands

```bash
# Development
php artisan serve              # Run Laravel server
npm run dev                    # Run Vite dev server
composer dev                   # Run all services concurrently (server, queue, logs, vite)

# Testing & Quality
php artisan test               # Run tests
./vendor/bin/pint              # Format PHP code (PSR-12)

# Database
php artisan migrate            # Run migrations
php artisan db:seed            # Run seeders
php artisan migrate:fresh --seed  # Reset and seed database

# Module Management
php artisan module:make ModuleName           # Create new module
php artisan module:migrate ModuleName        # Run module migrations
php artisan module:make-controller Name Module
php artisan module:make-model Name Module
php artisan module:make-migration name Module
```

## Architecture

### Project Structure

```
resources/js/                  # Shared components (alias: @)
├── Layouts/                   # AppLayout.vue, GuestLayout.vue
├── Components/                # Shared UI components
│   └── Form/                  # Input.vue, Select.vue, Button.vue
├── Composables/               # usePermission.js, useFlash.js
└── Helpers/                   # formatters.js

modules/                       # Feature modules (lowercase)
├── Auth/
│   └── resources/views/       # login.vue
├── Dashboard/
│   └── resources/views/       # index.vue
├── UserManagement/            # Module ID: 1
│   └── resources/
│       ├── views/             # index.vue, create.vue, edit.vue, show.vue
│       ├── components/        # Module-specific components
│       └── composable/        # useUser.js
└── RolePermission/            # Module ID: 2
    └── resources/
        ├── views/             # index.vue, create.vue, edit.vue, show.vue
        ├── components/        # RoleForm.vue, PermissionMatrix.vue
        └── composable/        # useRole.js
```

### Template Location

The UI template is in `themes/material/` - reference this for components and styling.

### Permission System

Permissions are stored as JSON with module IDs:
```php
// Role permissions structure
[
    'read'   => ['*'],        // ['*'] = full access, [1,2,3] = specific modules
    'create' => [1, 2],       // Module IDs that can create
    'update' => [1],          // Module IDs that can update
    'delete' => [],           // Empty = no delete access
]
```

**Permission Flow:**
1. Login → `AuthService::loadUserSession()` merges all role permissions → stored in session
2. Sidebar → only shows modules where user has `read` permission
3. Middleware → `module.access:1` checks read, `module.permission:1,create` checks specific action
4. UI → `v-if="canCreate"` conditionally shows buttons

### Key Patterns

**Backend:**
- Controllers extend `BaseController` with `$moduleId` and `$moduleName`
- Use Services for business logic, Repositories for data access
- Form Requests handle validation AND authorization via `can_access()`
- Routes use middleware: `module.access:{id}` and `module.permission:{id},{action}`

**Frontend (Vue):**
- Use `usePermission()` composable for permission checks
- Import layouts from `@/Layouts/`
- Import shared components from `@/Components/`

### Module Creation Checklist

1. `php artisan module:make ModuleName`
2. Add to `modules` table with unique ID
3. Set `module_id` in `modules/ModuleName/config/config.php`
4. Create Repository (Interface + Implementation) in `modules/ModuleName/app/Repositories/`
5. Create Service in `modules/ModuleName/app/Services/`
6. Create Form Requests with `can_access()` authorization in `modules/ModuleName/app/Http/Requests/`
7. Create Resource for JSON transformation in `modules/ModuleName/app/Http/Resources/`
8. Create Controller extending BaseController in `modules/ModuleName/app/Http/Controllers/`
9. Setup routes with middleware in `modules/ModuleName/routes/web.php`
10. Create Vue pages in `modules/ModuleName/resources/views/` (lowercase filenames: index.vue, create.vue, edit.vue, show.vue)
11. Create Vue components in `modules/ModuleName/resources/components/` (PascalCase: UserForm.vue)
12. Create Vue composables in `modules/ModuleName/resources/composable/` (useUser.js)
13. Update role permissions if needed
14. Shared components → taruh di `resources/js/Components/`
15. Shared helpers → taruh di `resources/js/Helpers/` 


## Database Tables

| Table | Purpose |
|-------|---------|
| `modules` | System modules (id, name, slug, icon, route_name, order) |
| `users` | User accounts with soft delete |
| `roles` | Roles with JSON permissions column |
| `user_has_roles` | Pivot table (user_id, role_id) |

## Middleware Aliases

```php
'module.access' => CheckModuleAccess::class,      // Check read permission
'module.permission' => CheckModulePermission::class, // Check create/update/delete
```

## Helper Functions

```php
can_access($action, $moduleId)   // Check any permission
can_read($moduleId)              // Check read
can_create($moduleId)            // Check create
can_update($moduleId)            // Check update
can_delete($moduleId)            // Check delete
module_permissions($moduleId)    // Get all permissions for module (for Vue)
sidebar_modules()                // Get accessible modules for sidebar
```
