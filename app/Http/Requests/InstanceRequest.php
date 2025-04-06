<?php

namespace App\Http\Requests;

use App\Enums\InstanceTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'instance_type' => Rule::enum(InstanceTypeEnum::class),
            'selected_configuration' => [
                'required_if:instance_type,'.InstanceTypeEnum::Server->value,
            ],
            'docker_image' => [
                'required_if:instance_type,'.InstanceTypeEnum::Container->value,
                'url',
            ],
        ];
    }
}
