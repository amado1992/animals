<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabelCreateRequest;
use App\Http\Requests\LabelUpdateRequest;
use App\Models\Labels;
use Illuminate\Http\Request;

class LabelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->get_data_by_request($request);

        return view('labels.index', $data);
    }

    public function get_data_by_request($request)
    {
        $data['labels'] = Labels::orderBy('created_at', 'DESC')->paginate(50);

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('labels.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\LabelCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LabelCreateRequest $request)
    {
        Labels::create($request->all());

        return redirect(route('labels.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Labels  $origin
     * @return \Illuminate\Http\Response
     */
    public function edit(Labels $label)
    {
        return view('labels.edit', compact('label'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\LabelUpdateRequest  $request
     * @param  \App\Models\Labels  $label
     * @return \Illuminate\Http\Response
     */
    public function update(LabelUpdateRequest $request, Labels $label)
    {
        $label->update($request->all());

        return redirect(route('labels.index'));
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
                $Delete = Labels::findOrFail($id);
                if ($Delete['name'] != 'offer' && $Delete['name'] != 'order' && $Delete['name'] != 'offer' && $Delete['name'] != 'surplus' && $Delete['name'] != 'new_contact' && $Delete['name'] != 'wanted' && $Delete['name'] != 'task') {
                    $Delete->delete();

                    return response()->json(['error' => false, 'message' => 'The label was dalete successfully']);
                } else {
                    return response()->json(['error' => true, 'message' => 'The selected label cannot be deleted because it is a default configuration of the emails']);
                }
            }
        } else {
            return response()->json(['error' => true, 'message' => 'The label was not dalete successfully']);
        }
    }
}
