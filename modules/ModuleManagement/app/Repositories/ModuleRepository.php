<?php

declare(strict_types=1);

namespace Modules\ModuleManagement\Repositories;

use App\Models\Module;
use App\Models\ModuleGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\ModuleManagement\Repositories\Contracts\ModuleRepositoryInterface;

class ModuleRepository implements ModuleRepositoryInterface
{
    public function __construct(
        protected Module $model
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['parent', 'group']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            });
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['group_id'])) {
            $query->where('module_group_id', $filters['group_id']);
        }

        return $query->orderBy('order')->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->model->with(['parent', 'group'])->orderBy('order')->get();
    }

    public function find(int $id): ?Module
    {
        return $this->model->with(['parent', 'group', 'children'])->find($id);
    }

    public function create(array $data): Module
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Module
    {
        $module = $this->model->findOrFail($id);
        $module->update($data);

        return $module->fresh(['parent', 'group']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getModuleGroups(): Collection
    {
        return ModuleGroup::active()->ordered()->get();
    }
}
