<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Exports\AirfreightsExport;
use App\Http\Requests\AirfreightCreateRequest;
use App\Http\Requests\AirfreightUpdateRequest;
use App\Models\Airfreight;
use App\Models\Airport;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Offer;
use App\Models\OfferAirfreightPallet;
use App\Models\OfferSpecies;
use App\Models\OfferSpeciesAirfreight;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AirfreightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $airfreights = Airfreight::orderBy('departure_continent', 'ASC')->orderBy('arrival_continent', 'ASC')->where("standard_flight", 1);

        $regions = Region::pluck('name', 'id');

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('airfreight.filter')) {
            $request = session('airfreight.filter');

            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_source'])) {
                $airfreights->where('source', $request['filter_source']);

                $filterData = Arr::add($filterData, 'filter_source', 'Source: ' . $request['filter_source']);
            }

            if (isset($request['filter_tranport_agent_id'])) {
                $filterContact = Contact::where('id', $request['filter_tranport_agent_id'])->first();

                $airfreights->where('transport_agent', $filterContact->id);

                $filterData = Arr::add($filterData, 'filter_tranport_agent_id', 'Agent: ' . $filterContact->full_name);
            }

            if (isset($request['filter_from_continent'])) {
                $filterFromRegion = Region::where('id', $request['filter_from_continent'])->first();

                $airfreights->where('departure_continent', $filterFromRegion->id);

                $filterData = Arr::add($filterData, 'filter_from_continent', 'From region: ' . $filterFromRegion->name);
            }

            if (isset($request['filter_to_continent'])) {
                $filterToRegion = Region::where('id', $request['filter_to_continent'])->first();

                $airfreights->where('arrival_continent', $filterToRegion->id);

                $filterData = Arr::add($filterData, 'filter_to_continent', 'To region: ' . $filterToRegion->name);
            }

            if (isset($request['filter_type']) && $request['filter_type'] != 'all') {
                if ($request['filter_type'] == 'not_set') {
                    $airfreights->whereNull('type');
                } else {
                    $airfreights->where('type', $request['filter_type']);
                }

                $filterData = Arr::add($filterData, 'filter_type', 'Type: ' . $request['filter_type']);
            }

            if (isset($request['filter_start_offered_date'])) {
                $airfreights->where('offered_date', '>=', $request['filter_start_offered_date']);

                $filterData = Arr::add($filterData, 'filter_start_offered_date', 'Offered start date: ' . $request['filter_start_offered_date']);
            }

            if (isset($request['filter_end_offered_date'])) {
                $airfreights->where('offered_date', '<=', $request['filter_end_offered_date']);

                $filterData = Arr::add($filterData, 'filter_end_offered_date', 'Offered end date: ' . $request['filter_end_offered_date']);
            }

            if (isset($request['filter_start_modified_date'])) {
                $airfreights->where('updated_at', '>=', $request['filter_start_modified_date']);

                $filterData = Arr::add($filterData, 'filter_start_modified_date', 'Updated start at: ' . $request['filter_start_modified_date']);
            }

            if (isset($request['filter_end_modified_date'])) {
                $airfreights->where('updated_at', '<=', $request['filter_end_modified_date']);

                $filterData = Arr::add($filterData, 'filter_end_modified_date', 'Updated end at: ' . $request['filter_end_modified_date']);
            }

            if (isset($request['filter_remarks'])) {
                $airfreights->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_remarks', 'Remarks: ' . $request['filter_remarks']);
            }
            //dd(DB::getQueryLog()); // Show results of log
        }

        if (isset($request) && isset($request['recordsPerPage'])) {
            $airfreights = $airfreights->paginate($request['recordsPerPage']);
        } else {
            $airfreights = $airfreights->paginate(20);
        }

        return view('airfreights.index', compact(
            'airfreights',
            'regions',
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
        session()->forget('airfreight.filter');

        return redirect(route('airfreights.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($offerOrSpeciesId = null, $offerAirfreightType = null)
    {
        $currencies = Currency::get();
        $regions    = Region::pluck('name', 'id');

        $offerOrSpecies = null;
        if ($offerOrSpeciesId != null) {
            if (Str::contains($offerAirfreightType, 'volKg')) {
                $offerOrSpecies = OfferSpecies::where('id', $offerOrSpeciesId)->first();
            } else {
                $offerOrSpecies = Offer::where('id', $offerOrSpeciesId)->first();
            }
        }

        return view('airfreights.create', compact('regions', 'currencies', 'offerOrSpecies', 'offerAirfreightType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AirfreightCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AirfreightCreateRequest $request)
    {
        if(!empty($request["offered_date"])){
            $request["offered_date"] = date("Y-m-d H:i:s", strtotime($request["offered_date"]));
        }
        if(!empty($request["standard_flight"]) && $request["standard_flight"] === "on"){
            $request["standard_flight"] = 1;
        }else{
            $request["standard_flight"] = 0;
        }
        $airfreight = Airfreight::create($request->all());

        if ($request->offer_or_species_id != null) {
            if ($request->offer_airfreight_type != null) {
                if (Str::contains($request->offer_airfreight_type, 'volKg')) {
                    $offerSpecies = OfferSpecies::where('id', $request->offer_or_species_id)->first();
                    OfferSpeciesAirfreight::where('offer_species_id', $offerSpecies->id)->whereNull('airfreight_id')->delete();

                    $newOfferSpeciesAirfreight                   = new OfferSpeciesAirfreight();
                    $newOfferSpeciesAirfreight->offer_species_id = $request->offer_or_species_id;
                    $newOfferSpeciesAirfreight->airfreight_id    = $airfreight->id;
                    $newOfferSpeciesAirfreight->cost_volKg       = $airfreight->volKg_weight_cost * 0.90;
                    $newOfferSpeciesAirfreight->sale_volKg       = $airfreight->volKg_weight_cost * 1;
                    $newOfferSpeciesAirfreight->save();

                    return (Str::contains($request->offer_airfreight_type, 'offer')) ? redirect(route('offers.show', $offerSpecies->offer_id)) : redirect(route('orders.show', $offerSpecies->offer->order->id));
                } elseif (Str::contains($request->offer_airfreight_type, 'pallet')) {
                    $offer = Offer::where('id', $request->offer_or_species_id)->first();

                    $newOfferAirfreightPallet                      = new OfferAirfreightPallet();
                    $newOfferAirfreightPallet->offer_id            = $request->offer_or_species_id;
                    $newOfferAirfreightPallet->airfreight_id       = $airfreight->id;
                    $newOfferAirfreightPallet->pallet_quantity     = 1;
                    $newOfferAirfreightPallet->departure_continent = $request->departure_continent;
                    $newOfferAirfreightPallet->arrival_continent   = $request->arrival_continent;
                    $newOfferAirfreightPallet->pallet_sale_value   = ($airfreight->type == 'lowerdeck') ? $airfreight->lowerdeck_cost : $airfreight->maindeck_cost;
                    $newOfferAirfreightPallet->save();

                    return (Str::contains($request->offer_airfreight_type, 'offer')) ? redirect(route('offers.show', $offer->id)) : redirect(route('orders.show', $offer->order->id));
                }
            }
        } else {
            return redirect(route('airfreights.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $airfreight = Airfreight::find($id);

        if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
            $orderByDirection = $request['orderByDirection'];
            if ($request['orderByField'] === 'created_at') {
                $orderByField = $request['orderByField'];
            } else {
                $orderByField = $request['orderByField'];
            }
        } else {
            $orderByDirection = 'desc';
            $orderByField     = 'created_at';
        }

        if ($airfreight['type'] == 'volKg') {
            $airfreightsRelated = Airfreight::join('offers_species_airfreights', 'offers_species_airfreights.airfreight_id', '=', 'airfreights.id')
                ->where('departure_continent', $airfreight['departure_continent'])
                ->where('arrival_continent', $airfreight['arrival_continent'])
                ->select('*', 'airfreights.created_at as created_at', 'airfreights.updated_at as updated_at')
                ->orderBy('airfreights.' . $orderByField, $orderByDirection)
                ->paginate(10);
        } elseif ($airfreight['type'] == 'maindeck') {
            $airfreightsRelated = Airfreight::join('offers_airfreight_pallets', 'offers_airfreight_pallets.airfreight_id', '=', 'airfreights.id')
                ->where('airfreights.departure_continent', $airfreight['departure_continent'])
                ->where('airfreights.arrival_continent', $airfreight['arrival_continent'])
                ->select('*', 'airfreights.created_at as created_at', 'airfreights.updated_at as updated_at')
                ->orderBy('airfreights.' . $orderByField, $orderByDirection)
                ->paginate(10);
        } else {
            $airfreightsRelated = Airfreight::where('departure_continent', $airfreight['departure_continent'])
                ->where('arrival_continent', $airfreight['arrival_continent'])
                ->where('id', '!=', $id)
                ->orderBy($orderByField, $orderByDirection)
                ->paginate(10);
        }

        return view('airfreights.show', compact('airfreight', 'airfreightsRelated', 'orderByDirection', 'orderByField'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $airfreight               = Airfreight::findOrFail($id);
        $airfreight->offered_date = date('Y-m-d', strtotime($airfreight->offered_date));

        $currencies = Currency::get();
        $regions    = Region::pluck('name', 'id');

        return view('airfreights.edit', compact('airfreight', 'regions', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AirfreightUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AirfreightUpdateRequest $request, $id)
    {
        if(!empty($request["offered_date"])){
            $request["offered_date"] = date("Y-m-d H:i:s", strtotime($request["offered_date"]));
        }
        if(!empty($request["standard_flight"]) && $request["standard_flight"] === "on"){
            $request["standard_flight"] = 1;
        }else{
            $request["standard_flight"] = 0;
        }
        $updateAirfreight = Airfreight::findOrFail($id);
        $updateAirfreight->update($request->all());

        return redirect(route('airfreights.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $airfreightDelete = Airfreight::findOrFail($id);
        Storage::deleteDirectory('public/airfreights_docs/' . $airfreightDelete->id);
        $airfreightDelete->delete();

        return redirect(route('airfreights.index'));
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
            $message = false;
            foreach ($request->items as $id) {
                $deleteItem = Airfreight::findOrFail($id);

                if ($deleteItem->offer_airfreight || $deleteItem->offer_airfreight_pallet) {
                    $message = true;
                } else {
                    Storage::deleteDirectory('public/airfreights_docs/' . $deleteItem->id);
                    $deleteItem->delete();
                }
            }
        }

        return response()->json([
            'message' => ($message) ? 'There are airfreights related with an offer/order that cannot be deleted. Please remove the airfreight in the offer/order.' : '',
        ]);
    }

    /**
     * Filter airfreights.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterAirfreights(Request $request)
    {
        // Set session crate filter
        session(['airfreight.filter' => $request->query()]);

        return redirect(route('airfreights.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('airfreight.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['airfreight.filter' => $query]);

        return redirect(route('airfreights.index'));
    }

    /**
     * Remove from airfreight session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromAirfreightSession($key)
    {
        $query = session('airfreight.filter');
        Arr::forget($query, $key);
        session(['airfreight.filter' => $query]);

        return redirect(route('airfreights.index'));
    }

    //Upload airfreight file
    public function upload_file(Request $request)
    {
        if ($request->hasFile('file_to_upload')) {
            $file = $request->file('file_to_upload');

            //File Name
            $file_name = $file->getClientOriginalName();

            $path = Storage::putFileAs('public/airfreights_docs/' . $request->id, $file, $file_name);
        }

        return redirect(route('airfreights.index'));
    }

    /**
     * Delete airfreight file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file($airfreight_id, $file_name)
    {
        Storage::delete('public/airfreights_docs/' . $airfreight_id . '/' . $file_name);

        return redirect(route('airfreights.index'));
    }

    /**
     * Get airfreight by id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAirfreightById(Request $request)
    {
        $airfreight = null;
        if ($request->has('selectedFreightId')) {
            $airfreight                     = Airfreight::findOrFail($request->selectedFreightId);
            $airfreight->departureContinent = $airfreight->from_continent->name;
            $airfreight->arrivalContinent   = $airfreight->to_continent->name;
        }

        return response()->json(['success' => true, 'airfreight' => $airfreight, 'offerSpeciesAirfreightId' => '']);
    }

    /**
     * Get freights by countries and airports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAirfreightsByCountriesAndAirports(Request $request)
    {
        $data     = collect();
        $freights = null;
        if ($request->has('departure_continent') && $request->has('arrival_continent')) {
            if ($request->isPallet == true) {
                $freights = Airfreight::where([
                    ['departure_continent', $request->departure_continent],
                    ['arrival_continent', $request->arrival_continent], ])
                    ->where(function ($query) {
                        $query->where('type', 'lowerdeck')
                            ->orWhere('type', 'maindeck');
                    })
                    ->get();

                foreach ($freights as $freight) {
                    $text = $freight->from_continent->name . '-' . $freight->to_continent->name . ', Type: ' . $freight->type . ', Price: ' . $freight->currency . ' ' . (($freight->type == 'lowerdeck') ? $freight->lowerdeck_cost : $freight->maindeck_cost) . ', Remarks: ' . $freight->remarks;
                    $data->put($freight->id, $text);
                }
            } else {
                $freights = Airfreight::where([
                    ['departure_continent', $request->departure_continent],
                    ['arrival_continent', $request->arrival_continent], ])
                    ->where(function ($query) {
                        $query->where('type', 'volKg')
                            ->orWhere('type', null);
                    })
                    ->get();

                foreach ($freights as $freight) {
                    $text = $freight->from_continent->name . '-' . $freight->to_continent->name . ', Type: ' . $freight->type . ', Price: ' . $freight->currency . ' ' . $freight->volKg_weight_cost . ', Remarks: ' . $freight->remarks;
                    $data->put($freight->id, $text);
                }
            }

            $freights = $data->toArray();
        }

        return response()->json(['success' => true, 'freights' => $freights]);
    }

    //Export excel document with airfreights info.
    public function export(Request $request)
    {
        $file_name = 'Airfreights list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $airfreights = Airfreight::whereIn('id', explode(',', $request->items))->orderBy('created_at')->get();

        $export = new AirfreightsExport($airfreights);

        return Excel::download($export, $file_name);
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePrecies(Request $request)
    {
        if (!empty($request->id)) {
            $airfreight = Airfreight::find($request->id);

            $airfreight[$request->field] = $request->value;
            $airfreight['updated_at']    = Carbon::now()->format('Y-m-d H:i:s');
            $airfreight->save();
        }

        return response()->json(['error' => false, 'message' => 'Surplus data updated successfully']);
    }
}
