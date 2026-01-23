# Module Stubs - Template Structure

Dokumentasi untuk stub files yang digunakan saat generate module baru.

---

## Overview

Stubs adalah template files yang akan di-generate saat menjalankan:
```bash
php artisan module:make ModuleName
```

Custom stubs akan override default stubs dari nwidart/laravel-modules.

---

## Module Directory Structure

```
modules/                              # lowercase
└── ModuleName/
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   └── ModuleNameController.php
    │   │   ├── Requests/
    │   │   │   ├── StoreRequest.php
    │   │   │   └── UpdateRequest.php
    │   │   └── Resources/
    │   │       └── ModuleNameResource.php
    │   ├── Models/
    │   │   └── ModuleName.php
    │   ├── Providers/
    │   │   ├── ModuleNameServiceProvider.php
    │   │   └── RouteServiceProvider.php
    │   ├── Repositories/
    │   │   ├── Contracts/
    │   │   │   └── ModuleNameRepositoryInterface.php
    │   │   └── ModuleNameRepository.php
    │   └── Services/
    │       └── ModuleNameService.php
    ├── config/
    │   └── config.php
    ├── database/
    │   ├── factories/
    │   ├── migrations/
    │   └── seeders/
    ├── resources/                    # lowercase
    │   ├── views/                    # Vue pages
    │   │   ├── index.vue             # lowercase filenames
    │   │   ├── create.vue
    │   │   ├── edit.vue
    │   │   └── show.vue
    │   ├── components/               # Vue components
    │   │   ├── Form.vue
    │   │   ├── Table.vue
    │   │   └── Filters.vue
    │   └── composable/               # Vue composables
    │       └── useModuleName.js
    ├── routes/
    │   ├── web.php
    │   └── api.php
    ├── tests/
    │   ├── Feature/
    │   └── Unit/
    ├── composer.json
    └── module.json
```

---

## Stubs Directory Structure

```
stubs/
└── nwidart-stubs/
    ├── controller.stub
    ├── controller-api.stub
    ├── model.stub
    ├── migration.stub
    ├── seeder.stub
    ├── factory.stub
    ├── request.stub
    ├── resource.stub
    ├── service.stub
    ├── repository.stub
    ├── repository-interface.stub
    ├── provider.stub
    ├── route-provider.stub
    ├── routes-web.stub
    ├── routes-api.stub
    ├── config.stub
    ├── module.stub
    ├── composer.stub
    ├── vite.stub
    ├── vue/
    │   ├── page-index.stub
    │   ├── page-create.stub
    │   ├── page-edit.stub
    │   ├── page-show.stub
    │   ├── component-form.stub
    │   ├── component-table.stub
    │   ├── component-filters.stub
    │   └── composable.stub
    └── test/
        ├── feature.stub
        └── unit.stub
```

---

## PHP Stubs

### controller.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use $MODULE_NAMESPACE$\Http\Requests\Store$NAME$Request;
use $MODULE_NAMESPACE$\Http\Requests\Update$NAME$Request;
use $MODULE_NAMESPACE$\Http\Resources\$NAME$Resource;
use $MODULE_NAMESPACE$\Services\$NAME$Service;

class $CLASS$ extends Controller
{
    protected int $moduleId = $MODULE_ID$;
    protected string $moduleName = '$MODULE$';

    public function __construct(
        protected $NAME$Service $$LOWER_NAME$Service
    ) {}

