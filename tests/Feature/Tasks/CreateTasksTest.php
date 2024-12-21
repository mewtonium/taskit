<?php

use App\Enums\Priority;
use App\Models\User;

test('a task can be created', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'title' => 'Test Task',
        'notes' => 'Test Task Notes',
        'priority' => Priority::HIGH->value,
        'start_at' => now()->format('Y-m-d'),
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertSee('Test Task');
});

test('a task can be created without optional fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'title' => 'Test Task',
        'priority' => Priority::HIGH->value,
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertSee('Test Task');
});

test('a task wasn\'t created because validation fails', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'title' => 'Test Task',
    ]);

    $response->assertSessionHasErrors('priority');

    $this->assertDatabaseCount('tasks', 0);
});
