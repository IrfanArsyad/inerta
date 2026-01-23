# Laravel Modular Admin Panel (Velzon Template)

Laravel 12 modular monolith admin panel with Inertia.js + Vue.js 3, using **Module-ID Based Permission System**.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue.js 3 (Composition API), Inertia.js |
| Styling | Tailwind CSS 4 |
| Module System | nwidart/laravel-modules |
| State Management | Pinia |

## Requirements

- PHP 8.2+
- Node.js 20+
- Composer 2.x
- MySQL 8.0+ / PostgreSQL 15+

## Installation

```bash
# Clone repository
git clone <repository-url>
cd vue-velzon

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build
```

## Development

```bash
# Run all services concurrently (recommended)
composer dev

# Or run separately
php artisan serve    # Laravel server
npm run dev          # Vite dev server
```

## Project Structure

```
vue-velzon/
├── app/                         # Core Laravel app
│   ├── Helpers/                 # Global helpers
│   ├── Http/Middleware/         # Auth & permission middleware
│   ├── Models/                  # Core models (User, Role, Module)
│   ├── Services/                # Core services (AuthService)
│   └── Traits/                  # Reusable traits (HasRoles)
├── resources/js/                # Shared Vue components (alias: @)
│   ├── Layouts/                 # AppLayout.vue, GuestLayout.vue
│   ├── Components/              # Shared UI components
│   ├── Composables/             # usePermission.js, useFlash.js
│   └── Helpers/                 # formatters.js
├── modules/                     # Feature modules (lowercase)
│   ├── Auth/                    # Authentication module
│   │   └── resources/views/     # login.vue (lowercase)
│   ├── Dashboard/               # Dashboard module
│   │   └── resources/views/     # index.vue
│   ├── UserManagement/          # User Management (Module ID: 1)
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/
│   │   │   │   ├── Requests/
│   │   │   │   └── Resources/
│   │   │   ├── Repositories/
│   │   │   └── Services/
│   │   └── resources/
│   │       ├── views/           # index.vue, create.vue, edit.vue, show.vue
│   │       ├── components/      # UserForm.vue, UserTable.vue
│   │       └── composable/      # useUser.js
│   └── RolePermission/          # Role & Permission (Module ID: 2)
│       └── ...                  # Same structure as UserManagement
├── themes/material/             # UI template reference
├── ARCHITECTURE.md              # Full architecture documentation
├── DATABASE.md                  # Database schema & ERD
├── STUBS.md                     # Module stub templates
├── TASKS.md                     # Implementation task list
└── CLAUDE.md                    # Claude Code instructions
```

## Permission System

Permissions are stored as JSON with module IDs:

```php
// Role permissions
[
    'read'   => ['*'],        // ['*'] = full access, [1,2,3] = specific modules
    'create' => [1, 2],       // Module IDs that can create
    'update' => [1],          // Module IDs that can update
    'delete' => [],           // Empty = no delete access
]
```

### Module IDs

| ID | Module |
|----|--------|
| 1 | User Management |
| 2 | Role & Permission |
| 3 | Dashboard |

### Permission Flow

1. **Login** - `AuthService::loadUserSession()` merges all role permissions → stored in session
2. **Sidebar** - Only shows modules where user has `read` permission
3. **Middleware** - `module.access:1` checks read, `module.permission:1,create` checks specific action
4. **UI** - `v-if="canCreate"` conditionally shows buttons

## Commands Reference

```bash
# Development
php artisan serve              # Run Laravel server
npm run dev                    # Run Vite dev server
composer dev                   # Run all services concurrently

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
```

## Documentation

| File | Description |
|------|-------------|
| [ARCHITECTURE.md](./ARCHITECTURE.md) | Full architecture design, code patterns, security layers |
| [DATABASE.md](./DATABASE.md) | Database schema, migrations, ERD, permission system |
| [STUBS.md](./STUBS.md) | Module stub templates for generating new modules |
| [TASKS.md](./TASKS.md) | Implementation task list (103+ tasks) |
| [CLAUDE.md](./CLAUDE.md) | Claude Code instructions for AI assistance |

## Default Credentials

```
Email: admin@example.com
Password: password
Role: Super Admin (full access)
```

## License

MIT License
