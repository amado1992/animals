<?php

namespace App\Http\Controllers;

use App\Enums\ProtocolSections;
use App\Http\Requests\GuidelineCreateRequest;
use App\Http\Requests\GuidelineUpdateRequest;
use App\Models\Guideline;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProtocolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories    = ProtocolSections::get();
        $categories    = Arr::prepend($categories, 'All', 'all');
        $categoryField = null;

        $protocols = Guideline::where('section', '<>', 'guidelines')
            ->where('section', '<>', 'offers_reservations_contracts')
            ->orderByDesc('updated_at');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('protocol.filter')) {
            $request = session('protocol.filter');

            if (isset($request['categoryField']) && $request['categoryField'] != 'all') {
                $categoryField = $request['categoryField'];

                $protocols->where('section', $categoryField);

                $filterData = Arr::add($filterData, 'categoryField', 'Category: ' . $categoryField);
            }

            if (isset($request['subject'])) {
                $protocols->where('subject', 'like', '%' . $request['subject'] . '%');

                $filterData = Arr::add($filterData, 'subject', 'Subject: ' . $request['subject']);
            }

            if (isset($request['remark'])) {
                $protocols->where('remark', 'like', '%' . $request['remark'] . '%');

                $filterData = Arr::add($filterData, 'remark', 'Remark: ' . $request['remark']);
            }
        }

        $protocols = $protocols->paginate(20);

        return view('protocols.index', compact(
            'protocols',
            'categories',
            'categoryField',
            'filterData'
        ));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('protocol.filter');

        return redirect(route('protocols.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ProtocolSections::get();

        return view('protocols.create', compact('categories'));
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
                'public/guidelines_docs/' . $request->section, $file, $file_name
            );
        }

        Guideline::create($request->all());

        return redirect(route('protocols.index'));
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
        $protocol = Guideline::findOrFail($id);

        $categories = ProtocolSections::get();

        return view('protocols.edit', compact('protocol', 'categories'));
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
                Storage::delete('public/guidelines_docs/' . $guidelineUpdate->section . '/' . $oldRelatedFile);
            }

            $path = Storage::putFileAs(
                'public/guidelines_docs/' . $request->section, $file, $file_name
            );
        }

        $guidelineUpdate->update($request->all());

        return redirect(route('protocols.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $protocolDelete = Guideline::findOrFail($id);

        if ($protocolDelete->related_filename != null) {
            Storage::delete('public/guidelines_docs/' . $protocolDelete->section . '/' . $protocolDelete->related_filename);
        }

        $protocolDelete->delete();

        return redirect(route('protocols.index'));
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
                $protocolDelete = Guideline::findOrFail($id);

                if ($protocolDelete->related_filename != null) {
                    Storage::delete('public/guidelines_docs/' . $protocolDelete->section . '/' . $protocolDelete->related_filename);
                }

                $protocolDelete->delete();
            }
        }

        return response()->json();
    }

    //Filter guidelines
    public function filter(Request $request)
    {
        // Set session crate filter
        session(['protocol.filter' => $request->query()]);

        return redirect(route('protocols.index'));
    }

    /**
     * Quick category selection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function categoryProtocols(Request $request)
    {
        $query                  = session('protocol.filter');
        $query['categoryField'] = $request->categoryField;
        session(['protocol.filter' => $query]);

        return redirect(route('protocols.index'));
    }

    /**
     * Remove from guideline session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromProtocolSession($key)
    {
        $query = session('protocol.filter');
        Arr::forget($query, $key);
        session(['protocol.filter' => $query]);

        return redirect(route('protocols.index'));
    }
}
