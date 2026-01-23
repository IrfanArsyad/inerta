<?php

declare(strict_types=1);

namespace Modules\ModuleManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return can_create(4);
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:modules,id'],
            'module_group_id' => ['nullable', 'integer', 'exists:module_groups,id'],
            'permission' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255', 'unique:modules,name'],
            'alias' => ['nullable', 'string', 'max:255'],
            'label' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'badge_source' => ['nullable', 'string', 'max:255'],
            'active' => ['boolean'],
            'external' => ['boolean'],
            'order' => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama module wajib diisi.',
            'name.unique' => 'Nama module sudah digunakan.',
            'label.required' => 'Label module wajib diisi.',
        ];
    }
}
