<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(Task::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks'
        ]);

        $task = Task::create([
            'title' => $request->title,
        ]);

        return response()->json($task);
    }

    public function update($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['is_completed' => !$task->is_completed]);
        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
