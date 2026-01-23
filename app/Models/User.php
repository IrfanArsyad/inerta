<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if user has permission for action on module
     */
    public function hasPermission(string $action, int $moduleId): bool
    {
        $permissions = session('auth.permissions', $this->getPermissions());
        $allowed = $permissions[$action] ?? [];

        if (in_array('*', $allowed, true)) {
            return true;
        }

        return in_array($moduleId, $allowed, true);
    }

    /**
     * Get permissions from role
     */
    public function getPermissions(): array
    {
        if (!$this->role) {
            return [
                'read' => [],
                'create' => [],
                'update' => [],
                'delete' => [],
            ];
        }

        return [
            'read' => $this->role->read ?? [],
            'create' => $this->role->create ?? [],
            'update' => $this->role->update ?? [],
            'delete' => $this->role->delete ?? [],
        ];
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
     * Get accessible modules for sidebar
     */
    public function getAccessibleModules(): array
    {
        $permissions = session('auth.permissions', $this->getPermissions());
        $readableIds = $permissions['read'] ?? [];

        $query = Module::where('active', true)->orderBy('order');

        if (!in_array('*', $readableIds, true)) {
            if (empty($readableIds)) {
                return [];
            }
            $query->whereIn('id', $readableIds);
        }

        return $query->get()->toArray();
    }
}
