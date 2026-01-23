<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'active',
        'read',
        'create',
        'update',
        'delete',
    ];

    protected $casts = [
        'active' => 'boolean',
        'read' => 'array',
        'create' => 'array',
        'update' => 'array',
        'delete' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role has permission for specific action on module
     */
    public function hasPermission(string $action, int $moduleId): bool
    {
        $permissions = $this->{$action} ?? [];

        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array($moduleId, $permissions, true);
    }

    public function canRead(int $moduleId): bool
    {
        return $this->hasPermission('read', $moduleId);
    }

    public function canCreate(int $moduleId): bool
    {
        return $this->hasPermission('create', $moduleId);
    }

    public function canUpdate(int $moduleId): bool
    {
        return $this->hasPermission('update', $moduleId);
    }

    public function canDelete(int $moduleId): bool
    {
        return $this->hasPermission('delete', $moduleId);
    }

    /**
     * Get all readable module IDs
     */
    public function getReadableModuleIds(): array
    {
        $read = $this->read ?? [];

        if (in_array('*', $read, true)) {
            return Module::where('active', true)->pluck('id')->toArray();
        }

        return $read;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
