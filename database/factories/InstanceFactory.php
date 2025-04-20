<?php

namespace Database\Factories;

use App\Models\Instance;
use App\Models\User;
use App\Models\Configuration;
use App\States\InstanceStatusState\Started;
use App\States\InstanceStatusState\Stopped;
use App\States\InstanceStatusState\Suspended;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class InstanceFactory extends Factory
{
    protected $model = Instance::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement([Started::class, Stopped::class, Suspended::class]),
            'is_ready' => 1,
            'created_by' => User::factory(),
            'started_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
