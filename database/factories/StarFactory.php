<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Star>
 */
class StarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'starrable_id' => null,
            'starrable_type' => null,
        ];
    }

    /**
     * Sets a task a starred.
     */
    public function forTask(?Task $task = null)
    {
        return $this->state(fn () => [
            'starrable_id' => $task?->id ?? Task::factory(),
            'starrable_type' => Task::class,
        ]);
    }
}
