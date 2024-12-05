<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuidelineCreateRequest;
use App\Http\Requests\GuidelineUpdateRequest;
use App\Models\Guideline;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class GuidelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guidelines = Guideline::where('section', 'guidelines')->orderByDesc('updated_at');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('guideline.filter')) {
            $request = session('guideline.filter');

            if (isset($request['subject'])) {
                $guidelines->where('subject', 'like', '%' . $request['subject'] . '%');

                $filterData = Arr::add($filterData, 'subject', 'Subject: ' . $request['subject']);
            }

            if (isset($request['remark'])) {
                $guidelines->where('remark', 'like', '%' . $request['remark'] . '%');

                $filterData = Arr::add($filterData, 'remark', 'Remark: ' . $request['remark']);
            }

            if ($request['category'] != 'any') {
                $guidelines->where('category', $request['category']);

                $filterData = Arr::add($filterData, 'category', 'Category: ' . $request['category']);
            }
        }

        $guidelines = $guidelines->paginate(20);

        return view('guidelines.index', compact('guidelines', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('guideline.filter');

        return redirect(route('guidelines.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('guidelines.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\GuidelineCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GuidelineCreateRequest $request)
    {
        //Only for information.
        /*$request->validate([
            'file' => 'required|mimes:pdf,xlx,csv|max:2048'
            //'string_param' => 'required:max:255',
            //'param' => 'required',
            //'int_param' => 'required|numeric'
        ]);*/

        if ($request->hasFile('relatedFile')) {
            $file = $request->file('relatedFile');

            //File Name
            $file_name                   = $file->getClientOriginalName();
            $request['related_filename'] = $file_name;

            //File Extension. Only for information.
            $file_extension = $file->getClientOriginalExtension();

            //File Real Path. Only for information.
            $file_real_path = $file->getRealPath();

            //File Size. Only for information.
            $file_size = $file->getSize();

            //File Mime Type. Only for information.
            $file_mime_type = $file->getMimeType();

            $path = Storage::putFileAs(
                'public/guidelines_docs', $file, $file_name
            );
        }

        Guideline::create($request->all());

        return redirect(route('guidelines.index'));
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
        $guideline = Guideline::findOrFail($id);

        return view('guidelines.edit', compact('guideline'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\GuidelineUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GuidelineUpdateRequest $request, $id)
    {
        $guidelineUpdate = Guideline::findOrFail($id);

        if ($request->hasFile('relatedFile')) {
            $file = $request->file('relatedFile');

            //File Name
            $oldRelatedFile              = $guidelineUpdate->relatedFile;
            $file_name                   = $file->getClientOriginalName();
            $request['related_filename'] = $file_name;

            if ($oldRelatedFile != null) {
                Storage::delete('public/guidelines_docs/' . $oldRelatedFile);
            }

            $path = Storage::putFileAs(
                'public/guidelines_docs', $file, $file_name
            );
        }

        $guidelineUpdate->update($request->all());

        return redirect(route('guidelines.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guidelineDelete = Guideline::findOrFail($id);

        if ($guidelineDelete->related_filename != null) {
            Storage::delete('public/guidelines_docs/' . $guidelineDelete->related_filename);
        }

        $guidelineDelete->delete();

        return redirect(route('guidelines.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $guidelineDelete = Guideline::findOrFail($id);

                if ($guidelineDelete->related_filename != null) {
                    Storage::delete('public/guidelines_docs/' . $guidelineDelete->related_filename);
                }

                $guidelineDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter guidelines
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['guideline.filter' => $request->query()]);

        return redirect(route('guidelines.index'));
    }

    /**
     * Remove from guideline session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromGuidelineSession($key)
    {
        $query = session('guideline.filter');
        Arr::forget($query, $key);
        session(['guideline.filter' => $query]);

        return redirect(route('guidelines.index'));
    }
}
