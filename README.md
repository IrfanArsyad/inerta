# Inerta - Laravel Modular Monolith Starter Kit

A production-ready Laravel 12 modular monolith starter kit with Vue.js 3, Inertia.js, and Pathify.

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue.js 3 (Composition API), Inertia.js
- **CSS**: Bootstrap 5 SCSS (Velzon Material Theme)
- **Module System**: nwidart/laravel-modules
- **Route Helper**: Pathify (Ziggy alternative)

## Features

- **Modular Architecture**: Organized into self-contained modules
- **Authentication**: Complete auth system with session-based login
- **Role & Permission Management**: Flexible RBAC with module-level permissions
- **User Management**: Full CRUD with role assignment
- **Module Management**: Dynamic module registration and configuration
- **Global Components**: Pre-registered Vue components with `c-` prefix
- **Responsive Design**: Mobile-friendly Bootstrap 5 layout

## Directory Structure

```
├── app/
│   ├── Helpers/          # Global helper functions
│   ├── Http/
│   │   ├── Controllers/  # Base controller
│   │   └── Middleware/   # Custom middleware
│   ├── Models/           # Eloquent models
│   └── Services/         # Business logic services
├── config/
│   ├── modules.php       # nwidart/laravel-modules config
│   └── pathify.php       # Pathify route helper config
├── modules/
│   ├── Auth/             # Authentication module
│   ├── Dashboard/        # Dashboard module
│   ├── UserManagement/   # User CRUD module
│   ├── RolePermission/   # Role & permission module
│   └── ModuleManagement/ # Module management
├── resources/
│   ├── js/
│   │   ├── Components/   # Vue components
│   │   ├── Composables/  # Vue composables
│   │   ├── Helpers/      # JS helpers
│   │   ├── Layouts/      # App layouts
│   │   └── app.js        # Vue app entry
│   └── scss/             # Bootstrap SCSS
├── stubs/
│   └── nwidart-stubs/    # Module generator stubs
└── vendor/
    └── herolabid/pathify # Pathify package
```

## Installation

### 1. Clone Repository

```bash
git clone git@github.com:IrfanArsyad/inerta.git
cd inerta
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 5. Generate Pathify Routes

```bash
php artisan pathify:generate
```

### 6. Build Assets

```bash
npm run build
# or for development
npm run dev
```

### 7. Start Server

```bash
php artisan serve
```

## Default Credentials

- **Email**: admin@example.com
- **Password**: password

## Module System

### Creating New Module

```bash
php artisan module:make ModuleName
```

This uses custom stubs from `stubs/nwidart-stubs/` that include:
- Repository pattern
- Service layer
- Form requests
- Vue views with modal CRUD

### Module Structure

```
modules/ModuleName/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Repositories/
│   │   └── Contracts/
│   ├── Services/
│   └── Providers/
├── config/
├── database/
│   └── seeders/
├── resources/
│   └── views/           # Vue components
├── routes/
│   └── web.php
└── module.json
```

## Permission System

Permissions are stored in roles as JSON arrays:

```php
[
    'read' => [1, 2, 3],    // Module IDs with read access
    'create' => [1],        // Module IDs with create access
    'update' => [1],        // Module IDs with update access
    'delete' => [],         // Module IDs with delete access
]
```

Use `['*']` for full access to all modules.

### Middleware Usage

```php
// Read access only
Route::middleware(['auth', 'module.access:1']);

// Specific permission
Route::middleware(['auth', 'module.permission:1,create']);
```

### Helper Functions

```php
can_read($moduleId)
can_create($moduleId)
can_update($moduleId)
can_delete($moduleId)
can_access($action, $moduleId)
```

## Pathify (Route Helper)

### Configuration

Pathify is initialized in `resources/js/app.js`:

```javascript
import { PathifyVue, setConfig } from '../../vendor/herolabid/pathify/resources/js/vue'
import PathifyConfig from './pathify.js'

// In setup
setConfig(PathifyConfig)
app.use(PathifyVue)
```

### Usage in Vue

```vue
<template>
  <a :href="route('users.index')">Users</a>
</template>

<script setup>
// route() is available globally in templates
// For script, use explicit URL or router
</script>
```

### Regenerate Routes

```bash
php artisan pathify:generate
```

## Global Components

Registered in `app.js` with `c-` prefix:

| Component | Usage |
|-----------|-------|
| c-link | Inertia Link |
| c-page-header | Page header with breadcrumbs |
| c-data-table | Data table with pagination |
| c-table-filters | Search and filter bar |
| c-confirm-dialog | Delete confirmation |
| c-flash-message | Toast notifications |
| c-input | Form input |
| c-select | Form select |
| c-checkbox | Form checkbox/switch |
| c-button | Form button with loading |
| c-textarea | Form textarea |

## Composables

### usePermission

```javascript
import { usePermission } from '@/Composables/usePermission'

const { canCreate, canUpdate, canDelete } = usePermission(moduleId)
```

### useConfirm

```javascript
import { useConfirm } from '@/Composables/useConfirm'

const { confirmDelete } = useConfirm()
const confirmed = await confirmDelete('Delete this item?')
```

## License

MIT License

## Author

Irfan Arsyad
