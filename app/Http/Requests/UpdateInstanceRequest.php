<?php

namespace App\Http\Requests;

use App\Enums\InstanceStatusEnum;
use App\Models\Instance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:5000'],
            'status' => ['sometimes', Rule::enum(InstanceStatusEnum::class)],
        ];
    }

    // Make sure the user is the owner of the instance. Will return 403 if not.
    public function authorize(): bool
    {
        $instance = Instance::find($this->route('instance'));

        return $instance->created_by === $this->user()->id;
    }
}
