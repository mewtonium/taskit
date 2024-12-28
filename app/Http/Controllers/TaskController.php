<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $request->user()
            ->tasks()
            ->orderBy('priority')
            ->orderBy('start_at', 'desc')
            ->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskStoreRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index');
    }

    /**
     * Mark the task as completed.
     */
    public function complete(Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'completed_at' => $task->completed_at === null ? now() : null,
        ]);

        return redirect()->route('tasks.index');
    }
}
