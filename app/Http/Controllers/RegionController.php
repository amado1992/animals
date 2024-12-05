<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionCreateRequest;
use App\Http\Requests\RegionUpdateRequest;
use App\Models\AreaRegion;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::get();

        return view('regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = AreaRegion::orderBy('name')->pluck('name', 'id');

        return view('regions.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\RegionCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionCreateRequest $request)
    {
        Region::create($request->all());

        return redirect(route('regions.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function show(Region $region)
    {
        return view('regions.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        $areas = AreaRegion::orderBy('name')->pluck('name', 'id');

        return view('regions.edit', compact('region', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RegionUpdateRequest  $request
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function update(RegionUpdateRequest $request, Region $region)
    {
        $region->update($request->all());

        return redirect(route('regions.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function destroy(Region $region)
    {
        $region->delete();

        return redirect(route('regions.index'));
    }

    /**
     * Get continent countries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCountriesByRegionId(Request $request)
    {
        $countries = null;
        if ($request->has('value')) {
            $countries = Country::where('region_id', $request->value)->orderBy('name')->pluck('name', 'id');
        }

        return response()->json(['success' => true, 'countries' => $countries]);
    }
}
