<?php

declare(strict_types=1);

namespace Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\RolePermission\Http\Requests\StoreRoleRequest;
use Modules\RolePermission\Http\Requests\UpdateRoleRequest;
use Modules\RolePermission\Http\Resources\RoleResource;
use Modules\RolePermission\Services\RoleService;

class RoleController extends Controller
{
    protected int $moduleId = 2;
    protected string $moduleName = 'RolePermission';

    public function __construct(
        protected RoleService $roleService
    ) {}

    public function index(): Response
    {
        $roles = $this->roleService->paginate(
            request()->only(['search']),
            request()->integer('per_page', 15)
        );

        return Inertia::render("{$this->moduleName}::views/index", [
            'roles' => RoleResource::collection($roles),
            'modules' => $this->roleService->getAllModules(),
            'filters' => request()->only(['search']),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->moduleName}::views/create", [
            'modules' => $this->roleService->getAllModules(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->create($request->validated());

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(string $id): Response
    {
        $role = $this->roleService->find((int) $id);

        return Inertia::render("{$this->moduleName}::views/show", [
            'role' => new RoleResource($role),
            'modules' => $this->roleService->getAllModules(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function edit(string $id): Response
    {
        $role = $this->roleService->find((int) $id);

        return Inertia::render("{$this->moduleName}::views/edit", [
            'role' => new RoleResource($role),
            'modules' => $this->roleService->getAllModules(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function update(UpdateRoleRequest $request, string $id): RedirectResponse
    {
        $this->roleService->update((int) $id, $request->validated());

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->roleService->delete((int) $id);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
