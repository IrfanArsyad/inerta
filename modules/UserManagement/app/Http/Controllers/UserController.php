<?php

declare(strict_types=1);

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\UserManagement\Http\Requests\StoreUserRequest;
use Modules\UserManagement\Http\Requests\UpdateUserRequest;
use Modules\UserManagement\Http\Resources\UserResource;
use Modules\UserManagement\Services\UserService;

class UserController extends Controller
{
    protected int $moduleId = 1;
    protected string $moduleName = 'UserManagement';

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): Response
    {
        $users = $this->userService->paginate(
            request()->only(['search', 'is_active']),
            request()->integer('per_page', 15)
        );

        return Inertia::render("{$this->moduleName}::views/index", [
            'users' => UserResource::collection($users),
            'roles' => $this->userService->getAllRoles(),
            'filters' => request()->only(['search', 'is_active']),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->moduleName}::views/create", [
            'roles' => $this->userService->getAllRoles(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(int $id): Response
    {
        $user = $this->userService->find($id);

        return Inertia::render("{$this->moduleName}::views/show", [
            'user' => new UserResource($user),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function edit(int $id): Response
    {
        $user = $this->userService->find($id);

        return Inertia::render("{$this->moduleName}::views/edit", [
            'user' => new UserResource($user),
            'roles' => $this->userService->getAllRoles(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->update($id, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->userService->delete($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
