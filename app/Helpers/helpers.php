<?php

declare(strict_types=1);

use App\Helpers\PermissionHelper;

if (!function_exists('can_access')) {
    function can_access(string $action, int $moduleId): bool
    {
        return PermissionHelper::can($action, $moduleId);
    }
}

if (!function_exists('can_read')) {
    function can_read(int $moduleId): bool
    {
        return PermissionHelper::canRead($moduleId);
    }
}

if (!function_exists('can_create')) {
    function can_create(int $moduleId): bool
    {
        return PermissionHelper::canCreate($moduleId);
    }
}

if (!function_exists('can_update')) {
    function can_update(int $moduleId): bool
    {
        return PermissionHelper::canUpdate($moduleId);
    }
}

if (!function_exists('can_delete')) {
    function can_delete(int $moduleId): bool
    {
        return PermissionHelper::canDelete($moduleId);
    }
}

if (!function_exists('module_permissions')) {
    function module_permissions(int $moduleId): array
    {
        return PermissionHelper::getModulePermissions($moduleId);
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        return session('auth.user');
    }
}

if (!function_exists('sidebar_modules')) {
    function sidebar_modules(): array
    {
        return PermissionHelper::getAccessibleModules();
    }
}
