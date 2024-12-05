<?php

namespace App\Http\Controllers;

use App\Enums\AgeGroup;
use App\Enums\AvailabilityGroup;
use App\Enums\ConfirmOptions;
use App\Enums\Currency;
use App\Enums\OurSurplusOrderByOptions;
use App\Enums\Size;
use App\Exports\StockExport;
use App\Http\Requests\OurSurplusCreateRequest;
use App\Http\Requests\OurSurplusUpdateRequest;
use App\Models\Contact;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Classification;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\Origin;
use App\Models\OurSurplus;
use App\Models\OurSurplusList;
use App\Models\OurWantedList;
use App\Models\Region;
use App\Models\Surplus;
use Carbon\Carbon;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class OurSurplusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries    = Country::orderBy('name')->pluck('name', 'id');
        $regionsNames = Region::pluck('name', 'id');
        $areas        = AreaRegion::all();

        $currencies      = Currency::get();
        $availability    = AvailabilityGroup::get();
        $origin          = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup        = AgeGroup::get();
        $sizes           = Size::get();
        $confirm_options = ConfirmOptions::get();

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $ourSurplusLists = OurSurplusList::get();
        $theWantedLists  = OurWantedList::pluck('name', 'id');

        $orderByOptions = OurSurplusOrderByOptions::get();

        $result_array           = $this->get_records_by_filter();
        $filterData             = $result_array['filterData'];
        $ourSurpluses           = $result_array['ourSurpluses'];
        $orderByDirection       = $result_array['orderByDirection'];
        $orderByField           = $result_array['orderByField'];
        $ourSurplusListSelected = $result_array['ourSurplusListSelected'];
        $filter_imagen_species = $result_array["filter_imagen_species"];

        $array_object_results = [];
        if (isset($filterData['filter_same_surplus'])) {
            foreach ($ourSurpluses as $groupByAnimal) {
                foreach ($groupByAnimal as $groupByOrigin) {
                    foreach ($groupByOrigin as $groupByRegion) {
                        if (count($groupByRegion) > 1) {
                            foreach ($groupByRegion as $ourSurplus) {
                                array_push($array_object_results, $ourSurplus);
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($ourSurpluses as $ourSurplus) {
                array_push($array_object_results, $ourSurplus);
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = $result_array['recordsPerPage'];

        $currentItems = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);

        $ourSurpluses = new LengthAwarePaginator($currentItems, count($array_object_results), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        //dd($ourSurpluses);

        return view(
            'our_surplus.index', compact(
                'ourSurpluses',
                'availability',
                'countries',
                'areas',
                'regionsNames',
                'origin',
                'ageGroup',
                'sizes',
                'currencies',
                'confirm_options',
                'ourSurplusLists',
                'ourSurplusListSelected',
                'theWantedLists',
                'classes',
                'orderByOptions',
                'orderByDirection',
                'orderByField',
                'filterData',
                'filter_imagen_species'
            )
        );
    }

    /**
     * Calculate cost percentage.
     *
     * @param  \App\Http\Requests\Request $request
     * @return float|int
     */
    public function calculateCostPercentage(Request $request)
    {
        return $this->getCalculateCost($request->sale_price);
    }

    public function getCalculateCost($sale_price)
    {
        $percentages = config('constants.cost_percentage');
        $cost        = 0;
        foreach ($percentages as $row) {
            if ($sale_price >= $row['sales'] && $sale_price <= $row['between']) {
                $cost = ($sale_price * $row['percentage']) / 100;
                $cost = round($cost, -1);
                break;
            }
        }

        return $cost;
    }

    public function calculateSalesPercentage(Request $request)
    {
        return $this->getCalculateSales($request->cost_price);
    }

    public function getCalculateSales($cost_price)
    {
        if (!empty($cost_price)) {
            $percentages = config('constants.sales_percentage');
            $cost        = 0;
            foreach ($percentages as $row) {
                if ($cost_price >= $row['sales'] && $cost_price <= $row['between']) {
                    $cost = $cost_price * $row['percentage'];
                    $cost = round($cost, -1);
                    break;
                }
            }

            return $cost;
        }

        return 0;
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('our_surplus.filter');

        return redirect(route('our-surplus.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries    = Country::orderBy('name')->pluck('name', 'id');
        $regionsNames = Region::pluck('name', 'id');
        $areas        = AreaRegion::all();

        $currencies   = Currency::get();
        $availability = AvailabilityGroup::get();
        $origin       = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup     = AgeGroup::get();
        $sizes        = Size::get();

        $ourSurplusLists = OurSurplusList::pluck('name', 'id');

        return view(
            'our_surplus.create', compact(
                'availability',
                'areas',
                'regionsNames',
                'countries',
                'origin',
                'ageGroup',
                'sizes',
                'currencies',
                'ourSurplusLists'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OurSurplusCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(OurSurplusCreateRequest $request)
    {
        //dd($request->all());
        $origin         = $request->origin;
        $region         = $request->input('region');
        $area_region_id = $request->input('area');

        $request['region_id']      = $region;
        $request['area_region_id'] = $area_region_id;
        $request['is_public']      = $request->input('is_public') ? true : false;

        /*$validator = Validator::make($request->toArray(), []);

        $ourSurplusAlreadyExist = OurSurplus::where('animal_id', $request->animal_id)
                                            ->when($area_region_id, function ($query, $area_region_id) {
                                                return $query->where('area_region_id', $area_region_id);
                                            })
                                            ->when($origin, function ($query, $origin) {
                                                return $query->where('origin', $origin);
                                            })
                                            ->get();

        if ($ourSurplusAlreadyExist != null) {
            $validator->errors()->add('already_exist', 'A surplus with the same species, origin, and area already exist.');

            return redirect(route('our-surplus.create'))->withInput($request->toArray())->withErrors($validator);
        }
        else*/ {
            $ourSurplus = OurSurplus::create($request->all());

            $ourSurplus->area_regions()->sync($request->area_id);
            $ourSurplus->oursurplus_lists()->sync($request->ourSurplusLists);

            $surpluses = [];
            $surpluses = Surplus::where('animal_id', $request->animal_id)
                ->when(
                    $area_region_id, function ($query, $area_region_id) {
                        return $query->where('area_region_id', $area_region_id);
                    }
                )
                ->when(
                    $origin, function ($query, $origin) {
                        return $query->where('origin', $origin);
                    }
                )
                ->get();

        if (count($surpluses) > 0) {
            foreach ($surpluses as $surplus) {
                if ($ourSurplus->salePriceM > 0) {
                    $surplus->salePriceM = $ourSurplus->salePriceM;
                }
                if ($ourSurplus->salePriceF > 0) {
                    $surplus->salePriceF = $ourSurplus->salePriceF;
                }
                if ($ourSurplus->salePriceU > 0) {
                    $surplus->salePriceU = $ourSurplus->salePriceU;
                }
                if ($ourSurplus->salePriceP > 0) {
                    $surplus->salePriceP = $ourSurplus->salePriceP;
                }

                $surplus->update();

                if ($surplus->costPriceM == 0 && $surplus->costPriceF == 0 && $surplus->costPriceU == 0 && $surplus->costPriceP == 0) {
                    $surplus->update(['warning_indication' => 1]);
                } elseif ($surplus->salePriceM == 0 && $surplus->salePriceF == 0 && $surplus->salePriceU == 0 && $surplus->salePriceP == 0) {
                    $surplus->update(['warning_indication' => 1]);
                } else {
                    $surplus->update(['warning_indication' => 0]);
                }
            }
        }

            return redirect(route('our-surplus.index'));
        }
    }

    /**
     * Check same record by: species, origin and area.
     *
     * @param  \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkSameRecord(Request $request)
    {
        $ourSurplusId           = $request->oursurplus_id;
        $ourSurplusAlreadyExist = OurSurplus::when(
            $ourSurplusId, function ($query, $ourSurplusId) {
                return $query->where('id', '<>', $ourSurplusId);
            }
        )
            ->where('animal_id', $request->animal_id)
            ->where('area_region_id', $request->area_region_id)
            ->where('origin', $request->origin)
            ->first();

        if ($ourSurplusAlreadyExist != null) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $ourSurplus = OurSurplus::findOrFail($id);
        if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
            $orderByDirection = $request['orderByDirection'];
            if ($request['orderByField'] === 'created_at') {
                $orderByField = 'surplus.' . $request['orderByField'];
            } else {
                $orderByField = $request['orderByField'];
            }
        } else {
            $orderByDirection = 'desc';
            $orderByField     = 'surplus.created_at';
        }
        $origin = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');

        $animalRelatedSurplus = $ourSurplus->animal->surpluses($orderByField, $orderByDirection)
            ->select('*', 'surplus.created_at as created_at', 'surplus.created_at as updated_at')
            ->join('organisations', 'organisations.id', '=', 'surplus.organisation_id')
            ->where('surplus_status', '<>', 'collection')
            ->where('animal_id', $ourSurplus->animal_id)
            ->where('area_region_id', $ourSurplus->area_region_id)
            ->where('origin', $ourSurplus->origin)
            ->orderByDesc('surplus.updated_at')
            ->paginate(10);

        return view('our_surplus.show', compact('ourSurplus', 'orderByDirection', 'orderByField', 'animalRelatedSurplus', 'origin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currencies   = Currency::get();
        $availability = AvailabilityGroup::get();
        $origin       = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup     = AgeGroup::get();
        $sizes        = Size::get();

        $ourSurplus           = OurSurplus::findOrFail($id);
        $ourSurplus['animal'] = ($ourSurplus->animal != null) ? $ourSurplus->animal->common_name . ' (' . $ourSurplus->animal->scientific_name . ')' : 'No animal selected.';

        $areas        = AreaRegion::all();
        $regionsNames = Region::pluck('name', 'id');
        $countries    = Country::orderBy('name')->pluck('name', 'id');

        $ourSurplusAreasSelected = $ourSurplus->area_regions()->pluck('area_region_id');

        $ourSurplusLists         = OurSurplusList::pluck('name', 'id');
        $ourSurplusListsSelected = $ourSurplus->oursurplus_lists()->pluck('our_surplus_list_id');

        return view(
            'our_surplus.edit', compact(
                'ourSurplus',
                'availability',
                'countries',
                'origin',
                'ageGroup',
                'sizes',
                'currencies',
                'areas',
                'regionsNames',
                'ourSurplusAreasSelected',
                'ourSurplusLists',
                'ourSurplusListsSelected'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OurSurplusUpdateRequest $request
     * @param  int                                        $id
     * @return \Illuminate\Http\Response
     */
    public function update(OurSurplusUpdateRequest $request, $id)
    {
        $ourSurplus = OurSurplus::findOrFail($id);

        $origin         = $request->origin;
        $region         = $request->input('region');
        $area_region_id = $request->input('area');

        $request['region_id']      = $region;
        $request['area_region_id'] = $area_region_id;
        $request['is_public']      = $request->input('is_public') ? true : false;

        $validator = Validator::make($request->toArray(), []);

        $ourSurplusAlreadyExist = OurSurplus::where('id', '<>', $id)
            ->where('animal_id', $request->animal_id)
            ->when(
                $area_region_id, function ($query, $area_region_id) {
                    return $query->where('area_region_id', $area_region_id);
                }
            )
            ->when(
                $origin, function ($query, $origin) {
                    return $query->where('origin', $origin);
                }
            )
            ->get();

        /*if ($ourSurplusAlreadyExist != null) {
            $validator->errors()->add('already_exist', 'A surplus with the same species, origin, and area already exist.');

            return redirect(route('our-surplus.edit', $ourSurplus->id))->withErrors($validator);
        }
        else {*/
        $ourSurplus->update($request->all());

        $ourSurplus->area_regions()->sync($request->area_id);
        $ourSurplus->oursurplus_lists()->sync($request->ourSurplusLists);

        $surpluses = [];
        $surpluses = Surplus::where('animal_id', $request->animal_id)
            ->when(
                $area_region_id, function ($query, $area_region_id) {
                    return $query->where('area_region_id', $area_region_id);
                }
            )
            ->when(
                $origin, function ($query, $origin) {
                    return $query->where('origin', $origin);
                }
            )
            ->get();

        if (count($surpluses) > 0) {
            foreach ($surpluses as $surplus) {
                if ($ourSurplus->salePriceM > 0) {
                    $surplus->salePriceM = $ourSurplus->salePriceM;
                }
                if ($ourSurplus->salePriceF > 0) {
                    $surplus->salePriceF = $ourSurplus->salePriceF;
                }
                if ($ourSurplus->salePriceU > 0) {
                    $surplus->salePriceU = $ourSurplus->salePriceU;
                }
                if ($ourSurplus->salePriceP > 0) {
                    $surplus->salePriceP = $ourSurplus->salePriceP;
                }
                $surplus->new_animal = 1;
                $surplus->update();

                if ($surplus->costPriceM == 0 && $surplus->costPriceF == 0 && $surplus->costPriceU == 0 && $surplus->costPriceP == 0) {
                    $surplus->update(['warning_indication' => 1]);
                } elseif ($surplus->salePriceM == 0 && $surplus->salePriceF == 0 && $surplus->salePriceU == 0 && $surplus->salePriceP == 0) {
                    $surplus->update(['warning_indication' => 1]);
                } else {
                    $surplus->update(['warning_indication' => 0]);
                }
            }
        }

        return redirect(route('our-surplus.show', $ourSurplus->id));
        //}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ourSurplusDelete = OurSurplus::findOrFail($id);
        Storage::deleteDirectory('public/oursurplus_docs/' . $ourSurplusDelete->id);
        $ourSurplusDelete->delete();

        return redirect(route('our-surplus.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $ourSurplusDelete = OurSurplus::findOrFail($id);
                Storage::deleteDirectory('public/oursurplus_docs/' . $ourSurplusDelete->id);
                $ourSurplusDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Upload files for the standard surplus.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request)
    {
        if($request->hasFile('file')) {
            $stdSurplus = OurSurplus::findOrFail($request->relatedSurplusurplusId);

            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            $path = Storage::putFileAs('public/oursurplus_docs/'.$stdSurplus->id, $file, $file_name);
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Select surplus list.
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function selectOurSurplusList(Request $request)
    {
        session()->forget('our_surplus.filter');

        session(['our_surplus.filter' => ['ourSurplusListsTopSelect' => $request->ourSurplusListsTopSelect]]);

        return redirect(route('our-surplus.index'));
    }

    /**
     * Add surplus list.
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function saveOurSurplusList(Request $request)
    {
        OurSurplusList::create($request->all());

        return response()->json();
    }

    /**
     * Remove surplus list.
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function deleteOurSurplusList(Request $request)
    {
        $itemDelete = OurSurplusList::findOrFail($request->id);

        if ($itemDelete != null) {
            $itemDelete->delete();
            session()->forget('our_surplus.filter');
        }

        return response()->json();
    }

    /**
     * Filter standard surplus.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function filterOurSurplus(Request $request)
    {
        // Check if filter is set on session
        if (session()->has('our_surplus.filter')) {
            $query = session('our_surplus.filter');
            $query = Arr::collapse([$query, $request->query()]);
            session(['our_surplus.filter' => $query]);
        } else { // Set session standard surplus filter
            session(['our_surplus.filter' => $request->query()]);
        }

        return redirect(route('our-surplus.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('our_surplus.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['our_surplus.filter' => $query]);

        return redirect(route('our-surplus.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('our_surplus.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['our_surplus.filter' => $query]);

        return redirect(route('our-surplus.index'));
    }

    /**
     * Remove from our_surplus session.
     *
     * @param  string $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromOurSurplusSession($key)
    {
        $query = session('our_surplus.filter');
        Arr::forget($query, $key);
        session(['our_surplus.filter' => $query]);

        return redirect(route('our-surplus.index'));
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function editSelectedRecords(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $ourSurplus = OurSurplus::findOrFail($id);

                if (isset($request->availability)) {
                    $ourSurplus->update(['availability' => $request->availability]);
                }

                if (isset($request->is_public)) {
                    $ourSurplus->update(['is_public' => ($request->is_public == 'yes') ? true : false]);
                }

                if (isset($request->origin)) {
                    $ourSurplus->update(['origin' => $request->origin]);
                }

                if (isset($request->age_group)) {
                    $ourSurplus->update(['age_group' => $request->age_group]);
                }

                if (isset($request->cost_currency)) {
                    $ourSurplus->update(['cost_currency' => $request->cost_currency]);
                }

                if (isset($request->sale_currency)) {
                    $ourSurplus->update(['sale_currency' => $request->sale_currency]);
                }

                if ($request->areas > 0) {
                    $ourSurplus->regions()->sync($request->areas);
                }

                if (isset($request->add_to_stock_lists)) {
                    $ourSurplus->oursurplus_lists()->syncWithoutDetaching($request->add_to_stock_lists);
                }

                if (isset($request->remove_from_stock_lists)) {
                    $ourSurplus->oursurplus_lists()->detach($request->remove_from_stock_lists);
                }
            }
        }

        return response()->json();
    }

    /**
     * Generate pdf or html surplus list.
     *
     * @param \Illuminate\Http\Request $request the request
     * 
     * @return \Illuminate\Http\Response
     */
    public function printOurSurplusList(Request $request)
    {
        $document = $request->document_type;
        $language = $request->language;

        $print_stuffed = $request->print_stuffed ?? "no";

        if ($request->export_option !== 'all') {
            if ($print_stuffed == 'yes') {
                $surplusToPrint = OurSurplus::whereIn('id', $request->items)->whereNotNull('catalog_pic')->get();
            } else {
                $surplusToPrint = OurSurplus::whereIn('id', $request->items)->get();
            }
        } else {
            $result_array = $this->get_records_by_filter(true, $print_stuffed);
            $surplusToPrint = $result_array['surpluses'];

            if ($document == 'pdf' && $surplusToPrint->count() > 300) {
                return response()->json(['success' => false, 'message' => 'You cannot print more than 300 records in PDF. Please, select HTML and convert HTML file to PDF.']);
            }
        }


        $surplus_for_email = [
            'void' => [
                'void' => OurSurplus::whereIn('id', json_decode($request->cover_animals))->get(),
            ]
        ];
        $surplusToPrint    = $surplusToPrint->sortBy(
            function ($surplus, $key) {
                if (!empty($surplus->animal->classification->order)) {
                    $cammon_name = $surplus->animal->classification->order->common_name;
                } else {
                    $cammon_name = "";
                }

                return [$surplus->animal->code_number, $cammon_name, $surplus->animal->scientific_name];
            }
        );
        $surplusToPrint = $surplusToPrint->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);

        $name         = ($language == 'english')
            ? 'surplus_list_' . Carbon::now()->format('Y-m-d-H-m-s')
            : 'lista_de_excedentes_' . Carbon::now()->format('Y-m-d-H-m-s');        $extension    = '.' . $document;
        $has_prices   = $request->prices === 'yes' ? true : false;
        $date         = Carbon::now()->format('F j, Y');
        $rateEurUsd   = number_format(CurrencyRate::latest()->value('EUR_USD'), 2, '.', '');
        $rateUsdEur   = number_format(CurrencyRate::latest()->value('USD_EUR'), 2, '.', '');
        $picture      = $request->pictures;
        $type         = 'oursurplus';
        $is_standard  = true;
        $download_url = url('/') . '/storage/surplus_wanted_lists/' . $name . '.html';
        $wanted       = $request->wanted;

        if ($print_stuffed == "yes" && !empty($request->filter_client_id)) {
            $contact = Contact::findOrFail($request->filter_client_id);
            $fullname = $contact->letter_name;
        }
        
        if ($wanted == 'yes' && isset($request->wanted_list) != null) {
            $wantedListSelected = OurWantedList::find($request->wanted_list);
            $wantedToPrint      = $wantedListSelected->our_wanteds()->get()->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);
        } else {
            $wanted             = false;
            $wantedToPrint      = [];
        }

        $only_print_leadin = true;
        $row_limit         = 3;
        $template_vars     = compact(
            'date',
            'language',
            'only_print_leadin',
            'download_url',
            'rateEurUsd',
            'rateUsdEur',
            'document',
            'language',
            'surplusToPrint',
            'is_standard',
            'picture',
            'type',
            'print_stuffed',
            'has_prices',
            'row_limit',
            'name'
        );
        $template_vars['surplusToPrint'] = $surplus_for_email;
        $mjml = view(
            'pdf_documents.surplus_mjml_template',
            $template_vars
        )->render();

        Storage::put('public/surplus_wanted_lists/tmp.mjml', $mjml);
        $rel_storage_path = __DIR__ . '/../../../storage/app/public/surplus_wanted_lists/';
        $command_parts = [
            __DIR__ . '/../../../node_modules/.bin/mjml',
            $rel_storage_path . 'tmp.mjml',
            '-o ' . $rel_storage_path . $name . '_email.html'
        ];
        exec(implode(' ', $command_parts));

        $only_print_leadin = false;
        $row_limit         = 3000;
        $mjml = view(
            'pdf_documents.surplus_mjml_template',
            compact(
                'date',
                'rateEurUsd',
                'rateUsdEur',
                'document',
                'language',
                'surplusToPrint',
                'is_standard',
                'picture',
                'type',
                'print_stuffed',
                'only_print_leadin',
                'has_prices',
                'row_limit',
                'wanted',
                'wantedToPrint',
                'name'
            )
        )->render();

        Storage::put('public/surplus_wanted_lists/tmp.mjml', $mjml);

        $command_parts = [
            __DIR__ . '/../../../node_modules/.bin/mjml',
            $rel_storage_path . 'tmp.mjml',
            '-o ' . $rel_storage_path . $name . '.html'
        ];
        exec(implode(' ', $command_parts));
        unlink($rel_storage_path . 'tmp.mjml');

        if ($request->document_type === 'pdf') {
            $html = file_get_contents($rel_storage_path . $name . '.html');
            $document = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');
            Storage::put('public/surplus_wanted_lists/' . $name . '.pdf', $document->output());
        } else {
            $url = Storage::url('surplus_wanted_lists/' . $name . '_email' . $extension);
        }
        return response()->json(['success' => true, 'url' => $url, 'fileName' => $name . $extension]);
    }

    /**
     * Delete standard surplus file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file($oursurplus_id, $file_name)
    {
        Storage::delete('public/oursurplus_docs/' . $oursurplus_id . '/' . $file_name);

        return redirect(route('our-surplus.show', [$oursurplus_id]));
    }

    /**
     * Get related surplus of suppliers
     *
     * @param \Illuminate\Http\Request $request The request
     * 
     * @return \Illuminate\Http\Response
     */
    public function searchRelatedSurplusSuppliers(Request $request)
    {
        $ourSurplus = OurSurplus::findOrFail($request->id);

        $area_region_id = $ourSurplus->area_region_id;
        $origin         = $ourSurplus->origin;

        $surpluses = Surplus::with(['animal', 'contact', 'country'])
            ->where('animal_id', $ourSurplus->animal_id)
            ->when(
                $area_region_id, function ($query, $area_region_id) {
                    return $query->where('area_region_id', $area_region_id);
                }
            )
            ->when(
                $origin, function ($query, $origin) {
                    return $query->where('origin', $origin);
                }
            )
            ->orderByDesc('updated_at')
            ->get();

        foreach ($surpluses as $surplus) {
            $surplus->institution  = ($surplus->organisation) ? $surplus->organisation->name : '';
            $surplus->origin       = $surplus->origin_field;
            $surplus->age          = $surplus->age_field;
            $surplus->size         = $surplus->size_field;
            $surplus->updated_date = date('Y-m-d', strtotime($surplus->updated_at));
        }

        return response()->json(['success' => count($surpluses) > 0 ? true : false, 'surpluses' => $surpluses]);
    }

    //Export excel document with stock info.
    public function export(Request $request)
    {
        $file_name = 'Stock list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $ourSurpluses = OurSurplus::whereIn('id', explode(',', $request->items))->orderBy('updated_at')->get();

        $export = new StockExport($ourSurpluses);

        return Excel::download($export, $file_name);
    }

    public function get_records_by_filter($sw_list=false, $stuffer_list = "no")
    {
        $orderByDirection       = null;
        $orderByField           = null;
        $ourSurplusListSelected = null;
        $recordsPerPage         = 50;
        $filterData             = [];

        // Check if filter is set on session
        if (session()->has('our_surplus.filter')) {
            $request = session('our_surplus.filter');
        }

        if (isset($request['ourSurplusListsTopSelect']) && $request['ourSurplusListsTopSelect'] > 0) {
            $ourSurplusListSelected = OurSurplusList::find($request['ourSurplusListsTopSelect']);

            $ourSurpluses = $ourSurplusListSelected->our_surpluses()->orderByDesc('updated_at');

            $filterData = Arr::add($filterData, 'ourSurplusListsTopSelect', 'Stock list: ' . $ourSurplusListSelected->name);
        } else {
            $ourSurpluses = OurSurplus::with(['animal', 'area_regions'])->orderByDesc('updated_at');
        }

        //DB::enableQueryLog();
        if (isset($request)) {
            if (isset($request['recordsPerPage'])) {
                $recordsPerPage = $request['recordsPerPage'];
            }

            if (isset($request['filter_animal_option'])) {
                if ($request['filter_animal_option'] === 'by_id') {
                    if (isset($request['filter_animal_id'])) {
                        $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                        $ourSurpluses->where('animal_id', $filterAnimal->id);

                        $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
                    }
                } elseif ($request['filter_animal_option'] === 'by_name') {
                    if (isset($request['filter_animal_name'])) {
                        $ourSurpluses->whereHas(
                            'animal', function ($query) use ($request) {
                                $query->where('common_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                    ->orWhere('common_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                    ->orWhere('scientific_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                    ->orWhere('scientific_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                    ->orWhere('spanish_name', 'like', '%' . $request['filter_animal_name'] . '%');
                            }
                        );

                        $filterData = Arr::add($filterData, 'filter_animal_name', 'Animal name: ' . $request['filter_animal_name']);
                    }
                } else {
                    $ourSurpluses->whereNull('animal_id');

                    $filterData = Arr::add($filterData, 'filter_animal_option', 'Animal: empty');
                }
            }

            if (isset($request['filter_has_spanish_name'])) {
                if ($request['filter_has_spanish_name'] == 'yes') {
                    $ourSurpluses->whereHas(
                        'animal', function ($query) {
                            $query->whereNotNull('spanish_name')
                                ->where(DB::raw('TRIM(spanish_name)'), '<>', '');
                        }
                    );
                } else {
                    $ourSurpluses->whereHas(
                        'animal', function ($query) {
                            $query->whereNull('spanish_name')
                                ->orWhere(DB::raw('TRIM(spanish_name)'), '');
                        }
                    );
                }

                $filterData = Arr::add($filterData, 'filter_has_spanish_name', 'Has spanish name: ' . $request['filter_has_spanish_name']);
            }

            if (isset($request['filter_availability'])) {
                if ($request['filter_availability'] === 'empty') {
                    $ourSurpluses->whereNull('availability');
                } else {
                    $ourSurpluses->where('availability', $request['filter_availability']);
                }

                $filterData = Arr::add($filterData, 'filter_availability', 'Availability: ' . $request['filter_availability']);
            }

            if (isset($request['filter_is_public'])) {
                $ourSurpluses->where('is_public', ($request['filter_is_public'] == 'yes') ? true : false);

                $filterData = Arr::add($filterData, 'filter_is_public', 'Public: ' . $request['filter_is_public']);
            }

            if (isset($request['filter_origin'])) {
                if ($request['filter_origin'] === 'empty') {
                    $ourSurpluses->whereNull('origin');
                } else {
                    $ourSurpluses->where('origin', $request['filter_origin']);
                }

                $filterData = Arr::add($filterData, 'filter_origin', 'Origin: ' . $request['filter_origin']);
            }

            if (isset($request['filter_animal_class'])) {
                $ids_genera = [];

                $class  = Classification::where('id', $request['filter_animal_class'])->first();
                $order  = null;
                $family = null;
                $genus  = null;

                $filterData = Arr::add($filterData, 'filter_animal_class', 'Class: ' . $class->common_name);

                if (isset($request['filter_animal_order'])) {
                    $order = $class->under->where('id', $request['filter_animal_order'])->first();

                    $filterData = Arr::add($filterData, 'filter_animal_order', 'Order: ' . $order->common_name);
                }
                if (isset($request['filter_animal_family'])) {
                    $family = $order->under->where('id', $request['filter_animal_family'])->first();

                    $filterData = Arr::add($filterData, 'filter_animal_family', 'Family: ' . $family->common_name);
                }
                if (isset($request['filter_animal_genus'])) {
                    $genus = $family->under->where('id', $request['filter_animal_genus'])->first();

                    $filterData = Arr::add($filterData, 'filter_animal_genus', 'Genus: ' . $genus->common_name);
                }

                if ($genus != null) {
                    array_push($ids_genera, $genus->id);
                    $ourSurpluses->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $ourSurpluses->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                } elseif ($order != null) {
                    $families = $order->under->all();
                    foreach ($families as $order_family) {
                        $order_family_genera = $order_family->under->toArray();
                        foreach ($order_family_genera as $family_genus) {
                            array_push($ids_genera, $family_genus['id']);
                        }
                    }
                    $ourSurpluses->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                } elseif ($class != null) {
                    $orders = $class->under->all();
                    foreach ($orders as $class_order) {
                        $class_order_families = $class_order->under->all();
                        foreach ($class_order_families as $class_order_family) {
                            $class_order_family_genera = $class_order_family->under->toArray();
                            foreach ($class_order_family_genera as $family_genus) {
                                array_push($ids_genera, $family_genus['id']);
                            }
                        }
                    }
                    $ourSurpluses->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                }
            }

            if (isset($request['filter_have_cost_prices'])) {
                if ($request['filter_have_cost_prices'] == 'yes') {
                    $ourSurpluses->where(
                        function ($query) {
                            $query->where('costPriceM', '<>', '0')
                                ->orWhere('costPriceF', '<>', '0')
                                ->orWhere('costPriceU', '<>', '0')
                                ->orWhere('costPriceP', '<>', '0');
                        }
                    );
                } else {
                    $ourSurpluses->where(
                        [
                        ['costPriceM', '=', '0'],
                        ['costPriceF', '=', '0'],
                        ['costPriceU', '=', '0'],
                        ['costPriceP', '=', '0'], ]
                    );
                }

                $filterData = Arr::add($filterData, 'filter_have_cost_prices', 'Cost prices: ' . $request['filter_have_cost_prices']);
            }

            if (isset($request['filter_have_sale_prices'])) {
                if ($request['filter_have_sale_prices'] == 'yes') {
                    $ourSurpluses->where(
                        function ($query) {
                            $query->where('salePriceM', '<>', '0')
                                ->orWhere('salePriceF', '<>', '0')
                                ->orWhere('salePriceU', '<>', '0')
                                ->orWhere('salePriceP', '<>', '0');
                        }
                    );
                } else {
                    $ourSurpluses->where(
                        [
                        ['salePriceM', '=', '0'],
                        ['salePriceF', '=', '0'],
                        ['salePriceU', '=', '0'],
                        ['salePriceP', '=', '0'], ]
                    );
                }

                $filterData = Arr::add($filterData, 'filter_have_sale_prices', 'Sale prices: ' . $request['filter_have_sale_prices']);
            }

            if (isset($request['filter_intern_remarks'])) {
                $ourSurpluses->where('intern_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_intern_remarks', 'Intern remarks: ' . $request['filter_intern_remarks']);
            }

            if (isset($request['filter_region'])) {
                $filterRegion = Region::where('id', $request['filter_region'])->first();

                if ($request['filter_region'] == 0) {
                    $ourSurpluses->whereNull('region_id');
                } else {
                    $ourSurpluses->where('region_id', $filterRegion->id);
                }

                $filterData = Arr::add($filterData, 'filter_region', 'Region: ' . ($filterRegion != null ? $filterRegion->name : 'Empty'));
            }

            if (isset($request['filter_area'])) {
                $filterArea = AreaRegion::where('id', $request['filter_area'])->first();

                if ($request['filter_area'] == 0) {
                    $ourSurpluses->whereNull('area_region_id');
                } else {
                    $ourSurpluses->where('area_region_id', $filterArea->id);
                }

                $filterData = Arr::add($filterData, 'filter_area', 'Area: ' . ($filterArea != null ? $filterArea->name : 'Empty'));
            }

            if (isset($request['filter_updated_at_from'])) {
                $ourSurpluses->whereDate('updated_at', '>=', $request['filter_updated_at_from']);

                $filterData = Arr::add($filterData, 'filter_updated_at_from', 'Updated start at: ' . $request['filter_updated_at_from']);
            }

            if (isset($request['filter_updated_at_to'])) {
                $ourSurpluses->whereDate('updated_at', '<=', $request['filter_updated_at_to']);

                $filterData = Arr::add($filterData, 'filter_updated_at_to', 'Updated end at: ' . $request['filter_updated_at_to']);
            }

            if (isset($request['filter_areas_empty'])) {
                $ourSurpluses->whereDoesntHave('area_regions');

                $filterData = Arr::add($filterData, 'filter_areas_empty', 'Offer to: empty');
            } elseif (isset($request['filter_area_id'])) {
                $filter_areas = $request['filter_area_id'];

                $areas = AreaRegion::whereIn('id', $filter_areas)->get();

                $ourSurpluses->whereHas(
                    'area_regions', function ($query) use ($filter_areas) {
                        $query->whereIn('area_region_id', $filter_areas);
                    }
                );

                $areasLabel = '';
                foreach ($areas as $area) {
                    $areasLabel .= $area->short_cut . '-';
                }

                $filterData = Arr::add($filterData, 'filter_area_id', 'Offer to: ' . trim($areasLabel));
            }
            if (isset($request['filter_show_stuffed'])) {
                if ($request['filter_show_stuffed'] == 'no') {
                    $ourSurpluses->where(
                        function ($query) {
                            $query->where('origin', '!=', 'stuffed')
                                ->orwhereNull('origin');
                        }
                    );
                    $filterData = Arr::add($filterData, 'filter_show_stuffed', 'Show stuffed: ' . $request['filter_show_stuffed']);
                } else {
                    $ourSurpluses->where('origin', 'stuffed');
                    $filterData = Arr::add($filterData, 'filter_show_stuffed', 'Show stuffed: ' . $request['filter_show_stuffed']);
                }
            }

            if(isset($request['filter_upload_images'])) {
                if($request['filter_upload_images'] == "yes") {
                    $ourSurpluses->whereNotNull('catalog_pic')->where('catalog_pic', "!=", " ");
                }elseif($request['filter_upload_images'] == "no") {
                    $ourSurpluses->whereNull('catalog_pic')->orWhere('catalog_pic', " ");
                }

                $filterData = Arr::add($filterData, 'filter_upload_images', 'Upload Images: ' . $request['filter_upload_images']);
            }

            if(isset($request['filter_imagen_species'])) {
                if($request['filter_imagen_species'] === "yes") {
                    $ourSurpluses->whereHas(
                        'animal', function ($query) {
                            $query->whereNotNull('catalog_pic')->orWhere('catalog_pic', '!=', '');
                        }
                    );
                    $ourSurpluses->where("origin", "!=", "stuffed");
                    $noImages = array();
                    foreach($ourSurpluses->get() as $row){
                        if(!empty($row["animal"]) && !empty($row["animal"]->imagen_first)) {
                            array_push($noImages, $row["id"]);
                        }
                    }
                    if(!empty($noImages)) {
                        $ourSurpluses->orwhereIn('id', $noImages);
                    }
                }else{
                    $ourSurpluses->whereHas(
                        'animal', function ($query) {
                            $query->whereNull('catalog_pic')->orWhere('catalog_pic', '');
                        }
                    );
                    $noImages = array();
                    foreach($ourSurpluses->get() as $row){
                        if(!empty($row["animal"]) && !empty($row["animal"]->imagen_first)) {
                            array_push($noImages, $row["id"]);
                        }
                    }
                    if(!empty($noImages)) {
                        $ourSurpluses->whereNotIn('id', $noImages);
                    }
                }

                $filterData = Arr::add($filterData, 'filter_imagen_species', 'General images: ' . $request['filter_imagen_species']);
            }
            if($stuffer_list == "yes") {
                $ourSurpluses = $ourSurpluses->whereNotNull("catalog_pic");
            }
        }

        $ourSurpluses = $ourSurpluses->get();
        //dump(DB::getQueryLog());

        if (isset($request)) {
            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByField == 'common_name' || $orderByField == 'scientific_name' || $orderByField == 'code_number') {
                    if ($orderByDirection == 'desc') {
                        $ourSurpluses = $ourSurpluses->sortByDesc(
                            function ($our_surplus, $key) use ($orderByField) {
                                return $our_surplus->animal->$orderByField;
                            }
                        );
                    } else {
                        $ourSurpluses = $ourSurpluses->sortBy(
                            function ($our_surplus, $key) use ($orderByField) {
                                return $our_surplus->animal->$orderByField;
                            }
                        );
                    }
                } else {
                    if ($orderByDirection == 'desc') {
                        $ourSurpluses = $ourSurpluses->sortByDesc(
                            function ($our_surplus, $key) use ($orderByField) {
                                return $our_surplus->$orderByField;
                            }
                        );
                    } else {
                        $ourSurpluses = $ourSurpluses->sortBy(
                            function ($our_surplus, $key) use ($orderByField) {
                                return $our_surplus->$orderByField;
                            }
                        );
                    }
                }
            }

            if (isset($request['filter_same_surplus']) && !$sw_list) {
                $filterData = Arr::add($filterData, 'filter_same_surplus', 'Same surplus: ' . $request['filter_same_surplus']);

                $ourSurpluses = $ourSurpluses->groupBy(['animal_id', 'origin', 'area_region_id']);
            }
        }

        return [
            "filterData" => $filterData,
            "ourSurpluses" => $ourSurpluses,
            "orderByDirection" => $orderByDirection,
            "orderByField" => $orderByField,
            "ourSurplusListSelected" => $ourSurplusListSelected,
            "recordsPerPage" => $recordsPerPage,
            "filter_imagen_species" => $request["filter_imagen_species"] ?? "",
            "filter_upload_images" => $request["filter_upload_images"] ?? ""
        ];
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePrecies(Request $request)
    {
        if (!empty($request->id)) {
            $our_surplus = OurSurplus::find($request->id);
            $cost        = 0;

            if(!empty($our_surplus) && $our_surplus->origin != "stuffed") {
                if ($request->field === 'costPriceM') {
                    $cost                      = $this->getCalculateSales($request->value);
                    $our_surplus['salePriceM'] = $cost;
                }
                if ($request->field === 'costPriceF') {
                    $cost                      = $this->getCalculateSales($request->value);
                    $our_surplus['salePriceF'] = $cost;
                }
                if ($request->field === 'costPriceU') {
                    $cost                      = $this->getCalculateSales($request->value);
                    $our_surplus['salePriceU'] = $cost;
                }
                if ($request->field === 'costPriceP') {
                    $cost                      = $this->getCalculateSales($request->value);
                    $our_surplus['salePriceP'] = $cost;
                }
            }

            $our_surplus[$request->field] = $request->value;
            $our_surplus['updated_at']    = Carbon::now()->format('Y-m-d H:i:s');
            $our_surplus->save();
        }

        return response()->json(['error' => false, 'message' => 'Surplus data updated successfully', 'cost' => number_format($cost, 2, '.', '')]);
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePreciesSale(Request $request)
    {
        if (!empty($request->id)) {
            $our_surplus                  = OurSurplus::find($request->id);
            $our_surplus[$request->field] = $request->value;
            $our_surplus['updated_at']    = Carbon::now()->format('Y-m-d H:i:s');
            $our_surplus->save();
        }

        return response()->json(['error' => false, 'message' => 'Surplus data updated successfully']);
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrigin(Request $request)
    {
        if (!empty($request->id)) {
            $our_surplus           = OurSurplus::find($request->id);
            $our_surplus['origin'] = $request->origin;
            $our_surplus->save();
        }

        return response()->json(['error' => false, 'message' => 'Surplus data updated successfully']);
    }

    public function getRegionSpecies(Request $request)
    {
        $specie = OurSurplus::find($request->id);
        if (!empty($specie) && !empty($specie->region_id)) {
            return response()->json(['error' => false, 'region' => $specie->region_id, 'text' => $specie->region->name]);
        } else {
            return response()->json(['error' => true]);
        }
    }

    //Upload picture
    public function upload_picture(Request $request)
    {
        $request->validate(
            [
            'file' => "image|mimes:jpeg,jpg,bmp,png",
            'file' => 'dimensions:max_width=600,max_height=600'
            ]
        );

        $oursurplur = OurSurplus::findOrFail($request->ourSurplusId);

        if($request->hasFile('file')) {
            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            $pattern = "/[ '-]/i";
            $request->validate(
                [
                'file_name' => (preg_match($pattern, $file_name)) ? 'required' : ''
                ], [
                'file_name.required'=> 'Image name must not contain spaces or strange characters.'
                ]
            );

            Storage::delete('public/oursurplus_pictures/'.$request->ourSurplusId."/".$oursurplur->catalog_pic);
            $oursurplur->update(['catalog_pic' => $file_name]);

            $path = Storage::putFileAs(
                'public/oursurplus_pictures/'.$request->ourSurplusId, $file, $file_name
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete surplus file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file_catalog($oursurplus_id, $file_name)
    {
        Storage::delete('public/oursurplus_pictures/'.$oursurplus_id.'/'.$file_name);

        return redirect(route('our-surplus.show', [$oursurplus_id]));
    }
}
