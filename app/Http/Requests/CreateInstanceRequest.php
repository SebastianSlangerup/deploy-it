<?php

namespace App\Http\Requests;

use App\Enums\InstanceTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'hostname' => ['required', 'string', 'alpha_dash:ascii', 'unique:App\Models\Instance,hostname', 'lowercase'],
            'node' => ['required', 'string', 'max:255'],
            'instance_type' => Rule::enum(InstanceTypeEnum::class),
            'selected_configuration' => [
                'required_if:instance_type,'.InstanceTypeEnum::Server->value,
            ],
            'docker_image' => [
                'required_if:instance_type,'.InstanceTypeEnum::Container->value,
                'string',
            ],
            'selected_packages' => [
                'required_if:instance_type,'.InstanceTypeEnum::Server->value,
                'array',
            ],
            'server_id' => [
                'required_if:instance_type,'.InstanceTypeEnum::Container->value,
                'string',
            ],
        ];
    }

    public function messages()
    {
        return [
            'selected_configuration.required_if' => 'A configuration must be selected for server instances.',
            'docker_image.required_if' => 'A Docker image URL is required for container instances.',
        ];
    }
}
