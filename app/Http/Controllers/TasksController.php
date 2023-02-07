<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Traits\HttpResponses;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TasksResource;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{

    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return response()->json('This is my logout Method');

        return TasksResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority
        ]);

        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {

        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TasksResource($task);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {

        if(Auth::user()->id !== $task->user_id)
        {
            return $this->error('', 'You are not authorized to make this request', 403);
        }

        $task->update($request->all());

        return new TasksResource ($task);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {

        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();

    }

    private function isNotAuthorized($task){
        if(Auth::user()->id !== $task->user_id)
        {
            return $this->error('', 'You are not authorized to make this request', 403);
        }
    }

}
