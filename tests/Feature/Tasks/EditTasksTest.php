<?php

use App\Enums\Priority;
use App\Models\Task;
use App\Models\User;

test('a task can be edited', function () {
    $user = User::factory()
        ->has(Task::factory()->count(1)->state(fn () => ['title' => 'Test Task']))
        ->create();

    $task = $user->tasks->first();

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertSee('Test Task');

    $response = $this->actingAs($user)->put(route('tasks.update', $task), [
        'title' => 'Updated Test Task',
        'priority' => Priority::HIGH->value,
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->actingAs($user)
        ->get(route('tasks.index'))
        ->assertSee('Updated Test Task');
});

test('the user can\'t edit another user\'s task', function () {
    $user1 = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $user2 = User::factory()
        ->has(Task::factory()->count(3))
        ->create();

    $response = $this->actingAs($user1)
        ->put(route('tasks.update', $task = $user2->tasks->first()), [
            'title' => 'Another test task',
            'priority' => Priority::HIGH->value,
        ]);

    $response->assertStatus(403);

    expect(Task::where('id', $task->id)->first()->title)->not->toBe('Another test task');
});

test('a task wasn\'t updated because validation fails', function () {
    $user = User::factory()
        ->has(Task::factory()->count(1))
        ->create();

    $task = $user->tasks->first();

    $response = $this->actingAs($user)
        ->put(route('tasks.update', $task), [
            'title' => 'Updated Test Task',
        ]);

    $response->assertSessionHasErrors('priority');

    expect(Task::where('id', $task->id)->first()->title)->not->toBe('Updated Test Task');
});
