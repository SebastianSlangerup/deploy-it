<?php

namespace Database\Seeders;

use App\Models\Dependency;
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
            'password' => Hash::make('password'),
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
    }
}
