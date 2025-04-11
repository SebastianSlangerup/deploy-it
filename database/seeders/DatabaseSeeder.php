<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\RolesEnum;
use App\Models\Container;
use App\Models\Instance;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call([
        //     UserSeeder::class,
        // ]);

        $user = User::factory()->create([
            'name' => 'Admin',
            'role' => RolesEnum::Admin,
            'email' => 'admin@example.org',
        ]);

        Server::factory()->count(5)->create()->each(function (Server $server) use ($user) {
            Instance::factory()->create([
                'created_by' => $user->id,
                'instanceable_id' => $server->id,
                'instanceable_type' => Server::class,
            ]);
        });

        Container::factory()->count(5)->create()->each(function (Container $container) use ($user) {
            Instance::factory()->create([
                'created_by' => $user->id,
                'instanceable_id' => $container->id,
                'instanceable_type' => Container::class,
            ]);
        });
    }
}
