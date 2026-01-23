<?php

declare(strict_types=1);

namespace Modules\ModuleManagement\Services;

use App\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\ModuleManagement\Repositories\Contracts\ModuleRepositoryInterface;

class ModuleService
{
    public function __construct(
        protected ModuleRepositoryInterface $repository
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Module
    {
        $module = $this->repository->find($id);

        if (!$module) {
            abort(404, 'Module not found.');
        }

        return $module;
    }

    public function create(array $data): Module
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Module
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getModuleGroups(): Collection
    {
        return $this->repository->getModuleGroups();
    }

    public function getParentModules(): Collection
    {
        return Module::whereNull('parent_id')->active()->ordered()->get(['id', 'name', 'label']);
    }
}
