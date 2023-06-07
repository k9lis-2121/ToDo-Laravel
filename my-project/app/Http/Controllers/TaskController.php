<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::all();
        if($request->input('list') == 'update'){
            return response()->json(json_encode(compact('tasks')));
        }
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $task = new Task();
        $task->user_id = $request->input('user_id');
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        if($request->input('is_public') !== 'on') {
            $task->is_public = $request->input('is_public');
        }else{
            $task->is_public = 0;
        }
        $task->image_url = $request->input('image_url');
        $task->save();
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->is_public = $request->input('is_public');
        $task->image_url = $request->input('image_url');
        $task->save();
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        $task->delete();
        return response()->json(['success' => true]);
    }
}
