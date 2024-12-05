<?php

namespace App\Http\Controllers;

use App\Enums\AgeGroup;
use App\Enums\ConfirmOptions;
use App\Enums\LookingFor;
use App\Enums\WantedOrderByOptions;
use App\Enums\OrganisationLevel;
use App\Models\Region;
use App\Exports\ContactsAddressListExport;
use App\Exports\WantedsExport;
use App\Http\Requests\WantedCreateRequest;
use App\Http\Requests\WantedUpdateRequest;
use App\Jobs\SendMailQueueEmail;
use App\Mail\SendGeneralEmail;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Classification;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Email;
use App\Models\Labels;
use App\Models\Offer;
use App\Models\Organisation;
use App\Models\Origin;
use App\Models\OurWanted;
use App\Models\SearchMailing;
use App\Models\Surplus;
use App\Models\User;
use App\Models\Wanted;
use App\Models\WantedList;
use Carbon\Carbon;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\GraphService;

class WantedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', Auth::id())->first();

        $wanteds = Wanted::with(['animal'])->orderByDesc('updated_at');

        $origin           = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup         = AgeGroup::get();
        $lookingFor       = LookingFor::get();
        $confirm_options  = ConfirmOptions::get();
        $levels           = OrganisationLevel::get();
        $countries        = Country::orderBy('name')->pluck('name', 'id');
        $areas            = AreaRegion::pluck('name', 'id');
        $regions          = Region::orderBy('name')->pluck('name', 'id');

        $orderByOptions   = WantedOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $wantedLists = WantedList::get();

        $orderByOptions = wantedOrderByOptions::get();

        $result_array          = $this->get_records_by_filter();
        $filterData            = $result_array['filterData'];
        $wanteds               = $result_array['wanteds'];
        $orderByDirection      = $result_array['orderByDirection'];
        $orderByField          = $result_array['orderByField'];
        $wantedListSelected    = $result_array['wantedListSelected'];
        $filter_imagen_species = $result_array["filter_imagen_species"];

        $array_object_results = [];
        foreach ($wanteds as $wanted) {
            array_push($array_object_results, $wanted);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = $result_array['recordsPerPage'];

        $currentItems = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);

        $wanteds = new LengthAwarePaginator($currentItems, count($array_object_results), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        //dd($wanteds);

        return view(
            'wanted.index', compact(
                'wanteds',
                'origin',
                'ageGroup',
                'lookingFor',
                'classes',
                'confirm_options',
                'orderByOptions',
                'orderByDirection',
                'orderByField',
                'filterData',
                'wantedLists',
                'wantedListSelected',
                "levels",
                "countries",
                "areas",
                "regions"
            )
        );
    }

    /**
     * Filter Wanted records
     *
     * @return array
     */
    public function get_records_by_filter()
    {
        $orderByDirection      = null;
        $orderByField          = null;
        $wantedListSelected    = null;
        $recordsPerPage        = 50;
        $filterData            = [];

        // Check if filter is set on session
        if (session()->has('wanted.filter')) {
            $request = session('wanted.filter');
        }

        $wantedListSelected = null;
        if (isset($request['wantedListsTopSelect']) && $request['wantedListsTopSelect'] > 0) {
            $wantedListSelected = WantedList::find($request['wantedListsTopSelect']);

            $wanteds = $wantedListSelected->wanteds()->orderByDesc('updated_at');

            $filterData = Arr::add($filterData, 'wantedListsTopSelect', 'Wanted list: ' . $wantedListSelected->name);
        } else {
            $wanteds = Wanted::orderByDesc('updated_at');
        }

        if (isset($request)) {
            if (isset($request['recordsPerPage'])) {
                $recordsPerPage = $request['recordsPerPage'];
            }
            if (isset($request['filter_animal_option'])) {
                if ($request['filter_animal_option'] === 'by_id') {
                    if (isset($request['filter_animal_id'])) {
                        $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                        $wanteds->where('animal_id', $filterAnimal->id);

                        $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
                    }
                } elseif ($request['filter_animal_option'] === 'by_name') {
                    if (isset($request['filter_animal_name'])) {
                        $wanteds->whereHas(
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
                    $wanteds->whereNull('animal_id');

                    $filterData = Arr::add($filterData, 'filter_animal_option', 'Animal: empty');
                }
            }

            if (isset($request['empty_institution'])) {
                $wanteds->whereNull('organisation_id');

                $filterData = Arr::add($filterData, 'empty_institution', 'Institution: empty');
            } elseif (isset($request['filter_institution_id'])) {
                $institutionFilter = Organisation::where('id', $request['filter_institution_id'])->first();

                $wanteds->where('organisation_id', $institutionFilter->id);

                $filterData = Arr::add($filterData, 'filter_institution_id', 'Institution: ' . $institutionFilter->name);
            }

            if (isset($request['empty_client'])) {
                $wanteds->whereNull('contact_id');

                $filterData = Arr::add($filterData, 'empty_client', 'Client: empty');
            } elseif (isset($request['filter_client_id'])) {
                $contactFilter = Contact::where('id', $request['filter_client_id'])->first();

                $wanteds->where('client_id', $request['filter_client_id']);

                $filterData = Arr::add($filterData, 'filter_client_id', 'Client: ' . $contactFilter->full_name);
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
                    $wanteds->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $wanteds->whereHas(
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
                    $wanteds->whereHas(
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
                    $wanteds->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                }
            }

            if (isset($request['filter_have_standard_wanted'])) {
                $ourWanteds = OurWanted::pluck('animal_id');
                if ($request['filter_have_standard_wanted'] == 'yes') {
                    $wanteds->whereIn('animal_id', $ourWanteds);
                } else {
                    $wanteds->whereNotIn('animal_id', $ourWanteds);
                }

                $filterData = Arr::add($filterData, 'filter_have_standard_wanted', 'In our wanted: ' . $request['filter_have_standard_wanted']);
            }

            if (isset($request['filter_remarks'])) {
                $wanteds->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_remarks', 'Remarks: ' . $request['filter_remarks']);
            }

            if (isset($request['filter_intern_remarks'])) {
                $wanteds->where('intern_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_intern_remarks', 'Intern remarks: ' . $request['filter_intern_remarks']);
            }

            if (isset($request['filter_updated_at_from'])) {
                $wanteds->whereDate('updated_at', '>=', $request['filter_updated_at_from']);

                $filterData = Arr::add($filterData, 'filter_updated_at_from', 'Updated start at: ' . $request['filter_updated_at_from']);
            }

            if (isset($request['filter_updated_at_to'])) {
                $wanteds->whereDate('updated_at', '<=', $request['filter_updated_at_to']);

                $filterData = Arr::add($filterData, 'filter_updated_at_to', 'Updated end at: ' . $request['filter_updated_at_to']);
            }

            if (isset($request['filter_imagen_species'])) {
                if ($request['filter_imagen_species'] === 'yes') {
                    $wanteds->whereHas(
                        'animal', function ($query) {
                            $query->whereNotNull('catalog_pic')->orWhere('catalog_pic', '!=', '');
                        }
                    );
                    $noImages = [];
                    foreach ($wanteds->get() as $row) {
                        if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                            array_push($noImages, $row['id']);
                        }
                    }
                    if (!empty($noImages)) {
                        $wanteds->orwhereIn('id', $noImages);
                    }
                } else {
                    $wanteds->whereHas(
                        'animal', function ($query) {
                            $query->whereNull('catalog_pic')->orWhere('catalog_pic', '');
                        }
                    );
                    $noImages = [];
                    foreach ($wanteds->get() as $row) {
                        if (!empty($row['animal']) && !empty($row['animal']->imagen_first)) {
                            array_push($noImages, $row['id']);
                        }
                    }
                    if (!empty($noImages)) {
                        $wanteds->whereNotIn('id', $noImages);
                    }
                }

                $filterData = Arr::add($filterData, 'filter_imagen_species', 'Imagen Species: ' . $request['filter_imagen_species']);
            }

            if (isset($request['filter_institution_level'])) {
                if ($request['filter_institution_level'] === 'empty') {
                    $wanteds->whereHas(
                        'organisation', function ($query) use ($request) {
                            $query->whereNull('level');
                        }
                    );
                } else {
                    $wanteds->whereHas(
                        'organisation', function ($query) use ($request) {
                            $query->where('level', $request['filter_institution_level']);
                        }
                    );
                }

                $filterData = Arr::add($filterData, 'filter_institution_level', 'Level: ' . $request['filter_institution_level']);
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();

                $wanteds->whereHas(
                    'organisation', function ($query) use ($request, $filterCountry) {
                        $query->where('country_id', $filterCountry->id);
                    }
                );

                $filterData = Arr::add($filterData, 'filter_country', 'Country: ' . $filterCountry->name);
            }

            if (isset($request['filter_continent'])) {
                $filterRegion = Region::where('id', $request['filter_continent'])->first();

                $wanteds->whereHas(
                    'organisation', function ($query) use ($request, $filterRegion) {
                        $query->whereHas(
                            'country', function ($query_region) use ($filterRegion) {
                                $query_region->where('region_id', $filterRegion->id);
                            }
                        );
                    }
                );

                $filterData = Arr::add($filterData, 'filter_continent', 'Continent: ' . $filterRegion->name);
            }

            if (isset($request['filter_area'])) {
                $filterArea = AreaRegion::where('id', $request['filter_area'])->first();

                $wanteds->whereHas(
                    'organisation', function ($query) use ($request, $filterArea) {
                        $query->whereHas(
                            'country', function ($query_region) use ($filterArea) {
                                $query_region->whereHas(
                                    'region', function ($query_area) use ($filterArea) {
                                        $query_area->where('area_region_id', $filterArea->id);
                                    }
                                );
                            }
                        );
                    }
                );

                $filterData = Arr::add($filterData, 'filter_area', 'Area: ' . $filterArea->name);
            }

        }

        $wanteds = $wanteds->get();

        if (isset($request)) {
            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByField == 'common_name' || $orderByField == 'scientific_name' || $orderByField == 'code_number') {
                    if ($orderByDirection == 'desc') {
                        $wanteds = $wanteds->sortByDesc(
                            function ($wanted, $key) use ($orderByField) {
                                return $wanted->animal->$orderByField;
                            }
                        );
                    } else {
                        $wanteds = $wanteds->sortBy(
                            function ($wanted, $key) use ($orderByField) {
                                return $wanted->animal->$orderByField;
                            }
                        );
                    }
                } else {
                    if ($orderByDirection == 'desc') {
                        $wanteds = $wanteds->sortByDesc(
                            function ($wanted, $key) use ($orderByField) {
                                return $wanted->$orderByField;
                            }
                        );
                    } else {
                        $wanteds = $wanteds->sortBy(
                            function ($wanted, $key) use ($orderByField) {
                                return $wanted->$orderByField;
                            }
                        );
                    }
                }
            }
        }

        return [
          'filterData'            => $filterData,
          'wanteds'               => $wanteds,
          'orderByDirection'      => $orderByDirection,
          'orderByField'          => $orderByField,
          'wantedListSelected'    => $wantedListSelected,
          'recordsPerPage'        => $recordsPerPage,
          'filter_imagen_species' => $request['filter_imagen_species'] ?? '',
        ];
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        session()->forget('wanted.filter');

        return redirect(route('wanted.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $origin     = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        return view('wanted.create', compact('origin', 'ageGroup', 'lookingFor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\WantedCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WantedCreateRequest $request)
    {
        $validator = Validator::make($request->toArray(), []);

        $wantedAlreadyExist = Wanted::where('animal_id', $request->animal_id)
            ->where('organisation_id', $request->organisation_id)
            ->first();

        if ($wantedAlreadyExist != null) {
            $validator->errors()->add('already_exist', 'Please note the species is already inserted, please adjust quantities and sexes if necessary.');

            return redirect(route('wanted.create'))->withInput($request->toArray())->withErrors($validator);
        } else {
            $ourWanted = OurWanted::where('animal_id', $request->animal_id)->first();

            if (is_null($ourWanted)) {
                OurWanted::create($request->all());
            }

            $request['inserted_by'] = Auth::id();
            $request['new_wanted']  = 1;
            $wanted = Wanted::create($request->all());
            $wanted->wanted_lists()->sync($request->wantedLists);

            return redirect(route('wanted.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $wanted = Wanted::findOrFail($id);

        $user = Auth::user();
        if (!empty($user->id) && $user->id === 2) {
            $wanted               = Wanted::find($id);
            $wanted['new_wanted'] = 0;
            $wanted->save();
        }

        $areas      = AreaRegion::orderBy('name')->pluck('name', 'id');
        $to_country = Country::orderBy('name')->pluck('name', 'id');

        $animalRelatedWanted = $wanted->animal->wanteds()->paginate(10);
        $emails_received     = Email::where('wanted_id', $id)->where('is_send', 0)->orderBy('created_at', 'DESC')->paginate(10);
        $emails              = Email::where('wanted_id', $id)->where('is_send', 1)->orderBy('created_at', 'DESC')->paginate(10);

        return view('wanted.show', compact('wanted', 'areas', 'to_country', 'animalRelatedWanted', 'emails_received', 'emails'));
    }

    /**
     * Show the form for editing.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $wanted                 = Wanted::find($id);
        $wanted['client']       = ($wanted->client != null) ? $wanted->client->email : 'No contact selected.';
        $wanted['imagen_first'] = $wanted->animal->imagen_first ?? [];
        $wanted['catalog_pic']  = $wanted->animal->catalog_pic  ?? '';
        $wanted['animal']       = ($wanted->animal != null) ? $wanted->animal->common_name . ' (' . $wanted->animal->scientific_name . ')' : 'No animal selected.';

        $origin     = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        $wantedLists         = WantedList::pluck('name', 'id');
        $wantedListsSelected = $wanted->wanted_lists()->pluck('wanted_list_id');

        return view(
            'wanted.edit', compact(
                'wanted',
                'origin',
                'ageGroup',
                'lookingFor',
                'wantedLists',
                'wantedListsSelected',
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\WantedUpdateRequest $request
     * @param  int                                    $id
     * @return \Illuminate\Http\Response
     */
    public function update(WantedUpdateRequest $request, $id)
    {
        $validator = Validator::make($request->toArray(), []);

        $wantedAlreadyExist = Wanted::where('id', '<>', $id)
            ->where('animal_id', $request->animal_id)
            ->where('organisation_id', $request->organisation_id)
            ->first();

        if ($wantedAlreadyExist != null) {
            $validator->errors()->add('already_exist', 'Please note the species is already inserted, please adjust quantities and sexes if necessary.');

            return redirect(route('wanted.edit', [$id]))->withInput($request->toArray())->withErrors($validator);
        } else {
            $updateItem = Wanted::findOrFail($id);

            $updateItem->update($request->all());

            return redirect(route('wanted.show', [$updateItem->id]));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $wantedDelete = Wanted::findOrFail($id);
        $wantedDelete->delete();

        return redirect(route('wanted.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $wantedDelete = Wanted::findOrFail($id);
                $wantedDelete->delete();
            }
        }

        return response()->json();
    }

    /**
     * Filter wanteds.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function filterWanted(Request $request)
    {
        // Set session wanted filter
        session(['wanted.filter' => $request->query()]);

        return redirect(route('wanted.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('wanted.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['wanted.filter' => $query]);

        return redirect(route('wanted.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('wanted.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['wanted.filter' => $query]);

        return redirect(route('wanted.index'));
    }

    /**
     * Remove from wanted session.
     *
     * @param  string $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromWantedSession($key)
    {
        $query = session('wanted.filter');
        Arr::forget($query, $key);
        session(['wanted.filter' => $query]);

        return redirect(route('wanted.index'));
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
                $wanted = Wanted::findOrFail($id);

                if (isset($request->origin)) {
                    $wanted->update(['origin' => $request->origin]);
                }

                if (isset($request->age_group)) {
                    $wanted->update(['age_group' => $request->age_group]);
                }

                if (isset($request->add_to_wanted_lists)) {
                    $wanted->wanted_lists()->syncWithoutDetaching($request->add_to_wanted_lists);
                }

                if (isset($request->remove_from_wanted_lists)) {
                    $wanted->wanted_lists()->detach($request->remove_from_wanted_lists);
                }
            }
        }

        return response()->json();
    }

    /**
     * Generate pdf or html wanted list.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function printWantedList(Request $request)
    {
        $document = $request->document_type;
        $language = $request->language;

        if ($request->export_option !== 'all') {
            $wantedToPrint = Wanted::whereIn('id', $request->items)->get();
        } else {
            $result_array  = $this->get_records_by_filter(true);
            $wantedToPrint = $result_array['wanteds'];

            if ($document == 'pdf' && $wantedToPrint->count() > 300) {
                return response()->json(['success' => false, 'message' => 'You cannot print more than 300 records in PDF. Please, select HTML and convert HTML file to PDF.']);
            }
        }

        $wantedToPrint = $wantedToPrint->sortBy(
            function ($our_wanted, $key) {
                return [$our_wanted->animal->code_number, $our_wanted->animal->classification->order->common_name, $our_wanted->animal->scientific_name];
            }
        );
        $wantedToPrint = $wantedToPrint->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);

        $date      = Carbon::now()->format('Y-m-d');
        $name      = ($language == 'english') ? 'Wanted list ' . $date : 'Lista de buscados ' . $date;
        $extension = '.' . $document;
        $fileName  = $name . $extension;

        $header_tittle = ($language == 'english') ? 'Wanted list' : 'Lista de buscados';
        $date          = Carbon::now()->format('F j, Y');

        $templateName = 'wanted_template';

        if (isset($request->pictures)) {
            if ($request->pictures == 'yes') {
                $templateName .= '_pictures';
            } else {
                $templateName .= '_no_pictures';
            }
        }

        $content = view('pdf_documents.' . $templateName, compact('header_tittle', 'date', 'document', 'language', 'wantedToPrint'))->render();
        $html    = ($request->document_type == 'pdf') ? str_replace('http://127.0.0.1:8000', base_path() . '/public', $content) : $content;

        $document = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

        Storage::put('public/surplus_wanted_lists/' . $fileName, ($request->document_type == 'pdf') ? $document->output() : $html);
        $url = Storage::url('surplus_wanted_lists/' . $fileName);

        return response()->json(['success' => true, 'url' => $url, 'fileName' => $fileName]);
    }

    //Export excel document with wanteds info.
    public function export(Request $request)
    {
        $file_name = 'Wanted list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $wanteds = Wanted::whereIn('id', explode(',', $request->items))->orderBy('created_at')->get();

        $export = new WantedsExport($wanteds);

        return Excel::download($export, $file_name);
    }

    /**
     * Get email option info.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function wantedEmailToSuppliers(Request $request)
    {
        $triggeredFrom = $request->triggeredFrom;
        $idTriggered   = $request->idTriggered;
        $idMailing     = $request->idMailing;

        $animal = Animal::findOrFail($request->idAnimal);
        $wanted = null;
        if ($triggeredFrom === 'wanted') {
            $wanted       = Wanted::findOrFail($idTriggered);
            $number_email = '#WA-' . $wanted->id;
        } else {
            $number_email = '#AN-' . $animal->id;
        }

        $bodyText   = $request->bodyText;
        $area_id    = $request->idArea;
        $country_id = $request->idCountry;

        if ($animal != null) {
            $surplusesWithInstitution = [];
            $surplusesWithOnlyContact = [];
            if ($country_id != null) {
                $surplusesWithInstitution = Surplus::where('animal_id', $animal->id)
                    ->whereHas(
                        'organisation.country', function ($query) use ($country_id) {
                            $query->where('id', $country_id);
                        }
                    )
                    ->get()->groupBy('organisation_id');
            } elseif ($area_id != null) {
                $surplusesWithInstitution = Surplus::where('animal_id', $animal->id)
                    ->whereHas(
                        'organisation.country.region.area_region', function ($query) use ($area_id) {
                            $query->where('id', $area_id);
                        }
                    )
                    ->get()->groupBy('organisation_id');
            } else {
                $surplusesWithInstitution = Surplus::where('animal_id', $animal->id)
                    ->whereNotNull('organisation_id')
                    ->get()->groupBy('organisation_id');

                $surplusesWithOnlyContact = Surplus::where('animal_id', $animal->id)
                    ->whereNull('organisation_id')
                    ->whereNotNull('contact_id')
                    ->get()->groupBy('contact_id');
            }

            if ($bodyText !== 'export_addresses') {
                $email_from    = 'info@zoo-services.com';
                $email_to      = '';
                $email_subject = 'Wanted ' . $animal->common_name . ' (' . $animal->scientific_name . '). ' . $number_email;
                $email_body    = view(($bodyText === 'search_mail') ? 'emails.wanted-email-to-suppliers' : 'emails.direct-project-email-to-suppliers', compact('animal', 'wanted'))->render();

                if (count($surplusesWithInstitution) > 0 || count($surplusesWithOnlyContact) > 0) {
                    foreach ($surplusesWithInstitution as $key => $surplusWithInstitution) {
                        $institution = Organisation::where('id', $key)->first();
                        foreach ($institution->contacts as $contact) {
                            if ($contact->email != null && $contact->mailing === 'All mailings' && !Str::contains($email_to, $contact->email)) {
                                $email_to .= $contact->email . ',';
                            }
                        }
                    }

                    foreach ($surplusesWithOnlyContact as $key => $surplusWithOnlyContact) {
                        $contact = Contact::where('id', $key)->first();
                        if ($contact->email != null && $contact->mailing === 'All mailings' && !Str::contains($email_to, $contact->email)) {
                            $email_to .= $contact->email . ',';
                        }
                    }

                    return view(
                        'wanted.wanted_email_to_suppliers', compact(
                            'triggeredFrom',
                            'idTriggered',
                            'idMailing',
                            'animal',
                            'email_from',
                            'email_to',
                            'email_subject',
                            'email_body'
                        )
                    );
                } else {
                    return redirect()->back()->with('error', 'The species has not surplus. Check selected area and country.');
                }
            } else {
                $file_name = 'Contacts address list ' . Carbon::now()->format('Y-m-d') . '.csv';

                $contactsList = [];

                foreach ($surplusesWithInstitution as $key => $surplusWithInstitution) {
                    $institution = Organisation::where('id', $key)->first();
                    foreach ($institution->contacts as $contact) {
                        if ($contact->email != null && trim($contact->email) != '' && $contact->mailing === 'All mailings') {
                            array_push($contactsList, $contact);
                        }
                    }
                }

                foreach ($surplusesWithOnlyContact as $key => $surplusWithOnlyContact) {
                    $contact = Contact::where('id', $key)->first();
                    if ($contact->email != null && trim($contact->email) != '' && $contact->mailing === 'All mailings') {
                        array_push($contactsList, $contact);
                    }
                }

                $export = new ContactsAddressListExport($contactsList);

                return Excel::download($export, $file_name);
            }
        } else {
            return redirect()->back()->with('error', 'Animal not assigned.');
        }
    }

    /**
     * Send email option.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendWantedEmail(Request $request)
    {
        $email_to_array = array_map('trim', explode(',', $request->email_to));

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        /*$email_body = Str::of($request->email_body)->replace('contact_name', 'IZS');
        Mail::to('izs@zoo-services.com')->send(new SendGeneralEmail($request->email_from, $request->email_subject, $email_body));

        foreach($email_to_array as $email) {
            $email = trim($email);
            if($email != '') {
                $contact = Contact::GetContacts()->where('email', $email)->first();

                $email_body = Str::of($request->email_body)->replace('contact_name', $contact->letter_name);

                Mail::to($email)->send(new SendGeneralEmail($request->email_from, $request->email_subject, $email_body));
            }
        }*/

        /*SendMailQueueEmail::dispatch($request->email_from, $request->email_subject, $request->email_body, $email_to_array, $email_cc_array)
                            ->delay(now()->addSeconds(2));*/

        try{
            SendMailQueueEmail::dispatch($request->email_from, $request->email_subject, $request->email_body, $email_to_array, $email_cc_array, $request->triggered_id, "wanted")->onQueue('search_mail');
        } catch (\Throwable $th) {

        }

        if ($request->triggered_from === 'offers') {
            $newSearchMailing                   = new SearchMailing();
            $newSearchMailing->animal_id        = $request->animal_id;
            $newSearchMailing->date_sent_out    = Carbon::now()->format('Y-m-d H:i:s');
            $newSearchMailing->next_reminder_at = Carbon::now()->addDays(7);
            $newSearchMailing->times_reminded   = 0;
            $newSearchMailing->save();

            $offer = Offer::findOrFail($request->triggered_id);
            $offer->search_mailings()->save($newSearchMailing);
            $offer->refresh();

            return redirect(route('offers.show', [$request->triggered_id]))->with('status', 'Email successfully sent.');
        } elseif ($request->triggered_from === 'wanted') {
            $newSearchMailing                   = new SearchMailing();
            $newSearchMailing->animal_id        = $request->animal_id;
            $newSearchMailing->date_sent_out    = Carbon::now()->format('Y-m-d H:i:s');
            $newSearchMailing->next_reminder_at = Carbon::now()->addDays(7);
            $newSearchMailing->times_reminded   = 0;
            $newSearchMailing->save();

            $wanted = Wanted::findOrFail($request->triggered_id);
            $wanted->search_mailings()->save($newSearchMailing);
            $wanted->refresh();

            return redirect(route('wanted.show', [$request->triggered_id]))->with('succes', 'Email successfully sent.');
        } else {
            $searchMailing = SearchMailing::where('id', $request->search_mailing_id)->first();

            $searchMailing->update(['next_reminder_at' => Carbon::parse($searchMailing->next_reminder_at)->addDays(7), 'times_reminded' => ($searchMailing->times_reminded += 1)]);

            return redirect(route('search-mailings.index'))->with('success', 'Email successfully sent.');
        }
    }

    public function resetListEmailNewWanted()
    {
        $wanted = Wanted::where('new_wanted', 1)->get();
        if (!empty($wanted)) {
            foreach ($wanted as $row) {
                $row['new_wanted'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Wanted';

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function saveSentEmail($email)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        if(!empty($email)) {
            $userToken = $GraphService->getAllUserToken();
            if(!empty($userToken)) {
                foreach ($userToken as $row){
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $email["from_email"]);
                    if(!empty($token)) {
                        $email_attachment = [];
                        if(!empty($email->attachments)) {
                            foreach ($email->attachments as $key => $attachment) {
                                $email_attachment[$key]["name"] = $attachment->name;
                                $email_attachment[$key]["type"] = $attachment->type;
                                $email_attachment[$key]["content"] = file_get_contents(Storage::disk('')->path($attachment->path));
                            }
                        }
                        $email_cc_array = [];
                        if ($email["cc_email"] != null) {
                            $email_cc_array = array_map('trim', explode(',', $email["cc_email"]));
                        }

                        $email_bcc_array = [];
                        if ($email["bcc_email"] != null) {
                            $email_bcc_array = array_map('trim', explode(',', $email["bcc_email"]));
                        }
                        $result = $GraphService->saveSentItems($token,  $user_id->getId(), $email["subject"], $email["body"], $email["to_email"],  $email_cc_array,  $email_bcc_array, $email_attachment);
                        $email["guid"] = $result["id"];
                        $email->save();
                        if(!empty($result)) {
                            $result = $GraphService->updateIsDraftEmailInbox($token,  $user_id->getId(), $result["id"]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Select Wanted list
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function selectWantedList(Request $request)
    {
        session()->forget('wanted.filter');

        session(['wanted.filter' => ['wantedListsTopSelect' => $request->wantedListsTopSelect]]);

        return redirect(route('wanted.index'));
    }

    /**
     * Add wanted list
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function saveWantedList(Request $request)
    {
        WantedList::create($request->all());

        return response()->json(['success' => true]);
    }

    /**
     * Remove wanted list
     *
     * @param  Request $Request
     * @return \Illuminate\Http\Response
     */
    public function deleteWantedList(Request $request)
    {
        $itemDelete = WantedList::findOrFail($request->id);
        if ($itemDelete != null) {
            $itemDelete->delete();
            session()->forget('wanted.filter');
        }

        return response()->json(['success' => true]);
    }
}
