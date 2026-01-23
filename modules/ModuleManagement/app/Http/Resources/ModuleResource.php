<?php

declare(strict_types=1);

namespace Modules\ModuleManagement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'module_group_id' => $this->module_group_id,
            'permission' => $this->permission,
            'name' => $this->name,
            'alias' => $this->alias,
            'label' => $this->label,
            'icon' => $this->icon,
            'url' => $this->url,
            'route_name' => $this->route_name,
            'badge_source' => $this->badge_source,
            'active' => $this->active,
            'external' => $this->external,
            'order' => $this->order,
            'parent' => $this->whenLoaded('parent', fn () => [
                'id' => $this->parent->id,
                'name' => $this->parent->name,
                'label' => $this->parent->label,
            ]),
            'group' => $this->whenLoaded('group', fn () => [
                'id' => $this->group->id,
                'name' => $this->group->name,
                'label' => $this->group->label,
            ]),
            'children' => $this->whenLoaded('children', fn () => $this->children->map(fn ($child) => [
                'id' => $child->id,
                'name' => $child->name,
                'label' => $child->label,
                'active' => $child->active,
            ])),
            'created_at' => $this->created_at?->format('d M Y H:i'),
            'updated_at' => $this->updated_at?->format('d M Y H:i'),
        ];
    }
}
