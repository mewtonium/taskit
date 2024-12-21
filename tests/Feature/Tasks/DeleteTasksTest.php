<?php

use App\Models\Task;
use App\Models\User;

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
