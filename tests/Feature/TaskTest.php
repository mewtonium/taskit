<?php

use App\Enums\Priority;
use App\Models\Task;
use App\Models\User;

test('the user can see their own tasks', function () {
    $user = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $response = $this->actingAs($user)->get(route('tasks.index'));

    $response->assertOk();

    foreach ($user->tasks as $task) {
        $response->assertSee($task->title);
    }
});

test('the user won\'t see another user\'s tasks', function () {
    $user1 = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $user2 = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $response = $this->actingAs($user1)->get(route('tasks.index'));

    foreach ($user2->tasks as $task) {
        $response->assertDontSee($task->title);
    }
});

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

test('the user can delete their own tasks', function () {
    $user = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $response = $this->actingAs($user)->delete(route('tasks.destroy', $task = $user->tasks->first()));

    $response->assertSessionHasNoErrors();

    $this->assertNotEquals(403, $response->getStatusCode());
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('the user can\'t delete another user\'s tasks', function () {
    $user1 = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $user2 = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $response = $this->actingAs($user1)->delete(route('tasks.destroy', $task = $user2->tasks->first()));

    $response->assertStatus(403);

    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});
