<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(rand(min: 2, max: 5)),
            'notes' => fake()->paragraph(1),
            'priority' => fake()->randomElement(Priority::class),
            'start_at' => now()->addDays(rand(0, 7))->format('Y-m-d'),
            'completed_at' => now()->addDays(7)->addDays(rand(0, 3)),
        ];
    }

    /**
     * [Untitled method]
     */
    public function withoutNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => null,
        ]);
    }

    /**
     * [Untitled method]
     */
    public function withoutStartDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => null,
        ]);
    }

    /**
     * [Untitled method]
     */
    public function notCompleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => null,
        ]);
    }
}
