<?php

declare(strict_types=1);

namespace Modules\ModuleManagement\Repositories\Contracts;

use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ModuleRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function all(): Collection;

    public function find(int $id): ?Module;

    public function create(array $data): Module;

    public function update(int $id, array $data): Module;

    public function delete(int $id): bool;

    public function getModuleGroups(): Collection;
}
