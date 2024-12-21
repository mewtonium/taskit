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
