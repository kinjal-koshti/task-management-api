<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['category', 'user'])
            ->where('user_id', auth()->id());
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
            $q->where('title', 'LIKE', "%{$request->search}%")
              ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        }

        // Sorting
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $order = $request->order ?? 'desc';
        $query->orderBy($sortBy, $order);

        // Pagination
        $limit = $request->limit ?? 10;
        $tasks = $query->paginate($limit);

       $tasks->getCollection()->transform(function ($task) {
        return [
            'id'            => $task->id,
            'title'         => $task->title,
            'description'   => $task->description,
            'status'        => $task->status,
            'category_id'   => $task->category_id,
            'category_name' => $task->category ? $task->category->name : null,
            'user_name'     => $task->user ? $task->user->name : null,
            'created_at'    => $task->created_at->format('d-m-Y H:i'),
            'updated_at'    => $task->updated_at->format('d-m-Y H:i'),
            'attachment'    => $task->attachment ? asset('storage/' . $task->attachment) : null,
        ];
    });

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:todo,in_progress,done',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $filePath = null;

        // Upload file
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('tasks', 'public');
        }

        $task = Task::create([
            'user_id'     => auth()->id(),
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status ?? 'todo',
            'attachment'  => $filePath
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    public function show($id)
    {
        $task = Task::where('user_id', auth()->id())->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', auth()->id())->find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id,deleted_at,NULL',
            'title'       => 'string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:todo,in_progress,done',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        // Replace file
        if ($request->hasFile('attachment')) {
            if ($task->attachment) {
                Storage::disk('public')->delete($task->attachment);
            }
            $task->attachment = $request->file('attachment')->store('tasks', 'public');
        }

        $task->fill([
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status,
        ]);
        
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', auth()->id())->find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        if ($task->attachment) {
            Storage::disk('public')->delete($task->attachment);
        }

        $task->is_active = 0;
        $task->save();
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }
}
