<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColorCreateRequest;
use App\Http\Requests\ColorUpdateRequest;
use App\Models\Labels;
use Illuminate\Http\Request;
use App\Http\Requests\LabelCreateRequest;
use App\Http\Requests\LabelUpdateRequest;
use App\Models\Color;

class ColorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->get_data_by_request($request);
        return view('colors.index', $data);
    }

    public function get_data_by_request($request)
    {
        $data["colors"] = Color::orderBy('created_at', "DESC")->paginate(50);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('colors.create');
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ColorCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ColorCreateRequest $request)
    {
        Color::create($request->all());
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Color  $origin
     * @return \Illuminate\Http\Response
     */
    public function edit(Color $color)
    {
        return view('colors.edit', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ColorUpdateRequest  $request
     * @param  \App\Models\Color  $label
     * @return \Illuminate\Http\Response
     */
    public function update(ColorUpdateRequest $request,  Color $color)
    {
        $color->update($request->all());
        return redirect(route('colors.index'));
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
                $delete = Color::findOrFail($id);
                if(!empty($delete)){
                    $delete->delete();
                    return response()->json(['error' =>false, 'message' => "The color was dalete successfully"]);
                }else{
                    return response()->json(['error' =>true, 'message' => "The selected color cannot be deleted because it is a default configuration of the emails"]);
                }
            }

        }else{
            return response()->json(['error' =>true, 'message' => "The color was not dalete successfully"]);
        }
    }
}
