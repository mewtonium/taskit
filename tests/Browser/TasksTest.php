<?php

namespace Tests\Browser\Tasks;

use App\Enums\Priority;
use App\Models\Task;
use App\Models\User;
use Laravel\Dusk\Browser;

test('a user can create a new task', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser
            ->loginAs($user)
            ->visitRoute('tasks.index')
            ->press('@create-task')
            ->type('@title', 'Test Task')
            ->type('@notes', 'Lorem ipsum dolor sit amet')
            ->select('@priority', Priority::HIGH->value)
            ->type('@start-at', now()->format('d/m/Y'))
            ->press('@save-task')
            ->waitForRoute('tasks.index')
            ->assertSee('Test Task');
    });
});

test('a user can edit an existing task', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()
            ->has(Task::factory()->count(3))
            ->create();

        $browser
            ->loginAs($user)
            ->visitRoute('tasks.index')
            ->press('@edit-task--1')
            ->type('@title', 'Test Task Updated')
            ->press('@save-task')
            ->waitForRoute('tasks.index')
            ->assertSee('Test Task Updated');
    });
});

test('a user can delete a task', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()
            ->has(Task::factory()->count(3))
            ->create();

        $task = $user->tasks->first();

        $browser
            ->loginAs($user)
            ->visitRoute('tasks.index')
            ->press("@delete-task--{$task->id}")
            ->press('@confirm-delete-task')
            ->waitForRoute('tasks.index')
            ->assertDontSee($task->title);
    });
});

test('a user can mark and see a task as complete', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()
            ->has(Task::factory()->count(3))
            ->create();

        $task = $user->tasks->first();

        $browser
            ->loginAs($user)
            ->visitRoute('tasks.index')
            ->check("@complete-task--{$task->id}")
            ->waitForRoute('tasks.index')
            ->assertChecked("@complete-task--{$task->id}")
            ->assertScript("document.querySelector('.task:nth-child(1) .task__title').classList.contains('line-through')");
    });
});

test('a user can mark and see a task as starred', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()
            ->has(Task::factory()->count(3))
            ->create();

        $task = $user->tasks->first();

        $browser
            ->loginAs($user)
            ->visitRoute('tasks.index')
            ->press("@star-task--{$task->id}")
            ->waitForRoute('tasks.index')
            ->assertScript("document.querySelector('.task:nth-child(1) .task__starred').closest('.task-list').querySelector('h2').innerHTML === 'Starred Tasks'");
    });
});

test('a user can open and see task notes', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()
            ->has(Task::factory()->count(1))
            ->create();

        $task = $user->tasks->first();

        $browser
            ->loginAs($user)
            ->visitRoute('tasks.index')
            ->press("@task-notes--{$task->id}")
            ->assertSee($task->notes);
    });
});
