<?php

namespace Database\Seeders;

use App\Models\Dependency;
use App\Models\Node;
use App\Models\Template;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Sebastian Møller',
            'email' => 'seba7271@edu.sde.dk',
            'password' => Hash::make('Admin1234'),
            'is_active' => 1,
        ]);
        User::factory()->create([
            'name' => 'Martin Egeskov',
            'email' => 'mart337i@gmail.com',
            'password' => Hash::make('Admin1234'),
            'is_active' => 1,
        ]);

        $templates = Template::factory()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'Web Development'],
                ['name' => 'Scripting'],
                ['name' => 'Crypto'],
            ))
            ->create();

        $dependencies = Dependency::factory()
            ->count(5)
            ->state(new Sequence(
                ['name' => 'nodejs', 'command' => 'apt install npm'],
                ['name' => 'python', 'command' => 'apt install python'],
                ['name' => 'php', 'command' => 'apt install php'],
                ['name' => 'mysql', 'command' => 'apt install mysql'],
                ['name' => 'postgresql', 'command' => 'apt install postgresql'],
            ))
            ->create();

        foreach ($dependencies as $dependency) {
            $dependency->templates()->attach($templates[rand(0, 2)]);
        }

        Node::factory()
            ->count(4)
            ->state(new Sequence(
                ['display_name' => 'Development Node', 'hostname' => 'pve', 'ip' => '192.168.1.10'],
                ['display_name' => 'Testing Node', 'hostname' => 'node1', 'ip' => '192.168.1.50'],
                ['display_name' => 'Staging Node', 'hostname' => 'node2', 'ip' => '192.168.1.51'],
                ['display_name' => 'Production Node', 'hostname' => 'node3', 'ip' => '192.168.1.52'],
            ))
            ->create();
    }
}
