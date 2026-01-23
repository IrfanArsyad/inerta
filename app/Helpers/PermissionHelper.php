<?php

declare(strict_types=1);

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Check if current user can perform action on module
     */
    public static function can(string $action, int $moduleId): bool
    {
        $permissions = session('auth.permissions', []);
        $allowed = $permissions[$action] ?? [];

        if (in_array('*', $allowed, true)) {
            return true;
        }

        return in_array($moduleId, $allowed, true);
    }

    public static function canRead(int $moduleId): bool
    {
        return self::can('read', $moduleId);
    }

    public static function canCreate(int $moduleId): bool
    {
        return self::can('create', $moduleId);
    }

    public static function canUpdate(int $moduleId): bool
    {
        return self::can('update', $moduleId);
    }

    public static function canDelete(int $moduleId): bool
    {
        return self::can('delete', $moduleId);
    }

    /**
     * Get permission object for Vue
     */
    public static function getModulePermissions(int $moduleId): array
    {
        return [
            'read' => self::canRead($moduleId),
            'create' => self::canCreate($moduleId),
            'update' => self::canUpdate($moduleId),
            'delete' => self::canDelete($moduleId),
        ];
    }

    /**
     * Get current user permissions
     */
    public static function all(): array
    {
        return session('auth.permissions', []);
    }

    /**
     * Get accessible modules for sidebar
     */
    public static function getAccessibleModules(): array
    {
        return session('auth.modules', []);
    }
}
