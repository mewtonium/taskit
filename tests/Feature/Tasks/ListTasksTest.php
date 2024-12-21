<?php

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
