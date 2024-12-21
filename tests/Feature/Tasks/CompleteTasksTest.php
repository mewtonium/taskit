<?php

use App\Models\Task;
use App\Models\User;

test('the user can mark an incomplete task as complete', function () {
    $user = User::factory()
        ->has(Task::factory()->notCompleted()->count(1))
        ->create();

    $this->actingAs($user)
        ->put(route('tasks.complete', $task = $user->tasks->first()))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->assertNotNull(Task::where('id', $task->id)->first()->completed_at);
});

test('the user can mark a completed task back to incomplete', function () {
    $user = User::factory()
        ->has(Task::factory()->count(1))
        ->create();

    $this->actingAs($user)
        ->put(route('tasks.complete', $task = $user->tasks->first()))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->assertNull(Task::where('id', $task->id)->first()->completed_at);
});

test('the user can\'t complete another user\'s tasks', function () {
    $user1 = User::factory()
        ->has(Task::factory()->notCompleted()->count(1))
        ->create();

    $user2 = User::factory()
        ->has(Task::factory()->notCompleted()->count(1))
        ->create();

    $response = $this->actingAs($user1)->put(route('tasks.complete', $task = $user2->tasks->first()));

    $response->assertStatus(403);

    $this->assertNull(Task::where('id', $task->id)->first()->completed_at);
});
