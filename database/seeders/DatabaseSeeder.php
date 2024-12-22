<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tasks = Task::factory()
            ->sequence(fn () => [
                ...((rand(1, 10) <= 2) ? ['notes' => null] : []),
                ...((rand(1, 10) <= 5) ? ['start_at' => null] : []),
                ...((rand(1, 10) <= 4) ? ['completed_at' => null] : []),
            ])
            ->count(10);

        User::factory()
            ->has($tasks)
            ->create([
                'name' => 'Mark Wilkinson',
                'email' => 'mark@example.com',
            ]);
    }
}
