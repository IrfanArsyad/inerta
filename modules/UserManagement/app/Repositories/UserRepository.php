<?php

declare(strict_types=1);

namespace Modules\UserManagement\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->with('role');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): ?User
    {
        return $this->model->with('role')->find($id);
    }

    public function create(array $data): User
    {
        $user = $this->model->create($data);

        return $user->load('role');
    }

    public function update(int $id, array $data): User
    {
        $user = $this->find($id);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh('role');
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function getAllRoles(): array
    {
        return Role::active()->get(['id', 'name', 'display_name'])->toArray();
    }
}