    public function index(): Response
    {
        $$PLURAL_LOWER_NAME$ = $this->$LOWER_NAME$Service->paginate(
            request()->only(['search']),
            request()->integer('per_page', 15)
        );

        return Inertia::render("{$this->moduleName}::views/index", [
            '$PLURAL_LOWER_NAME$' => $NAME$Resource::collection($$PLURAL_LOWER_NAME$),
            'filters' => request()->only(['search']),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->moduleName}::views/create", [
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function store(Store$NAME$Request $request): RedirectResponse
    {
        $this->$LOWER_NAME$Service->create($request->validated());

        return redirect()
            ->route('$PLURAL_LOWER_NAME$.index')
            ->with('success', '$NAME$ berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        $$LOWER_NAME$ = $this->$LOWER_NAME$Service->find($id);

        return Inertia::render("{$this->moduleName}::views/show", [
            '$LOWER_NAME$' => new $NAME$Resource($$LOWER_NAME$),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function edit(int $id): Response
    {
        $$LOWER_NAME$ = $this->$LOWER_NAME$Service->find($id);

        return Inertia::render("{$this->moduleName}::views/edit", [
            '$LOWER_NAME$' => new $NAME$Resource($$LOWER_NAME$),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function update(Update$NAME$Request $request, int $id): RedirectResponse
    {
        $this->$LOWER_NAME$Service->update($id, $request->validated());

        return redirect()
            ->route('$PLURAL_LOWER_NAME$.index')
            ->with('success', '$NAME$ berhasil diupdate.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->$LOWER_NAME$Service->delete($id);

        return redirect()
            ->route('$PLURAL_LOWER_NAME$.index')
            ->with('success', '$NAME$ berhasil dihapus.');
    }
}
```

### service.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use $MODULE_NAMESPACE$\Models\$NAME$;
use $MODULE_NAMESPACE$\Repositories\Contracts\$NAME$RepositoryInterface;

class $CLASS$
{
    public function __construct(
        protected $NAME$RepositoryInterface $repository
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id): $NAME$
    {
        $model = $this->repository->find($id);

        if (!$model) {
            abort(404, '$NAME$ tidak ditemukan.');
        }

        return $model;
    }

    public function create(array $data): $NAME$
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    public function update(int $id, array $data): $NAME$
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->repository->update($id, $data);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }
}
```

### repository-interface.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Pagination\LengthAwarePaginator;
use $MODULE_NAMESPACE$\Models\$NAME$;

interface $CLASS$
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?$NAME$;

    public function create(array $data): $NAME$;

    public function update(int $id, array $data): $NAME$;

    public function delete(int $id): bool;
}
```

### repository.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Pagination\LengthAwarePaginator;
use $MODULE_NAMESPACE$\Models\$NAME$;
use $MODULE_NAMESPACE$\Repositories\Contracts\$NAME$RepositoryInterface;

class $CLASS$ implements $NAME$RepositoryInterface
{
    public function __construct(
        protected $NAME$ $model
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): ?$NAME$
    {
        return $this->model->find($id);
    }

    public function create(array $data): $NAME$
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): $NAME$
    {
        $model = $this->find($id);
        $model->update($data);

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }
}
```

### request.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Foundation\Http\FormRequest;

class $CLASS$ extends FormRequest
{
    public function authorize(): bool
    {
        return can_access('$ACTION$', $MODULE_ID$);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
        ];
    }
}
```

### store-request.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Foundation\Http\FormRequest;

class $CLASS$ extends FormRequest
{
    public function authorize(): bool
    {
        return can_create($MODULE_ID$);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
        ];
    }
}
```

### update-request.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class $CLASS$ extends FormRequest
{
    public function authorize(): bool
    {
        return can_update($MODULE_ID$);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
        ];
    }
}
```

### resource.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class $CLASS$ extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

### model.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class $CLASS$ extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

### migration.stub

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('$TABLE$', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('$TABLE$');
    }
};
```

### seeder.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Database\Seeder;
use $MODULE_NAMESPACE$\Models\$NAME$;

class $CLASS$ extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Sample 1', 'is_active' => true],
            ['name' => 'Sample 2', 'is_active' => true],
        ];

        foreach ($items as $item) {
            $NAME$::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
```

### provider.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use $MODULE_NAMESPACE$\Repositories\Contracts\$NAME$RepositoryInterface;
use $MODULE_NAMESPACE$\Repositories\$NAME$Repository;

class $CLASS$ extends ServiceProvider
{
    protected string $moduleName = '$MODULE$';

    protected string $moduleNameLower = '$LOWER_NAME$';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        // Register Repository Bindings
        $this->app->bind(
            $NAME$RepositoryInterface::class,
            $NAME$Repository::class
        );
    }

    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'),
            $this->moduleNameLower
        );
    }
}
```

### route-provider.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class $CLASS$ extends ServiceProvider
{
    protected string $moduleNamespace = '$MODULE_NAMESPACE$\Http\Controllers';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('$MODULE$', '/routes/web.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('$MODULE$', '/routes/api.php'));
    }
}
```

### routes-web.stub

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use $MODULE_NAMESPACE$\Http\Controllers\$NAME$Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Module: $MODULE$
| Module ID: $MODULE_ID$
|
*/

Route::middleware(['auth', 'module.access:$MODULE_ID$'])
    ->prefix('$PLURAL_LOWER_NAME$')
    ->name('$PLURAL_LOWER_NAME$.')
    ->group(function () {
        // Read - accessible with module.access
        Route::get('/', [$NAME$Controller::class, 'index'])->name('index');
        Route::get('/{id}', [$NAME$Controller::class, 'show'])
            ->name('show')
            ->where('id', '[0-9]+');

        // Create - requires create permission
        Route::middleware('module.permission:$MODULE_ID$,create')->group(function () {
            Route::get('/create', [$NAME$Controller::class, 'create'])->name('create');
            Route::post('/', [$NAME$Controller::class, 'store'])->name('store');
        });

        // Update - requires update permission
        Route::middleware('module.permission:$MODULE_ID$,update')->group(function () {
            Route::get('/{id}/edit', [$NAME$Controller::class, 'edit'])
                ->name('edit')
                ->where('id', '[0-9]+');
            Route::put('/{id}', [$NAME$Controller::class, 'update'])
                ->name('update')
                ->where('id', '[0-9]+');
        });

        // Delete - requires delete permission
        Route::delete('/{id}', [$NAME$Controller::class, 'destroy'])
            ->name('destroy')
            ->where('id', '[0-9]+')
            ->middleware('module.permission:$MODULE_ID$,delete');
    });
```

### routes-api.stub

```php
<?php

declare(strict_types=1);

// API routes not used for Inertia-based application
```

### config.stub

```php
<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Module ID
    |--------------------------------------------------------------------------
    |
    | Unique identifier for this module. Used for permission checks.
    | IMPORTANT: This must match the ID in the modules database table.
    |
    */
    'module_id' => $MODULE_ID$,

    /*
    |--------------------------------------------------------------------------
    | Module Name
    |--------------------------------------------------------------------------
    */
    'name' => '$MODULE$',

    /*
    |--------------------------------------------------------------------------
    | Module Settings
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'per_page' => 15,
    ],
];
```

### module.stub

```json
{
    "name": "$MODULE$",
    "alias": "$LOWER_NAME$",
    "description": "$MODULE$ module",
    "keywords": [],
    "priority": 0,
    "providers": [
        "$MODULE_NAMESPACE$\\Providers\\$MODULE$ServiceProvider",
        "$MODULE_NAMESPACE$\\Providers\\RouteServiceProvider"
    ],
    "files": []
}
```

---

## Vue Stubs

### vue/page-index.stub

```vue
<script setup>
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermission } from '@/Composables/usePermission'
import { useConfirm } from '@/Composables/useConfirm'

const props = defineProps({
  $PLURAL_LOWER_NAME$: Object,
  filters: Object,
  can: Object,
  moduleId: Number,
})

const { canCreate, canUpdate, canDelete } = usePermission(props.moduleId)
const { confirmDelete } = useConfirm()

// Filters State
const search = ref(props.filters?.search || '')

// Table Columns - customize sesuai kebutuhan
const columns = [
  { key: 'name', label: 'Nama', sortable: true },
  { key: 'is_active', label: 'Status' },
  { key: 'created_at', label: 'Dibuat' },
]

// Apply Filters
const applyFilters = debounce(() => {
  router.get(route('$PLURAL_LOWER_NAME$.index'), {
    search: search.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch(search, applyFilters)

// Delete Handler
const handleDelete = async (item) => {
  const confirmed = await confirmDelete(`Hapus "$${item.name}"?`)
  if (confirmed) {
    router.delete(route('$PLURAL_LOWER_NAME$.destroy', item.id))
  }
}
</script>

<template>
  <Head title="$MODULE$" />

  <AppLayout>
    <!-- Page Header -->
    <c-page-header
      title="$MODULE$"
      :breadcrumbs="[
        { label: '$MODULE$', href: route('$PLURAL_LOWER_NAME$.index') },
        { label: 'Daftar $NAME$' },
      ]"
    />

    <!-- Compact Filters -->
    <c-table-filters
      v-model:search="search"
      search-placeholder="Cari..."
      :can-create="canCreate"
      create-route="$PLURAL_LOWER_NAME$.create"
      create-label="Tambah $NAME$"
    />

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Daftar $NAME$</h5>
      </div>
      <div class="card-body">
        <c-data-table
          :columns="columns"
          :data="$PLURAL_LOWER_NAME$.data"
          :pagination="$PLURAL_LOWER_NAME$.meta"
          :can-view="true"
          :can-edit="canUpdate"
          :can-delete="canDelete"
          view-route="$PLURAL_LOWER_NAME$.show"
          edit-route="$PLURAL_LOWER_NAME$.edit"
          @delete="handleDelete"
        >
          <!-- Custom Cell: Status -->
          <template #cell-is_active="{ item }">
            <span
              class="badge"
              :class="item.is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'"
            >
              {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </template>

          <!-- Custom Cell: Created At -->
          <template #cell-created_at="{ item }">
            <span class="text-muted">{{ item.created_at }}</span>
          </template>

          <!-- Empty State -->
          <template #empty>
            <div class="text-center py-4">
              <lord-icon
                src="https://cdn.lordicon.com/msoeawqm.json"
                trigger="loop"
                colors="primary:#405189,secondary:#0ab39c"
                style="width: 72px; height: 72px;"
              ></lord-icon>
              <h5 class="mt-4">Tidak ada data</h5>
              <p class="text-muted mb-0">Belum ada $LOWER_NAME$ yang terdaftar.</p>
            </div>
          </template>
        </c-data-table>
      </div>
    </div>

    <c-confirm-dialog />
  </AppLayout>
</template>
```

### vue/page-create.stub

```vue
<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/PageHeader.vue'

const props = defineProps({
  can: Object,
  moduleId: Number,
})

const form = ref({
  name: '',
  is_active: true,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
  processing.value = true
  errors.value = {}

  router.post(route('$PLURAL_LOWER_NAME$.store'), form.value, {
    onFinish: () => {
      processing.value = false
    },
    onError: (errs) => {
      errors.value = errs
    },
  })
}
</script>

<template>
  <Head title="Tambah $NAME$" />

  <AppLayout>
    <!-- Page Header -->
    <PageHeader
      title="Tambah $NAME$"
      :breadcrumbs="[
        { label: '$MODULE$', href: route('$PLURAL_LOWER_NAME$.index') },
        { label: 'Tambah $NAME$' },
      ]"
    />

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Form $NAME$</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="submit">
              <!-- Name -->
              <div class="mb-3">
                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="Masukkan nama"
                >
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
              </div>

              <!-- Is Active -->
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="form-check-input"
                  >
                  <label class="form-check-label" for="is_active">Aktif</label>
                </div>
              </div>

              <!-- Actions -->
              <div class="d-flex gap-2 justify-content-end">
                <Link :href="route('$PLURAL_LOWER_NAME$.index')" class="btn btn-light">
                  Batal
                </Link>
                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <span v-if="processing" class="spinner-border spinner-border-sm me-1"></span>
                  Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
```

### vue/page-edit.stub

```vue
<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/PageHeader.vue'

const props = defineProps({
  $LOWER_NAME$: Object,
  can: Object,
  moduleId: Number,
})

const form = ref({
  name: props.$LOWER_NAME$.name,
  is_active: props.$LOWER_NAME$.is_active,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
  processing.value = true
  errors.value = {}

  router.put(route('$PLURAL_LOWER_NAME$.update', props.$LOWER_NAME$.id), form.value, {
    onFinish: () => {
      processing.value = false
    },
    onError: (errs) => {
      errors.value = errs
    },
  })
}
</script>

<template>
  <Head :title="`Edit ${$LOWER_NAME$.name}`" />

  <AppLayout>
    <!-- Page Header -->
    <PageHeader
      title="Edit $NAME$"
      :breadcrumbs="[
        { label: '$MODULE$', href: route('$PLURAL_LOWER_NAME$.index') },
        { label: 'Edit $NAME$' },
      ]"
    />

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Form $NAME$</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="submit">
              <!-- Name -->
              <div class="mb-3">
                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="Masukkan nama"
                >
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
              </div>

              <!-- Is Active -->
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="form-check-input"
                  >
                  <label class="form-check-label" for="is_active">Aktif</label>
                </div>
              </div>

              <!-- Actions -->
              <div class="d-flex gap-2 justify-content-end">
                <Link :href="route('$PLURAL_LOWER_NAME$.index')" class="btn btn-light">
                  Batal
                </Link>
                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <span v-if="processing" class="spinner-border spinner-border-sm me-1"></span>
                  Update
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
```

### vue/page-show.stub

```vue
<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { usePermission } from '@/Composables/usePermission'

const props = defineProps({
  $LOWER_NAME$: Object,
  can: Object,
  moduleId: Number,
})

const { canUpdate } = usePermission(props.moduleId)
</script>

<template>
  <Head :title="$LOWER_NAME$.name" />

  <AppLayout>
    <!-- Page Header -->
    <PageHeader
      :title="$LOWER_NAME$.name"
      :breadcrumbs="[
        { label: '$MODULE$', href: route('$PLURAL_LOWER_NAME$.index') },
        { label: 'Detail $NAME$' },
      ]"
    />

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col">
                <h5 class="card-title mb-0">Detail $NAME$</h5>
              </div>
              <div class="col-auto">
                <Link v-if="canUpdate" :href="route('$PLURAL_LOWER_NAME$.edit', $LOWER_NAME$.id)" class="btn btn-primary">
                  <i class="ri-pencil-line align-bottom me-1"></i> Edit
                </Link>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                <tbody>
                  <tr>
                    <th class="ps-0" style="width: 200px;">ID</th>
                    <td class="text-muted">{{ $LOWER_NAME$.id }}</td>
                  </tr>
                  <tr>
                    <th class="ps-0">Nama</th>
                    <td class="text-muted">{{ $LOWER_NAME$.name }}</td>
                  </tr>
                  <tr>
                    <th class="ps-0">Status</th>
                    <td>
                      <span
                        class="badge"
                        :class="$LOWER_NAME$.is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'"
                      >
                        {{ $LOWER_NAME$.is_active ? 'Aktif' : 'Nonaktif' }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th class="ps-0">Dibuat</th>
                    <td class="text-muted">{{ $LOWER_NAME$.created_at }}</td>
                  </tr>
                  <tr>
                    <th class="ps-0">Diupdate</th>
                    <td class="text-muted">{{ $LOWER_NAME$.updated_at }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
```

---

## Global Components

Components yang **sering dipakai** sudah didaftarkan secara global di `resources/js/app.js` dengan prefix `c-`.
**Tidak perlu import** di setiap file - langsung pakai di template.

### UI Components (Global - Sering Dipakai)

| Global Name | Component | Description |
|-------------|-----------|-------------|
| `<c-link>` | Inertia Link | Link navigation |
| `<c-page-header>` | PageHeader | Breadcrumb dan page title |
| `<c-table-filters>` | TableFilters | Compact filter dengan search, dynamic filters, dan create button |
| `<c-data-table>` | DataTable | Table dengan pagination, sorting, actions |
| `<c-confirm-dialog>` | ConfirmDialog | Delete confirmation dialog |
| `<c-flash-message>` | FlashMessage | Flash message notification |

### Form Components (Global - Sering Dipakai)

| Global Name | Component | Description |
|-------------|-----------|-------------|
| `<c-input>` | Input | Text input field |
| `<c-select>` | Select | Dropdown select |
| `<c-checkbox>` | Checkbox | Checkbox input |
| `<c-button>` | Button | Button component |
| `<c-textarea>` | TextArea | Textarea input |

### Per-file Components (Jarang Dipakai - Import Manual)

Components ini **tidak di-register global** karena jarang dipakai. Import manual jika diperlukan:

```js
// Import jika butuh Modal
import Modal from '@/Components/Modal.vue'

// Pagination sudah include di DataTable, tidak perlu import terpisah
```

| Component | Kapan Dipakai | Import Path |
|-----------|---------------|-------------|
| Modal | Popup/dialog custom | `@/Components/Modal.vue` |
| Pagination | Sudah di dalam DataTable | - |

**Tidak perlu membuat component terpisah per module** (Table, Filters, Form).
Gunakan global components yang sudah ada dan customize menggunakan slots.

---

## Contoh Penggunaan TableFilters

```vue
<!-- Basic usage dengan search saja -->
<c-table-filters
  v-model:search="search"
  search-placeholder="Cari..."
  :can-create="canCreate"
  create-route="items.create"
  create-label="Tambah Item"
/>

<!-- Dengan dynamic filters (select, date) -->
<c-table-filters
  v-model:search="search"
  v-model="filterValues"
  search-placeholder="Cari nama atau email..."
  :filters="[
    {
      key: 'status',
      type: 'select',
      options: [
        { value: '', label: 'Semua Status' },
        { value: '1', label: 'Aktif' },
        { value: '0', label: 'Nonaktif' },
      ],
    },
    {
      key: 'date',
      type: 'date',
    },
  ]"
  :can-create="canCreate"
  create-route="items.create"
  create-label="Tambah Item"
/>
```

---

## Contoh Penggunaan DataTable dengan Slots

```vue
<c-data-table
  :columns="columns"
  :data="items.data"
  :pagination="items.meta"
  :can-view="true"
  :can-edit="canUpdate"
  :can-delete="canDelete"
  view-route="items.show"
  edit-route="items.edit"
  @delete="handleDelete"
>
  <!-- Custom Cell -->
  <template #cell-status="{ item }">
    <span class="badge bg-success">{{ item.status }}</span>
  </template>

  <!-- Custom Actions -->
  <template #actions="{ item }">
    <button class="btn btn-sm btn-soft-info" @click="customAction(item)">
      <i class="ri-download-line"></i>
    </button>
  </template>

  <!-- Empty State -->
  <template #empty>
    <div class="text-center py-4">
      <h5>No data found</h5>
    </div>
  </template>
</c-data-table>
```

### vue/composable.stub

```javascript
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export function use$NAME$() {
  const processing = ref(false)
  const errors = ref({})

  const create = (data) => {
    processing.value = true
    errors.value = {}

    router.post(route('$PLURAL_LOWER_NAME$.store'), data, {
      onFinish: () => {
        processing.value = false
      },
      onError: (errs) => {
        errors.value = errs
      },
    })
  }

  const update = (id, data) => {
    processing.value = true
    errors.value = {}

    router.put(route('$PLURAL_LOWER_NAME$.update', id), data, {
      onFinish: () => {
        processing.value = false
      },
      onError: (errs) => {
        errors.value = errs
      },
    })
  }

  const destroy = (id) => {
    if (!confirm('Yakin ingin menghapus?')) return

    processing.value = true

    router.delete(route('$PLURAL_LOWER_NAME$.destroy', id), {
      onFinish: () => {
        processing.value = false
      },
    })
  }

  return {
    processing,
    errors,
    create,
    update,
    destroy,
  }
}
```

---

## Test Stubs

### test/feature.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use $MODULE_NAMESPACE$\Models\$NAME$;

class $CLASS$ extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsUserWithPermissions([
            'read' => [$MODULE_ID$],
            'create' => [$MODULE_ID$],
            'update' => [$MODULE_ID$],
            'delete' => [$MODULE_ID$],
        ]);
    }

    public function test_can_view_index(): void
    {
        $response = $this->get(route('$PLURAL_LOWER_NAME$.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) =>
            $page->component('$MODULE$::views/index')
                 ->has('$PLURAL_LOWER_NAME$')
        );
    }

    public function test_can_create(): void
    {
        $data = [
            'name' => 'Test $NAME$',
            'is_active' => true,
        ];

        $response = $this->post(route('$PLURAL_LOWER_NAME$.store'), $data);

        $response->assertRedirect(route('$PLURAL_LOWER_NAME$.index'));
        $this->assertDatabaseHas('$TABLE$', ['name' => 'Test $NAME$']);
    }

    public function test_can_update(): void
    {
        $$LOWER_NAME$ = $NAME$::factory()->create();

        $response = $this->put(route('$PLURAL_LOWER_NAME$.update', $$LOWER_NAME$->id), [
            'name' => 'Updated Name',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('$PLURAL_LOWER_NAME$.index'));
        $this->assertDatabaseHas('$TABLE$', ['name' => 'Updated Name']);
    }

    public function test_can_delete(): void
    {
        $$LOWER_NAME$ = $NAME$::factory()->create();

        $response = $this->delete(route('$PLURAL_LOWER_NAME$.destroy', $$LOWER_NAME$->id));

        $response->assertRedirect(route('$PLURAL_LOWER_NAME$.index'));
        $this->assertDatabaseMissing('$TABLE$', ['id' => $$LOWER_NAME$->id]);
    }

    public function test_unauthorized_user_cannot_create(): void
    {
        $this->actingAsUserWithPermissions([
            'read' => [$MODULE_ID$],
            'create' => [], // No create permission
        ]);

        $response = $this->post(route('$PLURAL_LOWER_NAME$.store'), [
            'name' => 'Test',
        ]);

        $response->assertStatus(403);
    }
}
```

### test/unit.stub

```php
<?php

declare(strict_types=1);

namespace $NAMESPACE$;

use Tests\TestCase;
use Mockery;
use $MODULE_NAMESPACE$\Services\$NAME$Service;
use $MODULE_NAMESPACE$\Repositories\Contracts\$NAME$RepositoryInterface;

class $CLASS$ extends TestCase
{
    protected $NAME$Service $service;
    protected $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock($NAME$RepositoryInterface::class);
        $this->service = new $NAME$Service($this->mockRepository);
    }

    public function test_create_calls_repository(): void
    {
        $data = ['name' => 'Test'];

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn(new \$MODULE_NAMESPACE$\Models\$NAME$($data));

        $result = $this->service->create($data);

        $this->assertEquals('Test', $result->name);
    }

    public function test_find_returns_model(): void
    {
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(new \$MODULE_NAMESPACE$\Models\$NAME$(['id' => 1, 'name' => 'Test']));

        $result = $this->service->find(1);

        $this->assertEquals(1, $result->id);
    }

    public function test_find_throws_404_when_not_found(): void
    {
        $this->mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->service->find(999);
    }
}
```

---

## Stub Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| `$MODULE$` | Module name (PascalCase) | `UserManagement` |
| `$LOWER_NAME$` | Module name (lowercase) | `usermanagement` |
| `$NAME$` | Entity name (singular) | `User` |
| `$PLURAL_NAME$` | Entity name (plural) | `Users` |
| `$PLURAL_LOWER_NAME$` | Entity name (plural, lowercase) | `users` |
| `$MODULE_ID$` | Module ID for permissions | `1` |
| `$NAMESPACE$` | Full PHP namespace | `Modules\UserManagement\Http\Controllers` |
| `$MODULE_NAMESPACE$` | Module namespace | `Modules\UserManagement` |
| `$CLASS$` | Class name | `UserController` |
| `$TABLE$` | Database table name | `users` |
| `$ACTION$` | Permission action | `create`, `update`, `delete` |

---

## Usage

### Generate New Module

```bash
# 1. Create module structure
php artisan module:make Products

# 2. Module akan ter-generate dengan stubs yang sudah di-customize

# 3. Update module ID di config
# modules/Products/config/config.php
return [
    'module_id' => 4,  // Sesuaikan dengan ID di database
];

# 4. Add module ke database
INSERT INTO modules (id, name, slug, icon, route_name, `order`)
VALUES (4, 'Products', 'products', 'cube', 'products.index', 4);

# 5. Run migrations
php artisan module:migrate Products
```

### Generate Specific Files

```bash
# Controller
php artisan module:make-controller ProductController Products

# Model
php artisan module:make-model Product Products

# Migration
php artisan module:make-migration create_products_table Products

# Seeder
php artisan module:make-seed ProductSeeder Products

# Request
php artisan module:make-request StoreProductRequest Products
```

---

## Inertia Render Path Convention

Controllers use the format `{ModuleName}::views/{page}`:

```php
// In Controller
return Inertia::render("{$this->moduleName}::views/index", [...]);
return Inertia::render("{$this->moduleName}::views/create", [...]);
return Inertia::render("{$this->moduleName}::views/edit", [...]);
return Inertia::render("{$this->moduleName}::views/show", [...]);

// Resolves to:
// modules/{ModuleName}/resources/views/index.vue
// modules/{ModuleName}/resources/views/create.vue
// modules/{ModuleName}/resources/views/edit.vue
// modules/{ModuleName}/resources/views/show.vue
```

---

## Related Documentation

| File | Description |
|------|-------------|
| [ARCHITECTURE.md](./ARCHITECTURE.md) | Full architecture design, code patterns, security layers |
| [DATABASE.md](./DATABASE.md) | Database schema, migrations, seeders, ERD |
| [TASKS.md](./TASKS.md) | Implementation task list dengan 103+ tasks |
