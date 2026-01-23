<?php

declare(strict_types=1);

namespace Modules\RolePermission\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'active' => $this->active,
            'permissions' => [
                'read' => $this->read ?? [],
                'create' => $this->create ?? [],
                'update' => $this->update ?? [],
                'delete' => $this->delete ?? [],
            ],
            'users_count' => $this->whenCounted('users'),
            'users' => $this->whenLoaded('users', fn () => $this->users->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])),
            'created_at' => $this->created_at?->format('d M Y H:i'),
            'updated_at' => $this->updated_at?->format('d M Y H:i'),
        ];
    }
}
