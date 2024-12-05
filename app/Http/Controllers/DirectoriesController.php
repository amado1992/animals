<?php

namespace App\Http\Controllers;

use App\Http\Requests\DirectoryCreateRequest;
use App\Http\Requests\DirectoryUpdateRequest;
use App\Models\Directorie;
use Illuminate\Http\Request;

class DirectoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->get_data_by_request($request);

        return view('directories.index', $data);
    }

    public function get_data_by_request($request)
    {
        $data['directories'] = Directorie::orderBy('created_at', 'DESC')->paginate(50);

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('directories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DirectoryCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DirectoryCreateRequest $request)
    {
        Directorie::create($request->all());

        return redirect(route('directories.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Directorie  $directory
     * @return \Illuminate\Http\Response
     */
    public function edit(Directorie $directory)
    {
        return view('directories.edit', compact('directory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\DirectoryUpdateRequest  $request
     * @param  \App\Models\Directorie  $directory
     * @return \Illuminate\Http\Response
     */
    public function update(DirectoryUpdateRequest $request, Directorie $directory)
    {
        $directory->update($request->all());

        return redirect(route('directories.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $Delete = Directorie::findOrFail($id);
                $Delete->delete();
            }

            return response()->json(['error' => false, 'message' => 'The directories was dalete successfully']);
        } else {
            return response()->json(['error' => false, 'message' => 'The directories was not dalete successfully']);
        }
    }
}
