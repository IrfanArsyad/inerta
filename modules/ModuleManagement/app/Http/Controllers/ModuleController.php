<?php

declare(strict_types=1);

namespace Modules\ModuleManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\ModuleManagement\Http\Requests\StoreModuleRequest;
use Modules\ModuleManagement\Http\Requests\UpdateModuleRequest;
use Modules\ModuleManagement\Http\Resources\ModuleResource;
use Modules\ModuleManagement\Services\ModuleService;

class ModuleController extends Controller
{
    protected int $moduleId = 3;
    protected string $moduleName = 'ModuleManagement';

    public function __construct(
        protected ModuleService $moduleService
    ) {}

    public function index(): Response
    {
        $modules = $this->moduleService->paginate(
            request()->only(['search', 'active', 'group_id']),
            request()->integer('per_page', 15)
        );

        return Inertia::render("{$this->moduleName}::views/index", [
            'modules' => ModuleResource::collection($modules),
            'filters' => request()->only(['search', 'active', 'group_id']),
            'groups' => $this->moduleService->getModuleGroups(),
            'parents' => $this->moduleService->getParentModules(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("{$this->moduleName}::views/create", [
            'groups' => $this->moduleService->getModuleGroups(),
            'parents' => $this->moduleService->getParentModules(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function store(StoreModuleRequest $request): RedirectResponse
    {
        $this->moduleService->create($request->validated());

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module created successfully.');
    }

    public function show(int $id): Response
    {
        $module = $this->moduleService->find($id);

        return Inertia::render("{$this->moduleName}::views/show", [
            'module' => new ModuleResource($module),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function edit(int $id): Response
    {
        $module = $this->moduleService->find($id);

        return Inertia::render("{$this->moduleName}::views/edit", [
            'module' => new ModuleResource($module),
            'groups' => $this->moduleService->getModuleGroups(),
            'parents' => $this->moduleService->getParentModules(),
            'can' => module_permissions($this->moduleId),
            'moduleId' => $this->moduleId,
        ]);
    }

    public function update(UpdateModuleRequest $request, int $id): RedirectResponse
    {
        $this->moduleService->update($id, $request->validated());

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->moduleService->delete($id);

        return redirect()
            ->route('modules.index')
            ->with('success', 'Module deleted successfully.');
    }
}
