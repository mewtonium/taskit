<?php

use App\Models\Star;
use App\Models\Task;
use App\Models\User;

test('the user can star a task', function () {
    $user = User::factory()
        ->has(Task::factory()->notCompleted()->count(1))
        ->create();

    $this->actingAs($user)
        ->put(route('tasks.star', $task = $user->tasks->first()))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->assertNotNull(Task::where('id', $task->id)->first()->starred);
    $this->assertDatabaseCount('stars', 1);
});

test('the user can unstar a task', function () {
    $user = User::factory()
        ->has(Task::factory()->count(1))
        ->create();

    Star::factory()->forTask($task = $user->tasks->first())->create();

    $this->actingAs($user)
        ->put(route('tasks.star', $task))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('tasks.index'));

    $this->assertNull(Task::where('id', $task->id)->first()->starred);
    $this->assertDatabaseCount('stars', 0);
});

test('the user can\'t star another user\'s tasks', function () {
    $user1 = User::factory()
        ->has(Task::factory()->notCompleted()->count(1))
        ->create();

    $user2 = User::factory()
        ->has(Task::factory()->notCompleted()->count(1))
        ->create();

    $response = $this->actingAs($user1)->put(route('tasks.star', $task = $user2->tasks->first()));

    $response->assertStatus(403);

    $this->assertNull(Task::where('id', $task->id)->first()->starred);
});
