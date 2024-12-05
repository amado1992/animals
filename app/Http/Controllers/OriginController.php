<?php

namespace App\Http\Controllers;

use App\Http\Requests\OriginCreateRequest;
use App\Http\Requests\OriginUpdateRequest;
use App\Models\Origin;
use Illuminate\Http\Request;

class OriginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $origins = Origin::all();

        return view('origins.index', compact('origins'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Origin  $origin
     * @return \Illuminate\Http\Response
     */
    public function edit(Origin $origin)
    {
        return view('origins.edit', compact('origin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('origins.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OriginCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OriginCreateRequest $request)
    {
        Origin::create($request->all());

        return redirect(route('origins.index'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OriginUpdateRequest  $request
     * @param  \App\Models\Origin  $region
     * @return \Illuminate\Http\Response
     */
    public function update(OriginUpdateRequest $request, Origin $origin)
    {
        $origin->update($request->all());

        return redirect(route('origins.index'));
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
                $wantedDelete = Origin::findOrFail($id);
                $wantedDelete->delete();
            }
        }

        return response()->json();
    }
}
