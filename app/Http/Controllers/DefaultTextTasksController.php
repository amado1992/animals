<?php

namespace App\Http\Controllers;

use App\Models\DefaultTextTask;
use Illuminate\Http\Request;

class DefaultTextTasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $default_text_task = DefaultTextTask::orderByDesc('updated_at');

        $filterData = [];
        // Check if filter is set on session

        $default_text_task = $default_text_task->paginate(20);

        return view('default_text_task.index', compact('default_text_task', 'filterData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('default_text_task.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DefaultTextTask::create($request->all());

        return redirect(route('default-text-task.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $default_text_task             = DefaultTextTask::findOrFail($id);
        $default_text_task->created_at = date('Y-m-d', strtotime($default_text_task->created_at));

        return view('default_text_task.edit', compact('default_text_task'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $default_text_task = DefaultTextTask::findOrFail($id);
        $default_text_task->update($request->all());

        return redirect(route('default-text-task.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $default_text_task = DefaultTextTask::findOrFail($id);
                $default_text_task->delete();
            }
        }

        return response()->json();
    }
}
