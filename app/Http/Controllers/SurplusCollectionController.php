<?php

namespace App\Http\Controllers;

use App\Enums\AgeGroup;
use App\Enums\ConfirmOptions;
use App\Enums\Currency;
use App\Enums\OrganisationLevel;
use App\Enums\Size;
use App\Enums\SurplusOrderByOptions;
use App\Enums\SurplusStatus;
use App\Exports\SurplusCollectionAddressListExport;
use App\Http\Requests\SurplusCollectionCreateRequest;
use App\Http\Requests\SurplusCollectionUpdateRequest;
use App\Mail\SendGeneralEmail;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Association;
use App\Models\Classification;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Organisation;
use App\Models\Origin;
use App\Models\OurSurplus;
use App\Models\Region;
use App\Models\Surplus;
use App\Models\SurplusList;
use App\Models\User;
use App\Models\Wanted;
use Carbon\Carbon;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SurplusCollectionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::where('id', Auth::id())->first();

        $countries = Country::orderBy('name')->pluck('name', 'id');
        $areas     = AreaRegion::pluck('name', 'id');

        $currencies      = Currency::get();
        $surplus_status  = SurplusStatus::get();
        $origin          = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup        = AgeGroup::get();
        $sizes           = Size::get();
        $levels          = OrganisationLevel::get();
        $confirm_options = ConfirmOptions::get();
        $associations    = Association::orderBy('key')->get();
        $regions         = Region::orderBy('name')->pluck('name', 'id');

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $orderByOptions   = SurplusOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('surplus_collection.filter')) {
            $request = session('surplus_collection.filter');
        }

        if (isset($request['filter_show_surplus'])) {
            if($request['filter_show_surplus'] == "yes"){
                $surpluses = Surplus::with(['animal'])->orderByDesc('updated_at');
                $filterData = Arr::add($filterData, 'filter_show_surplus', 'Show Surplus: ' . $request['filter_show_surplus']);
            }else{
                $surpluses = Surplus::with(['animal'])->where('surplus_status', 'collection')->orderByDesc('updated_at');
                $filterData = Arr::add($filterData, 'filter_show_surplus', 'Show Surplus: ' . $request['filter_show_surplus']);
            }
        }else{
            $surpluses = Surplus::with(['animal'])->where('surplus_status', 'collection')->orderByDesc('updated_at');
        }

        //DB::enableQueryLog();
        $getFilter = $this->getDataFilter($request, $filterData, $surpluses);

        $surpluses  = $getFilter['surpluses'];
        $filterData = $getFilter['filterData'];

        $surpluses = $surpluses->get();
        //dump(DB::getQueryLog());

        if (isset($request)) {
            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByField == 'common_name' || $orderByField == 'scientific_name' || $orderByField == 'code_number') {
                    if ($orderByDirection == 'desc') {
                        $surpluses = $surpluses->sortByDesc(function ($surplus, $key) use ($orderByField) {
                            return $surplus->animal->$orderByField;
                        });
                    } else {
                        $surpluses = $surpluses->sortBy(function ($surplus, $key) use ($orderByField) {
                            return $surplus->animal->$orderByField;
                        });
                    }
                } else {
                    if ($orderByDirection == 'desc') {
                        $surpluses = $surpluses->sortByDesc(function ($surplus, $key) use ($orderByField) {
                            return $surplus->$orderByField;
                        });
                    } else {
                        $surpluses = $surpluses->sortBy(function ($surplus, $key) use ($orderByField) {
                            return $surplus->$orderByField;
                        });
                    }
                }
            }
        }

        $array_object_results = [];
        foreach ($surpluses as $surplus) {
            array_push($array_object_results, $surplus);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        if (isset($request) && isset($request['recordsPerPage'])) {
            $perPage = $request['recordsPerPage'];
        } else {
            $perPage = 50;
        }

        $currentItems = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);

        $surpluses = new LengthAwarePaginator($currentItems, count($array_object_results), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        //dd($surpluses);

        foreach ($surpluses as $surplus) {
            $surplus->docs = Storage::allFiles('public/surpluses_docs/' . $surplus->id);
        }

        return view('surplus_collections.index', compact(
            'surpluses',
            'surplus_status',
            'countries',
            'areas',
            'origin',
            'ageGroup',
            'sizes',
            'currencies',
            'levels',
            'confirm_options',
            'classes',
            'orderByOptions',
            'orderByDirection',
            'orderByField',
            'filterData',
            'associations',
            'regions'
        ));
    }

    public function createSurplusCollectionAddressList(Request $request)
    {
        $file_name = 'Surplus Collection address list ' . Carbon::now()->format('Y-m-d') . '.csv';

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('surplus_collection.filter')) {
            $request = session('surplus_collection.filter');
        }

        $surpluses = Surplus::with(['animal'])->where('surplus_status', 'collection')->orderByDesc('updated_at');

        $getFilter = $this->getDataFilter($request, $filterData, $surpluses);

        $surpluses = $getFilter['surpluses'];

        $surpluses = $surpluses->get();

        $export = new SurplusCollectionAddressListExport($surpluses);

        return Excel::download($export, $file_name);
    }

    public function getDataFilter($request, $filterData, $surpluses)
    {
        if (isset($request)) {
            if (isset($request['filter_animal_option'])) {
                if ($request['filter_animal_option'] === 'by_id') {
                    if (isset($request['filter_animal_id'])) {
                        $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                        $surpluses->where('animal_id', $filterAnimal->id);

                        $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
                    }
                } elseif ($request['filter_animal_option'] === 'by_name') {
                    if (isset($request['filter_animal_name'])) {
                        $surpluses->whereHas('animal', function ($query) use ($request) {
                            $query->where('common_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('common_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('scientific_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('scientific_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('spanish_name', 'like', '%' . $request['filter_animal_name'] . '%');
                        });

                        $filterData = Arr::add($filterData, 'filter_animal_name', 'Animal name: ' . $request['filter_animal_name']);
                    }
                } else {
                    $surpluses->whereNull('animal_id');

                    $filterData = Arr::add($filterData, 'filter_animal_option', 'Animal: empty');
                }
            }

            if (isset($request['empty_institution'])) {
                $surpluses->whereNull('organisation_id');

                $filterData = Arr::add($filterData, 'empty_institution', 'Institution: empty');
            } elseif (isset($request['filter_institution_id'])) {
                $institutionFilter = Organisation::where('id', $request['filter_institution_id'])->first();

                $surpluses->where('organisation_id', $institutionFilter->id);

                $filterData = Arr::add($filterData, 'filter_institution_id', 'Institution: ' . $institutionFilter->name);
            }

            if (isset($request['empty_contact'])) {
                $surpluses->whereNull('contact_id');

                $filterData = Arr::add($filterData, 'empty_contact', 'Contact: empty');
            } elseif (isset($request['filter_supplier_id'])) {
                $contactFilter = Contact::where('id', $request['filter_supplier_id'])->first();

                $surpluses->where('contact_id', $contactFilter->id);

                $filterData = Arr::add($filterData, 'filter_supplier_id', 'Contact: ' . $contactFilter->full_name);
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
                    $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                } elseif ($order != null) {
                    $families = $order->under->all();
                    foreach ($families as $order_family) {
                        $order_family_genera = $order_family->under->toArray();
                        foreach ($order_family_genera as $family_genus) {
                            array_push($ids_genera, $family_genus['id']);
                        }
                    }
                    $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
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
                    $surpluses->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                }
            }

            if (isset($request['filter_origin'])) {
                $surpluses->where('origin', $request['filter_origin']);

                $filterData = Arr::add($filterData, 'filter_origin', 'Origin: ' . $request['filter_origin']);
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();

                $surpluses->where('country_id', $filterCountry->id);

                $filterData = Arr::add($filterData, 'filter_country', 'Country: ' . $filterCountry->name);
            }

            if (isset($request['filter_continent'])) {
                $filterRegion = Region::where('id', $request['filter_continent'])->first();

                $surpluses->whereHas('country', function ($query) use ($filterRegion) {
                    $query->where('region_id', $filterRegion->id);
                });

                $filterData = Arr::add($filterData, 'filter_continent', 'Continent: ' . $filterRegion->name);
            }

            if (isset($request['filter_area'])) {
                $filterArea = AreaRegion::where('id', $request['filter_area'])->first();

                $surpluses->where('area_region_id', $filterArea->id);

                $filterData = Arr::add($filterData, 'filter_area', 'Area: ' . $filterArea->name);
            }

            if (isset($request['filter_remarks'])) {
                $surpluses->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_remarks', 'Remarks: ' . $request['filter_remarks']);
            }

            if (isset($request['filter_intern_remarks'])) {
                $surpluses->where('intern_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_intern_remarks', 'Intern remarks: ' . $request['filter_intern_remarks']);
            }

            if (isset($request['filter_updated_at_from'])) {
                $surpluses->whereDate('updated_at', '>=', $request['filter_updated_at_from']);

                $filterData = Arr::add($filterData, 'filter_updated_at_from', 'Updated start at: ' . $request['filter_updated_at_from']);
            }

            if (isset($request['filter_updated_at_to'])) {
                $surpluses->whereDate('updated_at', '<=', $request['filter_updated_at_to']);

                $filterData = Arr::add($filterData, 'filter_updated_at_to', 'Updated end at: ' . $request['filter_updated_at_to']);
            }

            if (isset($request['filter_institution_level'])) {
                if ($request['filter_institution_level'] === 'empty') {
                    $surpluses->whereHas(
                        'organisation', function ($query) use ($request) {
                            $query->whereNull('level');
                        }
                    );
                } else {
                    $surpluses->whereHas(
                        'organisation', function ($query) use ($request) {
                            $query->where('level', $request['filter_institution_level']);
                        }
                    );
                }

                $filterData = Arr::add($filterData, 'filter_institution_level', 'Level: ' . $request['filter_institution_level']);
            }

            if (isset($request['filter_imagen_species'])) {
                if ($request['filter_imagen_species'] === "yes") {
                    $surpluses->whereHas(
                        'animal', function ($query) {
                            $query->whereNotNull('catalog_pic')->orWhere('catalog_pic', '!=', '');
                        }
                    );
                    $surpluses->where("origin", "!=", "stuffed");
                    $noImages = array();
                    foreach ($surpluses->get() as $row) {
                        if (!empty($row["animal"]) && !empty($row["animal"]->imagen_first)) {
                            array_push($noImages, $row["id"]);
                        }
                    }
                    if (!empty($noImages)) {
                        $surpluses->orwhereIn('id', $noImages);
                    }
                } else {
                    $surpluses->whereHas(
                        'animal', function ($query) {
                            $query->whereNull('catalog_pic')->orWhere('catalog_pic', '');
                        }
                    );
                    $noImages = [];
                    foreach ($surpluses->get() as $row) {
                        if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                            array_push($noImages, $row['id']);
                        }
                    }
                    if (!empty($noImages)) {
                        $surpluses->whereNotIn('id', $noImages);
                    }
                }

                $filterData = Arr::add($filterData, 'filter_imagen_species', 'General images: ' . $request['filter_imagen_species']);
            }

            $associations = Association::orderBy('key')->get();

            foreach ($associations as $Row) {
                if (isset($request['filter_has_' . $Row->key]) && $request['filter_has_' . $Row->key] != 'all') {
                    $filterData = Arr::add($filterData, 'filter_has_' . $Row->key, 'Has ' . $Row->key . ': ' . $request['filter_has_' . $Row->key]);

                    if ($request['filter_has_' . $Row->key] == 'yes') {
                        $surpluses->whereHas('organisation', function ($query) use ($request, $Row) {
                            $query->whereHas('associations', function ($associations) use ($request, $Row) {
                                $associations->where('key', $Row->key);
                            });
                        });
                    } else {
                        $surpluses->whereHas('organisation', function ($query) use ($request, $Row) {
                            $query->whereHas('associations', function ($associations) use ($request, $Row) {
                                $associations->where('key', '!=', $Row->key);
                            })->orWhereDoesntHave('associations');
                        });
                    }
                }
            }
        }

        return ['surpluses' => $surpluses, 'filterData' => $filterData];
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('surplus_collection.filter');

        return redirect(route('surplus-collection.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $areas     = AreaRegion::orderBy('name')->pluck('name', 'id');

        $currencies = Currency::get();
        $origin     = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup   = AgeGroup::get();
        $sizes      = Size::get();

        return view('surplus_collections.create', compact(
            'countries',
            'areas',
            'origin',
            'ageGroup',
            'sizes',
            'currencies'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\SurplusCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SurplusCollectionCreateRequest $request)
    {
        $user = User::where('id', Auth::id())->first();

        $organization   = Organisation::where('id', $request->organisation_id)->first();
        $origin         = $request->origin;
        $area_region_id = ($organization->country) ? $organization->country->region->area_region->id : null;

        $request['quantityM']      = -1;
        $request['quantityF']      = -1;
        $request['quantityU']      = -1;
        $request['cost_currency']  = 'EUR';
        $request['costPriceM']     = 0;
        $request['costPriceF']     = 0;
        $request['costPriceU']     = 0;
        $request['costPriceP']     = 0;
        $request['sale_currency']  = 'EUR';
        $request['salePriceM']     = 0;
        $request['salePriceF']     = 0;
        $request['salePriceU']     = 0;
        $request['salePriceP']     = 0;
        $request['country_id']     = $organization->country_id;
        $request['area_region_id'] = $area_region_id;

        $validator = Validator::make($request->toArray(), []);

        $surplusCollectionAlreadyExist = Surplus::where('animal_id', $request->animal_id)
            ->where('organisation_id', $organization->id)
            ->where('surplus_status', 'collection')
            ->when($origin, function ($query, $origin) {
                return $query->where('origin', $origin);
            })
            ->first();

        if ($surplusCollectionAlreadyExist != null) {
            $validator->errors()->add('already_exist', 'A surplus collection already exist with the same species, provider, and origin.');

            return redirect(route('surplus-collection.create'))->withInput($request->toArray())->withErrors($validator);
        } else {
            $request['inserted_by']    = Auth::id();
            $request['surplus_status'] = 'collection';

            Surplus::create($request->all());

            return redirect(route('surplus-collection.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $surplus = Surplus::findOrFail($id);

        $surplus->docs = Storage::allFiles('public/surpluses_docs/' . $surplus->id);

        $animalRelatedSurplus = $surplus->animal->surpluses()->where('surplus_status', 'collection')->paginate(10);

        return view('surplus_collections.show', compact('surplus', 'animalRelatedSurplus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $surplus = Surplus::find($id);

        $surplus['supplier'] = ($surplus->contact != null) ? $surplus->contact->email : 'No contact selected.';
        $surplus['animal']   = ($surplus->animal  != null) ? $surplus->animal->common_name . ' (' . $surplus->animal->scientific_name . ')' : 'No animal selected.';

        $countries = Country::orderBy('name')->pluck('name', 'id');
        $areas     = AreaRegion::orderBy('name')->pluck('name', 'id');

        $currencies     = Currency::get();
        $surplus_status = SurplusStatus::get();
        $origin         = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup       = AgeGroup::get();
        $sizes          = Size::get();

        return view('surplus_collections.edit', compact(
            'surplus',
            'countries',
            'areas',
            'surplus_status',
            'origin',
            'ageGroup',
            'sizes',
            'currencies'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\SurplusUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SurplusCollectionUpdateRequest $request, $id)
    {
        $surplusUpdate = Surplus::findOrFail($id);

        $organization = Organisation::where('id', $request->organisation_id)->first();

        if ($surplusUpdate->organisation_id != $organization->id) {
            $area_region_id = ($organization->country) ? $organization->country->region->area_region->id : null;

            $request['country_id']     = $organization->country_id;
            $request['area_region_id'] = $area_region_id;
        }

        $surplusUpdate->update($request->all());

        return redirect(route('surplus-collection.show', $surplusUpdate->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $surplusDelete = Surplus::findOrFail($id);
        Storage::deleteDirectory('public/surpluses_docs/' . $surplusDelete->id);
        $surplusDelete->delete();

        return redirect(route('surplus-collection.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param Request $Request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $surplusDelete = Surplus::findOrFail($id);
                Storage::deleteDirectory('public/surpluses_docs/' . $surplusDelete->id);
                $surplusDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editSelectedRecords(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $surplus = Surplus::findOrFail($id);

                if (isset($request->institution_id)) {
                    $surplus->update(['organisation_id' => $request->institution_id]);
                }

                if (isset($request->origin)) {
                    $surplus->update(['origin' => $request->origin]);
                }

                if (isset($request->age_group)) {
                    $surplus->update(['age_group' => $request->age_group]);
                }

                if (isset($request->supplier_level)) {
                    $surplus->organisation()->update(['level' => $request->supplier_level]);
                }

                if (isset($request->surplus_status)) {
                    $surplus->update(['surplus_status' => $request->surplus_status]);
                }
            }
        }

        return response()->json();
    }

    /**
     * Filter surplus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterSurplus(Request $request)
    {
        // Check if filter is set on session
        if (session()->has('surplus_collection.filter')) {
            $query = session('surplus_collection.filter');
            $query = Arr::collapse([$query, $request->query()]);
            session(['surplus_collection.filter' => $query]);
        } else { // Set session surplus filter
            session(['surplus_collection.filter' => $request->query()]);
        }

        return redirect(route('surplus-collection.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('surplus_collection.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['surplus_collection.filter' => $query]);

        return redirect(route('surplus-collection.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('surplus_collection.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['surplus_collection.filter' => $query]);

        return redirect(route('surplus-collection.index'));
    }

    /**
     * Remove from surplus session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromSurplusSession($key)
    {
        $query = session('surplus_collection.filter');
        Arr::forget($query, $key);
        session(['surplus_collection.filter' => $query]);

        return redirect(route('surplus-collection.index'));
    }

    /**
     * Get email option info.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function askMoreSurplusDetails($id)
    {
        $surplus = Surplus::findOrFail($id);

        if ($surplus->animal != null) {
            if ($surplus->organisation != null) {
                $email_from = 'info@zoo-services.com';

                $email_to = '';
                foreach ($surplus->organisation->contacts as $contact) {
                    if ($contact->email != null) {
                        $email_to .= $contact->email . ', ';
                    }
                }

                $email_subject = 'About your surplus specimens.';
                $email_body    = view('emails.surplus-more-details', compact('surplus'))->render();

                return view('surplus_collections.email_asking_surplus_details', compact('surplus', 'email_from', 'email_to', 'email_subject', 'email_body'));
            } else {
                return redirect()->back()->with('error_msg', 'This surplus record has not contact assigned.');
            }
        } else {
            return redirect()->back()->with('error_msg', 'This surplus record has not animal assigned.');
        }
    }

    /**
     * Send email option.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendSurplusEmail(Request $request)
    {
        $surplus = Surplus::findOrFail($request->id_surplus);

        $email_to = array_map('trim', explode(',', $request->email_to));

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        foreach ($email_to as $email) {
            $email = trim($email);
            if ($email != '') {
                $contact = Contact::GetContacts()->where('email', $email)->first();

                $email_body = Str::of($request->email_body)->replace('contact_name', $contact->letter_name);

                Mail::to($email)->cc($email_cc_array)->queue(new SendGeneralEmail($request->email_from, $request->email_subject, $request->email_body));
            }
        }
        //Mail::to('johnrens@zoo-services.com')->cc(['development@zoo-services.com','rossmery@zoo-services.com'])->queue(new SendGeneralEmail($request->email_from, $request->email_subject, $request->email_body));

        return redirect(route('surplus-collection.show', [$surplus->id]))->with('success', 'Email successfully sent.');
    }

    /**
     * Delete surplus file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file($surplus_id, $file_name)
    {
        Storage::delete('public/surpluses_docs/' . $surplus_id . '/' . $file_name);

        return redirect(route('surplus-collection.show', [$surplus_id]));
    }

    /**
     * Generate pdf or html surplus list.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printSurplusList(Request $request)
    {
        $surplusToPrint = Surplus::whereIn('id', $request->items)->get()->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);

        if ($request->export_option !== 'all') {
            $surplusToPrint = Surplus::whereIn('id', $request->items)->get();
        } else {
            $surplusFilter = session('surplus.filter');

            if (isset($surplusFilter['surplusListsTopSelect']) && $surplusFilter['surplusListsTopSelect'] > 0) {
                $surplusListSelected = SurplusList::find($surplusFilter['surplusListsTopSelect']);

                $surplusToPrint = $surplusListSelected->surpluses;
            } else {
                return response()->json(['success' => false, 'message' => 'Print all surpluses is only possible for a selected surplus list.']);
            }
        }

        $surplusToPrint = $surplusToPrint->sortBy(function ($surplus, $key) {
            return [$surplus->animal->code_number, $surplus->animal->classification->order->common_name, $surplus->animal->scientific_name];
        });
        $surplusToPrint = $surplusToPrint->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);

        $document = $request->document_type;
        $language = $request->language;

        $date      = Carbon::now()->format('Y-m-d');
        $name      = ($language == 'english') ? 'Surplus list ' . $date : 'Lista de excedentes ' . $date;
        $extension = '.' . $document;
        $fileName  = $name . $extension;

        $header_tittle = ($language == 'english') ? 'Surplus list' : 'Lista de excedentes';
        $date          = Carbon::now()->format('F j, Y');

        $templateName = 'surplus_template_no_prices';

        if (isset($request->pictures)) {
            if ($request->pictures == 'yes') {
                $templateName .= '_pictures';
            } else {
                $templateName .= '_no_pictures';
            }
        }

        $content = view('pdf_documents.' . $templateName, compact('header_tittle', 'date', 'document', 'language', 'surplusToPrint'))->render();
        $html    = ($request->document_type == 'pdf') ? str_replace('http://127.0.0.1:8000', base_path() . '/public', $content) : $content;

        $document = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

        Storage::put('public/surplus_wanted_lists/' . $fileName, ($request->document_type == 'pdf') ? $document->output() : $html);
        $url = Storage::url('surplus_wanted_lists/' . $fileName);

        return response()->json(['success' => true, 'url' => $url, 'fileName' => $fileName]);
    }
}
