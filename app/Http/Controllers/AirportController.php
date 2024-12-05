<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirportCreateRequest;
use App\Http\Requests\AirportUpdateRequest;
use App\Models\Airport;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AirportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $airports = Airport::orderBy('name');

        $countries = Country::orderBy('name')->pluck('name', 'id');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('airport.filter')) {
            $request = session('airport.filter');
            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_name'])) {
                $airports->where('name', 'like', '%' . $request['filter_name'] . '%');

                $filterData = Arr::add($filterData, 'filter_name', 'Name: ' . $request['filter_name']);
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();

                $airports->where('country_id', $filterCountry->id);

                $filterData = Arr::add($filterData, 'filter_country', 'Country: ' . $filterCountry->name);
            }
        }

        $airports = $airports->get();

        return view('airports.index', compact('airports', 'countries', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('airport.filter');

        return redirect(route('airports.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');

        return view('airports.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AirportCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AirportCreateRequest $request)
    {
        Airport::create($request->all());

        return redirect(route('airports.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Airport  $airport
     * @return \Illuminate\Http\Response
     */
    public function show(Airport $airport)
    {
        return view('airports.show', compact('airport'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Airport  $airport
     * @return \Illuminate\Http\Response
     */
    public function edit(Airport $airport)
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');

        return view('airports.edit', compact('airport', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AirportUpdateRequest  $request
     * @param  \App\Models\Airport  $airport
     * @return \Illuminate\Http\Response
     */
    public function update(AirportUpdateRequest $request, Airport $airport)
    {
        $airport->update($request->all());

        return redirect(route('airports.show', [$airport->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Airport  $airport
     * @return \Illuminate\Http\Response
     */
    public function destroy(Airport $airport)
    {
        $airport->delete();

        return redirect(route('airports.index'));
    }

    /**
     * Filter airports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        // Set session code filter
        session(['airport.filter' => $request->query()]);

        return redirect(route('airports.index'));
    }

    /**
     * Remove from airport session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromAirportSession($key)
    {
        $query = session('airport.filter');
        Arr::forget($query, $key);
        session(['airport.filter' => $query]);

        return redirect(route('airports.index'));
    }
}
