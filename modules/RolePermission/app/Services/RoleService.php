<?php

declare(strict_types=1);

namespace Modules\RolePermission\Services;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Modules\RolePermission\Repositories\Contracts\RoleRepositoryInterface;

class RoleService
{
    public function __construct(
        protected RoleRepositoryInterface $repository
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Role
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Role
    {
        $permissions = $this->buildPermissions($data['permissions'] ?? []);
        unset($data['permissions']);

        $data = array_merge($data, $permissions);

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Role
    {
        $permissions = $this->buildPermissions($data['permissions'] ?? []);
        unset($data['permissions']);

        $data = array_merge($data, $permissions);

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getAllModules(): Collection
    {
        return Module::where('active', true)->orderBy('name')->get();
    }

    protected function buildPermissions(array $permissions): array
    {
        $result = [
            'read' => [],
            'create' => [],
            'update' => [],
            'delete' => [],
        ];

        foreach ($permissions as $moduleId => $actions) {
            $moduleId = (int) $moduleId;

            foreach ($actions as $action) {
                if (isset($result[$action]) && !in_array($moduleId, $result[$action])) {
                    $result[$action][] = $moduleId;
                }
            }
        }

        return $result;
    }
}
