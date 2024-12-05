<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryCreateRequest;
use App\Http\Requests\CountryUpdateRequest;
use App\Models\Airport;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::orderBy('name');

        $regions = Region::orderBy('name')->pluck('name', 'id');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('country.filter')) {
            $request = session('country.filter');
            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_name'])) {
                $countries->where('name', 'like', '%' . $request['filter_name'] . '%');

                $filterData = Arr::add($filterData, 'filter_name', 'Name: ' . $request['filter_name']);
            }

            if (isset($request['filter_region'])) {
                $filterRegion = Region::where('id', $request['filter_region'])->first();

                $countries->where('region_id', $filterRegion->id);

                $filterData = Arr::add($filterData, 'filter_region', 'Region: ' . $filterRegion->name);
            }

            if (isset($request['filter_language'])) {
                $countries->where('language', $request['filter_language']);

                $filterData = Arr::add($filterData, 'filter_language', 'Language: ' . $request['filter_language']);
            }
        }

        $countries = $countries->get();

        return view('countries.index', compact('countries', 'regions', 'filterData'));
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('country.filter');

        return redirect(route('countries.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $regions = Region::orderBy('name')->pluck('name', 'id');

        return view('countries.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CountryCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryCreateRequest $request)
    {
        Country::create($request->all());

        return redirect(route('countries.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        //$information = Country::where('country_code', $country->country_code)->first()->hydrate('cities')->cities->first();
        $information = Country::where('country_code', $country->country_code)->first()->airports->first();

        return view('countries.show', compact('country', 'information'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit(Country $country)
    {
        $regions = Region::orderBy('name')->pluck('name', 'id');

        return view('countries.edit', compact('country', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CountryUpdateRequest  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(CountryUpdateRequest $request, Country $country)
    {
        $country->update($request->all());

        return redirect(route('countries.show', [$country->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect(route('countries.index'));
    }

    /**
     * Get country airports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAirportsByCountryId(Request $request)
    {
        $airports = null;
        if ($request->has('value')) {
            $airports = Airport::where('country_id', $request->value)->orderBy('name')->pluck('name', 'id');
        }

        return response()->json(['success' => true, 'airports' => $airports, 'total_airports' => count($airports)]);
    }

    /**
     * Get area countries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCountriesByArea(Request $request)
    {
        $countries = null;
        if (isset($request->value)) {
            $countries = Country::whereHas('region.area_region', function ($query) use ($request) {
                $query->where('id', $request->value);
            })
                ->orderBy('name')
                ->pluck('name', 'id');
        } else {
            $countries = Country::orderBy('name')->pluck('name', 'id');
        }

        return response()->json(['success' => true, 'countries' => $countries, 'total_countries' => count($countries)]);
    }

    /**
     * Filter countries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        // Set session code filter
        session(['country.filter' => $request->query()]);

        return redirect(route('countries.index'));
    }

    /**
     * Remove from country session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromCountrySession($key)
    {
        $query = session('country.filter');
        Arr::forget($query, $key);
        session(['country.filter' => $query]);

        return redirect(route('countries.index'));
    }
}
