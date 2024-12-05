<?php
// phpcs:ignore
namespace App\Http\Controllers;

use App\Enums\AgeGroup;
use App\Enums\ConfirmOptions;
use App\Enums\Currency;
use App\Enums\OrganisationLevel;
use App\Enums\Size;
use App\Enums\SurplusOrderByOptions;
use App\Enums\SurplusStatus;
use App\Exports\SurplusExport;
use App\Http\Requests\SurplusCreateRequest;
use App\Http\Requests\SurplusUpdateRequest;
use App\Jobs\SendMailQueueEmail;
use App\Mail\SendGeneralEmail;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Classification;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Email;
use App\Models\Labels;
use App\Models\Organisation;
use App\Models\Origin;
use App\Models\OurSurplus;
use App\Models\Surplus;
use App\Models\SurplusList;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CurrencyRate;
use App\Services\GraphService;
use Illuminate\Support\Facades\App;

class SurplusController extends Controller //phpcs:ignore
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = User::where('id', Auth::id())->first();

        $countries = Country::orderBy('name')->pluck('name', 'id');
        $areas     = AreaRegion::pluck('name', 'id');

        $currencies = Currency::get();

        $surplus_status = SurplusStatus::get();
        Arr::forget($surplus_status, 'collection');
        $surplus_status["not_archive"] = "Not Archive";

        $origin              = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup            = AgeGroup::get();
        $sizes               = Size::get();
        $levels              = OrganisationLevel::get();
        $confirm_options     = ConfirmOptions::get();
        $organization_levels = OrganisationLevel::get();

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $surplusLists = SurplusList::get();

        $theWantedLists  = WantedList::pluck('name', 'id');

        $orderByOptions = SurplusOrderByOptions::get();

        $result_array        = $this->get_records_by_filter();
        $filterData          = $result_array['filterData'];
        $surpluses           = $result_array['surpluses'];
        $orderByDirection    = $result_array['orderByDirection'];
        $orderByField        = $result_array['orderByField'];
        $surplusListSelected = $result_array['surplusListSelected'];
        $filter_imagen_species = $result_array["filter_imagen_species"];

        $array_object_results = [];
        if (isset($filterData['filter_same_surplus'])) {
            foreach ($surpluses as $groupByInstitution) {
                foreach ($groupByInstitution as $groupByAnimal) {
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
            }
        } else {
            foreach ($surpluses as $surplus) {
                array_push($array_object_results, $surplus);
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = $result_array['recordsPerPage'];

        $currentItems = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);

        $surpluses = new LengthAwarePaginator($currentItems, count($array_object_results), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view(
            'surplus.index', compact(
                'surpluses',
                'countries',
                'areas',
                'surplus_status',
                'origin',
                'ageGroup',
                'sizes',
                'currencies',
                'levels',
                'confirm_options',
                'surplusLists',
                'surplusListSelected',
                'classes',
                'orderByOptions',
                'orderByDirection',
                'orderByField',
                'filterData',
                'organization_levels',
                'filter_imagen_species',
                'theWantedLists',
            )
        );
    }

    /**
     * Show all.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function showAll()
    {
        session()->forget('surplus.filter');

        return redirect(route('surplus.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $countries           = Country::orderBy('name')->pluck('name', 'id');
        $areas               = AreaRegion::orderBy('name')->pluck('name', 'id');
        $organization_levels = OrganisationLevel::get();

        $currencies          = Currency::get();
        $organization_levels = OrganisationLevel::get();

        $surplus_status = SurplusStatus::get();
        Arr::forget($surplus_status, 'collection');

        $origin   = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup = AgeGroup::get();
        $sizes    = Size::get();

        $surplusLists = SurplusList::pluck('name', 'id');

        return view(
            'surplus.create', compact(
                'countries',
                'areas',
                'surplus_status',
                'origin',
                'ageGroup',
                'sizes',
                'currencies',
                'surplusLists',
                'organization_levels'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\SurplusCreateRequest $request The request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SurplusCreateRequest $request)
    {
        //dd($request->all());
        $user = User::where('id', Auth::id())->first();

        $origin                = $request->origin;
        $area_region_id        = $request->area_region_id;
        $request['to_members'] = $request->input('to_members') ? true : false;

        $validator = Validator::make($request->toArray(), []);

        $surplusAlreadyExist = Surplus::where('animal_id', $request->animal_id)
            ->where('organisation_id', $request->organisation_id)
            ->where('surplus_status', '<>', 'collection')
            ->when(
                $origin, function ($query, $origin) {
                    return $query->where('origin', $origin);
                }
            )
            ->first();

        if ($surplusAlreadyExist != null) {
            if ($surplusAlreadyExist->costPriceM == 0 && $surplusAlreadyExist->costPriceF == 0 && $surplusAlreadyExist->costPriceU == 0 && $surplusAlreadyExist->costPriceP == 0) {
                $surplusAlreadyExist->update(['warning_indication' => 1]);
            } elseif ($surplusAlreadyExist->salePriceM == 0 && $surplusAlreadyExist->salePriceF == 0 && $surplusAlreadyExist->salePriceU == 0 && $surplusAlreadyExist->salePriceP == 0) {
                $surplusAlreadyExist->update(['warning_indication' => 1]);
            } else {
                $surplusAlreadyExist->update(['warning_indication' => 0]);
            }

            $validator->errors()->add('already_exist', 'Please note the species is already inserted, please adjust prices if necessary. (it is not necessary to adjust quantities and sexes, because it is changing daily)');

            return redirect(route('surplus.create'))->withInput($request->toArray())->withErrors($validator);
        } else {
            $request['inserted_by'] = Auth::id();

            if (!$user->hasPermission('surplus-suppliers.see-sale-prices')) {
                $request['sale_currency'] = $request->cost_currency;
                $request['salePriceM']    = 0;
                $request['salePriceF']    = 0;
                $request['salePriceU']    = 0;
                $request['salePriceP']    = 0;
            }

            $surplus = Surplus::create($request->all());

            $surplusCollectionAlreadyExist = Surplus::where('animal_id', $surplus->animal_id)
            ->where('organisation_id', $surplus->organisation_id)
            ->where('surplus_status', 'collection')
            ->first();

            if (!$surplusCollectionAlreadyExist) {
                Surplus::create(
                    [
                    'animal_id'        => $surplus->animal_id,
                    'organisation_id'  => $surplus->organisation_id,
                    'surplus_status'   => 'collection',
                    'inserted_by'      => Auth::id(),
                    'quantityM'        => -1,
                    'quantityF'        => -1,
                    'quantityU'        => -1,
                    'cost_currency'    => 'EUR',
                    'costPriceM'       => 0,
                    'costPriceF'       => 0,
                    'costPriceU'       => 0,
                    'costPriceP'       => 0,
                    'sale_currency'    => 'EUR',
                    'salePriceM'       => 0,
                    'salePriceF'       => 0,
                    'salePriceU'       => 0,
                    'salePriceP'       => 0,
                    'country_id'       => $surplus->country_id,
                    'area_region_id'   => $surplus->area_region_id,
                    ]
                );
            }

            //dd($request->surplusLists);
            $surplus->surplus_lists()->sync($request->surplusLists);

            if ($surplus->costPriceM == 0 && $surplus->costPriceF == 0 && $surplus->costPriceU == 0 && $surplus->costPriceP == 0) {
                $surplus->update(['warning_indication' => 1]);
            } elseif ($surplus->salePriceM == 0 && $surplus->salePriceF == 0 && $surplus->salePriceU == 0 && $surplus->salePriceP == 0) {
                $surplus->update(['warning_indication' => 1]);
            } else {
                $surplus->update(['warning_indication' => 0]);
            }

            $our_surplus = OurSurplus::where('animal_id', $request->animal_id)
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

            if (count($our_surplus) == 0) {
                $newOurSurplus = new OurSurplus();

                $newOurSurplus->animal_id          = $surplus->animal_id;
                $newOurSurplus->availability       = 'usually';
                $newOurSurplus->region_id          = ($surplus->country != null) ? $surplus->country->region->id : null;
                $newOurSurplus->area_region_id     = $surplus->area_region_id;
                $newOurSurplus->origin             = $surplus->origin;
                $newOurSurplus->age_group          = $surplus->age_group;
                $newOurSurplus->bornYear           = $surplus->bornYear;
                $newOurSurplus->size               = $surplus->size;
                $newOurSurplus->cost_currency      = $surplus->sale_currency;
                $newOurSurplus->sale_currency      = $surplus->sale_currency;
                $newOurSurplus->salePriceM         = $surplus->salePriceM;
                $newOurSurplus->salePriceF         = $surplus->salePriceF;
                $newOurSurplus->salePriceU         = $surplus->salePriceU;
                $newOurSurplus->salePriceP         = $surplus->salePriceP;
                $newOurSurplus->remarks            = $surplus->remarks;
                $newOurSurplus->intern_remarks     = $surplus->intern_remarks;
                $newOurSurplus->special_conditions = $surplus->special_conditions;

                $newOurSurplus->save();
            }

            return redirect(route('surplus.index'));
        }
    }

    /**
     * Show the surplus of surpliers.
     *
     * @param Surplus $id the surplus
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $surplus         = Surplus::findOrFail($id);
        $emails_received = Email::where('surplu_id', $id)->where('is_send', 0)->orderBy('created_at', 'DESC')->paginate(10);
        $emails          = Email::where('surplu_id', $id)->where('is_send', 1)->orderBy('created_at', 'DESC')->paginate(10);

        $animalRelatedSurplus = $surplus->animal->surpluses()->where('surplus_status', '<>', 'collection')->paginate(10);

        return view('surplus.show', compact('surplus', 'animalRelatedSurplus', 'emails_received', 'emails'));
    }

    /**
     * Show the form for editing.
     *
     * @param int     $id      The ID
     * @param Request $request The request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id, Request $request)
    {
        $surplus = Surplus::find($id);

        $surplus['supplier']     = ($surplus->contact != null) ? $surplus->contact->email : 'No contact selected.';
        $surplus['imagen_first'] = $surplus->animal->imagen_first ?? [];
        $surplus['catalog_pic']  = $surplus->animal->catalog_pic  ?? '';
        $surplus['animal']       = ($surplus->animal != null) ? $surplus->animal->common_name . ' (' . $surplus->animal->scientific_name . ')' : 'No animal selected.';
        $organization_levels     = OrganisationLevel::get();

        $surplusLists         = SurplusList::pluck('name', 'id');
        $surplusListsSelected = $surplus->surplus_lists()->pluck('surplus_list_id');

        $countries = Country::orderBy('name')->pluck('name', 'id');
        $areas     = AreaRegion::orderBy('name')->pluck('name', 'id');

        $currencies = Currency::get();

        $surplus_status = SurplusStatus::get();
        Arr::forget($surplus_status, 'collection');

        $origin   = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup = AgeGroup::get();
        $sizes    = Size::get();
        $url = $request["url"];

        return view(
            'surplus.edit', compact(
                'surplus',
                'countries',
                'areas',
                'surplus_status',
                'origin',
                'ageGroup',
                'sizes',
                'currencies',
                'surplusLists',
                'surplusListsSelected',
                'organization_levels',
                'url'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\SurplusUpdateRequest $request The request
     * @param int                                     $id      The id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SurplusUpdateRequest $request, $id)
    {
        $user = User::where('id', Auth::id())->first();

        // $surplusOriginal = Surplus::findOrFail($id);
        $surplusUpdate = Surplus::findOrFail($id);

        $origin                = $request->origin;
        $area_region_id        = $request->area_region_id;
        $request['to_members'] = $request->input('to_members') ? true : false;

        if (!$user->hasPermission('surplus-suppliers.see-sale-prices') && $surplusUpdate->cost_currency != $request->cost_currency) {
            $request['sale_currency'] = $request->cost_currency;
        }

        if (isset($request->institution_level) && $surplusUpdate->organisation != null) {
            if ($request->institution_level === 'empty') {
                $surplusUpdate->organisation()->update(['level' => null]);
            } else {
                $surplusUpdate->organisation()->update(['level' => $request->institution_level]);
            }
        }

        $surplusUpdate->update($request->all());
        $surplusUpdate->surplus_lists()->sync($request->surplusLists);

        if ($surplusUpdate->costPriceM == 0 && $surplusUpdate->costPriceF == 0 && $surplusUpdate->costPriceU == 0 && $surplusUpdate->costPriceP == 0) {
            $surplusUpdate->update(['warning_indication' => 1]);
        } elseif ($surplusUpdate->salePriceM == 0 && $surplusUpdate->salePriceF == 0 && $surplusUpdate->salePriceU == 0 && $surplusUpdate->salePriceP == 0) {
            $surplusUpdate->update(['warning_indication' => 1]);
        } else {
            $surplusUpdate->update(['warning_indication' => 0]);
        }

        $our_surplus = OurSurplus::where('animal_id', $request->animal_id)
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

        if (count($our_surplus) == 0) {
            $newOurSurplus = new OurSurplus();

            $newOurSurplus->animal_id          = $surplusUpdate->animal_id;
            $newOurSurplus->quantityM          = $surplusUpdate->quantityM;
            $newOurSurplus->quantityF          = $surplusUpdate->quantityF;
            $newOurSurplus->quantityU          = $surplusUpdate->quantityU;
            $newOurSurplus->region_id          = ($surplusUpdate->country != null) ? $surplusUpdate->country->region->id : null;
            $newOurSurplus->area_region_id     = $surplusUpdate->area_region_id;
            $newOurSurplus->origin             = $surplusUpdate->origin;
            $newOurSurplus->age_group          = $surplusUpdate->age_group;
            $newOurSurplus->bornYear           = $surplusUpdate->bornYear;
            $newOurSurplus->size               = $surplusUpdate->size;
            $newOurSurplus->sale_currency      = $surplusUpdate->sale_currency;
            $newOurSurplus->salePriceM         = $surplusUpdate->salePriceM;
            $newOurSurplus->salePriceF         = $surplusUpdate->salePriceF;
            $newOurSurplus->salePriceU         = $surplusUpdate->salePriceU;
            $newOurSurplus->salePriceP         = $surplusUpdate->salePriceP;
            $newOurSurplus->remarks            = $surplusUpdate->remarks;
            $newOurSurplus->intern_remarks     = $surplusUpdate->intern_remarks;
            $newOurSurplus->special_conditions = $surplusUpdate->special_conditions;

            $newOurSurplus->save();
        }

        if (!empty($request->url)) {
            return redirect(route('surplus.index'))->with("success", "The surplus information was saved correctly");
        } else {
            return redirect(route('surplus.show', $surplusUpdate->id))->with("success", "The surplus information was saved correctly");;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The id
     *
     * @return \Illuminate\Http\RedirectResponse|Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $surplusDelete = Surplus::findOrFail($id);
        Storage::deleteDirectory('public/surpluses_docs/' . $surplusDelete->id);
        $surplusDelete->delete();

        return redirect(route('surplus.index'));
    }

    /**
     * Remove the selected items.
     *
     * @param Request $request the request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request) //phpcs:ignore
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
     * Upload files for surplus.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request) //phpcs:ignore
    {
        if ($request->hasFile('file')) {
            $surplus = Surplus::findOrFail($request->surplusId);

            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            $path = Storage::putFileAs('public/surpluses_docs/' . $surplus->id, $file, $file_name);
        }

        //return redirect()->back()->with('status', 'Successfully uploaded file');
        return response()->json(['success' => true], 200);
    }

    /**
     * Create standard surplus record from surplus.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOurSurplus(Request $request)
    {
        $surplus = Surplus::findOrFail($request->surplusId);

        if ($surplus->salePriceM == 0 && $surplus->salePriceF == 0 && $surplus->salePriceU == 0 && $surplus->salePriceP == 0) {
            return redirect()->back()->with('error_msg', 'This surplus2 has not sale prices, please check.');
        } else {
            $origin         = $surplus->origin;
            $area_region_id = $surplus->area_region_id;
            $our_surplus    = OurSurplus::where('animal_id', $surplus->animal_id)
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

            if (count($our_surplus) > 0) {
                return redirect()->back()->with('error_msg', 'There is already a standard surplus with the same species, origin and area.');
            } else {
                $newOurSurplus = new OurSurplus();

                $newOurSurplus->animal_id          = $surplus->animal_id;
                $newOurSurplus->availability       = 'usually';
                $newOurSurplus->region_id          = ($surplus->country != null) ? $surplus->country->region->id : null;
                $newOurSurplus->area_region_id     = $surplus->area_region_id;
                $newOurSurplus->origin             = $surplus->origin;
                $newOurSurplus->age_group          = $surplus->age_group;
                $newOurSurplus->bornYear           = $surplus->bornYear;
                $newOurSurplus->size               = $surplus->size;
                $newOurSurplus->sale_currency      = $surplus->sale_currency;
                $newOurSurplus->salePriceM         = $surplus->salePriceM;
                $newOurSurplus->salePriceF         = $surplus->salePriceF;
                $newOurSurplus->salePriceU         = $surplus->salePriceU;
                $newOurSurplus->salePriceP         = $surplus->salePriceP;
                $newOurSurplus->remarks            = $surplus->remarks;
                $newOurSurplus->intern_remarks     = $surplus->intern_remarks;
                $newOurSurplus->special_conditions = $surplus->special_conditions;

                $newOurSurplus->save();

                return redirect()->back()->with('status', 'Standard surplus created successfully.');
            }
        }
    }

    /**
     * Update field "to_members_date" if param is true.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateToMembersDate(Request $request)
    {
        if ($request->has('surplus_id')) {
            $surplus = Surplus::findOrFail($request->surplus_id);
            if ($request->value == true) {
                $surplus->to_members_date = Carbon::now()->format('Y-m-d H:i:s');
            }

            $surplus->update();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Select surplus list.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function selectSurplusList(Request $request)
    {
        session()->forget('surplus.filter');

        session(['surplus.filter' => ['surplusListsTopSelect' => $request->surplusListsTopSelect]]);

        return redirect(route('surplus.index'));
    }

    /**
     * Add surplus list.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function saveSurplusList(Request $request)
    {
        SurplusList::create($request->all());

        return response()->json(['success' => true]);
    }

    /**
     * Remove surplus list.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteSurplusList(Request $request)
    {
        $itemDelete = SurplusList::findOrFail($request->id);

        if ($itemDelete != null) {
            $itemDelete->delete();
            session()->forget('surplus.filter');
        }

        return response()->json(['success' => true]);
    }

    /**
     * Filter surplus.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function filterSurplus(Request $request)
    {
        // Check if filter is set on session
        if (session()->has('surplus.filter')) {
            $query = session('surplus.filter');
            $query = Arr::collapse([$query, $request->query()]);
            session(['surplus.filter' => $query]);
        } else { // Set session surplus filter
            session(['surplus.filter' => $request->query()]);
        }

        return redirect(route('surplus.index'));
    }

    /**
     * Order by.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function orderBy(Request $request)
    {
        $query                     = session('surplus.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['surplus.filter' => $query]);

        return redirect(route('surplus.index'));
    }

    /**
     * Records per page.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('surplus.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['surplus.filter' => $query]);

        return redirect(route('surplus.index'));
    }

    /**
     * Remove from surplus session.
     *
     * @param string $key The key
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeFromSurplusSession($key)
    {
        $query = session('surplus.filter');
        Arr::forget($query, $key);
        session(['surplus.filter' => $query]);

        return redirect(route('surplus.index'));
    }

    /**
     * Edit selected items.
     *
     * @param \Illuminate\Http\Request $request the request
     *
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

                if (isset($request->cost_currency)) {
                    $surplus->update(['cost_currency' => $request->cost_currency]);
                }

                if (isset($request->sale_currency)) {
                    $surplus->update(['sale_currency' => $request->sale_currency]);
                }

                if (isset($request->supplier_level)) {
                    $surplus->organisation()->update(['level' => $request->supplier_level]);
                }

                if (isset($request->to_members)) {
                    if ($request->to_members == 'yes') {
                        $surplus->to_members_date = Carbon::now()->format('Y-m-d H:i:s');
                    }
                    $surplus->update(['to_members' => ($request->to_members == 'yes') ? true : false]);
                }

                if (isset($request->surplus_status)) {
                    $surplus->update(['surplus_status' => $request->surplus_status]);
                }

                if (isset($request->add_to_surplus_lists)) {
                    $surplus->surplus_lists()->syncWithoutDetaching($request->add_to_surplus_lists);
                }

                if (isset($request->remove_from_surplus_lists)) {
                    $surplus->surplus_lists()->detach($request->remove_from_surplus_lists);
                }

                if (isset($request->institution_level) && $surplus->organisation != null) {
                    if ($request->institution_level === 'empty') {
                        $surplus->organisation()->update(['level' => null]);
                    } else {
                        $surplus->organisation()->update(['level' => $request->institution_level]);
                    }
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
    public function printSurplusList(Request $request)
    {
        $document = $request->document_type;
        $language = $request->language;

        $print_stuffed = $request->print_stuffed ?? "no";

        if ($request->export_option !== 'all') {
            if ($print_stuffed == 'yes') {
                $surplusToPrint = Surplus::whereIn('id', $request->items)->whereNotNull('catalog_pic')->get();
            } else {
                $surplusToPrint = Surplus::whereIn('id', $request->items)->get();
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
                'void' => Surplus::whereIn('id', json_decode($request->cover_animals))->get(),
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
            : 'lista_de_excedentes_' . Carbon::now()->format('Y-m-d-H-m-s');
        $extension    = '.' . $document;
        $has_prices   = $request->prices === 'yes' ? true : false;
        $date         = Carbon::now()->format('F j, Y');
        $rateEurUsd   = number_format(CurrencyRate::latest()->value('EUR_USD'), 2, '.', '');
        $rateUsdEur   = number_format(CurrencyRate::latest()->value('USD_EUR'), 2, '.', '');
        $picture      = $request->pictures;
        $type         = "surplus";
        $is_standard  = true;
        $download_url = url('/') . '/storage/surplus_wanted_lists/' . $name . '.html';
        $wanted       = $request->wanted;

        if ($print_stuffed == "yes" && !empty($request->filter_client_id)) {
            $contact = Contact::findOrFail($request->filter_client_id);
            $fullname = $contact->letter_name;
        }

        if ($wanted == 'yes' && isset($request->wanted_list) != null) {
            $wantedListSelected = WantedList::find($request->wanted_list);
            $wantedToPrint      = $wantedListSelected->wanteds()->get()->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);
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
     * Get email option info.
     *
     * @param int $id The id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function surplusEmailToClients($id)
    {
        $surplus = Surplus::findOrFail($id);

        $number_email = '#SU-' . $surplus->id;

        if ($surplus->animal != null) {
            $email_from    = 'info@zoo-services.com';
            $email_to      = '';
            $email_subject = 'We can offer ' . $surplus->animal->common_name . ' (' . $surplus->animal->scientific_name . '). ' . $number_email;
            $email_body    = view('emails.surplus-email-to-clients', compact('surplus'))->render();

            $wantedRecordsWithInstitution = Wanted::where('animal_id', $surplus->animal_id)
                ->whereNotNull('organisation_id')
                ->get();

            $wantedRecordsWithOnlyContact = Wanted::where('animal_id', $surplus->animal_id)
                ->whereNull('organisation_id')
                ->whereNotNull('client_id')
                ->get();

            if ($wantedRecordsWithInstitution->count() > 0 || $wantedRecordsWithOnlyContact->count() > 0) {
                foreach ($wantedRecordsWithInstitution as $wantedWithInstitution) {
                    foreach ($wantedWithInstitution->organisation->contacts as $contact) {
                        if ($contact->email != null && $contact->mailing === 'All mailings' && !Str::contains($email_to, $contact->email)) {
                            $email_to .= $contact->email . ',';
                        }
                    }
                }

                foreach ($wantedRecordsWithOnlyContact as $wantedWithContact) {
                    if ($wantedWithContact->client->email != null && $wantedWithContact->client->mailing === 'All mailings' && !Str::contains($email_to, $wantedWithContact->client->email)) {
                        $email_to .= $wantedWithContact->client->email . ',';
                    }
                }

                return view('surplus.surplus_email_to_clients', compact('surplus', 'email_from', 'email_to', 'email_subject', 'email_body'));
            } else {
                return redirect()->back()->with('error_msg', 'The species of this surplus record has not clients interested.');
            }
        } else {
            return redirect()->back()->with('error_msg', 'This surplus record has not animal assigned.');
        }
    }

    /**
     * Get email option info.
     *
     * @param int $id The id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
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

                $number_email = '#SU-' . $surplus->id;

                $email_subject = 'About your surplus specimens. ' . $number_email;
                $email_body    = view('emails.surplus-more-details', compact('surplus'))->render();

                return view('surplus.email_asking_surplus_details', compact('surplus', 'email_from', 'email_to', 'email_subject', 'email_body'));
            } else {
                return redirect()->back()->with('error_msg', 'This surplus record has not institution assigned.');
            }
        } else {
            return redirect()->back()->with('error_msg', 'This surplus record has not animal assigned.');
        }
    }

    /**
     * Send email option.
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSurplusEmail(Request $request)
    {
        $surplus = Surplus::findOrFail($request->id_surplus);

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

                Mail::to($email)->cc($email_cc_array)->queue(new SendGeneralEmail($request->email_from, $request->email_subject, $request->email_body));
            }
        }*/

        try{
            SendMailQueueEmail::dispatch($request->email_from, $request->email_subject, $request->email_body, $email_to_array, $email_cc_array, $surplus->id, "surplu")->onQueue('surplus_mail');
        } catch (\Throwable $th) {

        }

        return redirect(route('surplus.show', [$surplus->id]))->with('success', 'Email successfully sent.');
    }

    /**
     * Delete surplus file.
     *
     * @param int    $surplus_id id
     * @param string $file_name  file name
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete_file($surplus_id, $file_name) //phpcs:ignore
    {
        Storage::delete('public/surpluses_docs/' . $surplus_id . '/' . $file_name);

        return redirect(route('surplus.show', [$surplus_id]));
    }

    /**
     * Delete surplus file.
     *
     * @param int    $surplus_id id
     * @param string $file_name  file name
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete_file_catalog($surplus_id, $file_name) //phpcs:ignore
    {
        Storage::delete('public/surpluses_pictures/'.$surplus_id.'/'.$file_name);

        return redirect(route('surplus.show', [$surplus_id]));
    }

    /**
     * Get related standard surpluses
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function searchRelatedStandardSurplus(Request $request)
    {
        $surplus = Surplus::findOrFail($request->id);

        $area_region_id = $surplus->area_region_id;
        $origin         = $surplus->origin;

        $stdSurpluses = OurSurplus::with(['region'])
            ->where('animal_id', $surplus->animal_id)
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

        foreach ($stdSurpluses as $stdSurplus) {
            $stdSurplus->availability = $stdSurplus->availability_field;
            $stdSurplus->origin       = $stdSurplus->origin_field;
            $stdSurplus->age          = $stdSurplus->age_field;
            $stdSurplus->size         = $stdSurplus->size_field;
            $stdSurplus->updated_date = date('Y-m-d', strtotime($stdSurplus->updated_at));
        }

        return response()->json(['success' => count($stdSurpluses) > 0 ? true : false, 'stdSurpluses' => $stdSurpluses]);
    }

    /**
     * Export
     *
     * @param Request $request THe request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $file_name = 'Surplus list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $surpluses = Surplus::whereIn('id', explode(',', $request->items))->get();

        if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
            $orderByDirection = $request['orderByDirection'];
            $orderByField = $request['orderByField'];

            if ($orderByField == 'common_name' || $orderByField == 'scientific_name' || $orderByField == 'code_number') {
                if ($orderByDirection == "desc") {
                    $surpluses = $surpluses->sortByDesc(
                        function ($surplus, $key) use ($orderByField) {
                            return $surplus->animal->$orderByField;
                        }
                    );
                } else {
                    $surpluses = $surpluses->sortBy(
                        function ($surplus, $key) use ($orderByField) {
                            return $surplus->animal->$orderByField;
                        }
                    );
                }
            } elseif ($orderByField == 'organisation_name') {
                if ($orderByDirection == "desc") {
                    $surpluses = $surpluses->sortByDesc(
                        function ($surplus, $key) use ($orderByField) {
                            if (!empty($surplus->organisation)) {
                                return $surplus->organisation->name;
                            }
                        }
                    );
                } else {
                    $surpluses = $surpluses->sortBy(
                        function ($surplus, $key) use ($orderByField) {
                            if (!empty($surplus->organisation)) {
                                return $surplus->organisation->name;
                            }
                        }
                    );
                }
            } else {
                if ($orderByDirection == "desc") {
                    $surpluses = $surpluses->sortByDesc(
                        function ($surplus, $key) use ($orderByField) {
                            return $surplus->$orderByField;
                        }
                    );
                } else {
                    $surpluses = $surpluses->sortBy(
                        function ($surplus, $key) use ($orderByField) {
                            return $surplus->$orderByField;
                        }
                    );
                }
            }
        }

        $export = new SurplusExport($surpluses);

        return Excel::download($export, $file_name);
    }

    /**
     * Get records by filter
     *
     * @param bool   $sw_list      Dunno
     * @param string $stuffer_list Dunno
     *
     * @return array
     */
    public function get_records_by_filter($sw_list=false, $stuffer_list = "no") //phpcs:ignore
    {
        $orderByDirection    = null;
        $orderByField        = null;
        $surplusListSelected = null;
        $recordsPerPage      = 50;
        $filterData          = [];

        // Check if filter is set on session
        if (session()->has('surplus.filter')) {
            $request = session('surplus.filter');
        }

        $surplusListSelected = null;
        if (isset($request['surplusListsTopSelect']) && $request['surplusListsTopSelect'] > 0) {
            $surplusListSelected = SurplusList::find($request['surplusListsTopSelect']);

            $surpluses = $surplusListSelected->surpluses()->orderByDesc('updated_at');

            $filterData = Arr::add($filterData, 'surplusListsTopSelect', 'Surplus list: ' . $surplusListSelected->name);
        } else {
            $surpluses = Surplus::with(['animal'])->where('surplus_status', '<>', 'collection')->orderByDesc('updated_at');
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

                        $surpluses->where('animal_id', $filterAnimal->id);

                        $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
                    }
                } elseif ($request['filter_animal_option'] === 'by_name') {
                    if (isset($request['filter_animal_name'])) {
                        $surpluses->whereHas(
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
                    $surpluses->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $surpluses->whereHas(
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
                    $surpluses->whereHas(
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
                    $surpluses->whereHas(
                        'animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        }
                    );
                }
            }

            if (isset($request['filter_origin'])) {
                if ($request['filter_origin'] === 'empty') {
                    $surpluses->whereNull('origin');
                } else {
                    $surpluses->where('origin', $request['filter_origin']);
                }

                $filterData = Arr::add($filterData, 'filter_origin', 'Origin: ' . $request['filter_origin']);
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();

                if ($request['filter_country'] == 0) {
                    $surpluses->whereNull('country_id');
                } else {
                    $surpluses->where('country_id', $filterCountry->id);
                }

                $filterData = Arr::add($filterData, 'filter_country', 'Country: ' . ($filterCountry != null ? $filterCountry->name : 'empty'));
            }

            if (isset($request['filter_area'])) {
                $filterArea = AreaRegion::where('id', $request['filter_area'])->first();

                if ($request['filter_area'] == 0) {
                    $surpluses->whereNull('area_region_id');
                } else {
                    $surpluses->where('area_region_id', $filterArea->id);
                }

                $filterData = Arr::add($filterData, 'filter_area', 'Area: ' . ($filterArea != null ? $filterArea->name : 'empty'));
            }

            if (isset($request['filter_surplus_status'])) {
                if ($request['filter_surplus_status'] === 'empty') {
                    $surpluses->whereNull('surplus_status');
                }elseif($request['filter_surplus_status'] === 'not_archive'){
                    $surpluses->where('surplus_status', "!=", "archive");
                }
                else {
                    $surpluses->where('surplus_status', $request['filter_surplus_status']);
                }

                $filterData = Arr::add($filterData, 'filter_surplus_status', 'Status: ' . ucfirst(str_replace("_", " ", $request['filter_surplus_status'])));
            }

            if (isset($request['filter_have_cost_prices'])) {
                if ($request['filter_have_cost_prices'] == 'yes') {
                    $surpluses->where(
                        function ($query) {
                            $query->where('costPriceM', '<>', '0')
                                ->orWhere('costPriceF', '<>', '0')
                                ->orWhere('costPriceU', '<>', '0')
                                ->orWhere('costPriceP', '<>', '0');
                        }
                    );
                } else {
                    $surpluses->where(
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
                    $surpluses->where(
                        function ($query) {
                            $query->where('salePriceM', '<>', '0')
                                ->orWhere('salePriceF', '<>', '0')
                                ->orWhere('salePriceU', '<>', '0')
                                ->orWhere('salePriceP', '<>', '0');
                        }
                    );
                } else {
                    $surpluses->where(
                        [
                        ['salePriceM', '=', '0'],
                        ['salePriceF', '=', '0'],
                        ['salePriceU', '=', '0'],
                        ['salePriceP', '=', '0'], ]
                    );
                }

                $filterData = Arr::add($filterData, 'filter_have_sale_prices', 'Sale prices: ' . $request['filter_have_sale_prices']);
            }

            //This option need to be checked according to the logic between the surplus and the standard surplus.
            //The rule is that surplus need to match with the standard surplus by same species and continent.
            if (isset($request['filter_have_standard_surplus'])) {
                $surpluses_matched = Surplus::join(
                    'our_surplus', function ($join) {
                        $join->on('surplus.animal_id', '=', 'our_surplus.animal_id')
                            ->on('surplus.area_region_id', '=', 'our_surplus.area_region_id');
                    }
                )
                    ->pluck('surplus.id');

                if ($request['filter_have_standard_surplus'] == 'yes') {
                    $surpluses->whereIn('id', $surpluses_matched);
                } else {
                    $surpluses->whereNotIn('id', $surpluses_matched);
                }

                $filterData = Arr::add($filterData, 'filter_have_standard_surplus', 'In stock list: ' . $request['filter_have_standard_surplus']);
            }

            if (isset($request['filter_to_members'])) {
                $surpluses->where('to_members', ($request['filter_to_members'] == 'yes') ? true : false);

                $filterData = Arr::add($filterData, 'filter_to_members', 'To members: ' . $request['filter_to_members']);
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

            if (isset($request['filter_upload_images'])) {
                if ($request['filter_upload_images'] == "yes") {
                    $surpluses->whereNotNull('catalog_pic')->where('catalog_pic', "!=", " ");
                } elseif ($request['filter_upload_images'] == "no") {
                    $surpluses->whereNull('catalog_pic')->orWhere('catalog_pic', " ");
                }

                $filterData = Arr::add($filterData, 'filter_upload_images', 'Upload Images: ' . $request['filter_upload_images']);
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


            if (isset($request['filter_show_stuffed'])) {
                if ($request['filter_show_stuffed'] == "no") {
                    $surpluses->where(
                        function ($query) {
                            $query->where('origin', "!=", "stuffed")
                                ->orwhereNull('origin');
                        }
                    );
                    $filterData = Arr::add($filterData, 'filter_show_stuffed', 'Show stuffed: ' . $request['filter_show_stuffed']);
                } else {
                    $filterData = Arr::add($filterData, 'filter_show_stuffed', 'Show stuffed: ' . $request['filter_show_stuffed']);
                }
            }
            if ($stuffer_list == "yes") {
                $surpluses->whereNotNull("catalog_pic");
            }
        }

        $surpluses = $surpluses->get();
        //dump(DB::getQueryLog());

        if (isset($request)) {
            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByField == 'common_name' || $orderByField == 'scientific_name' || $orderByField == 'code_number') {
                    if ($orderByDirection == 'desc') {
                        $surpluses = $surpluses->sortByDesc(
                            function ($surplus, $key) use ($orderByField) {
                                return $surplus->animal->$orderByField;
                            }
                        );
                    } else {
                        $surpluses = $surpluses->sortBy(
                            function ($surplus, $key) use ($orderByField) {
                                return $surplus->animal->$orderByField;
                            }
                        );
                    }
                } elseif ($orderByField == 'organisation_name') {
                    if ($orderByDirection == 'desc') {
                        $surpluses = $surpluses->sortByDesc(
                            function ($surplus, $key) use ($orderByField) {
                                if (!empty($surplus->organisation)) {
                                    return $surplus->organisation->name;
                                }
                            }
                        );
                    } else {
                        $surpluses = $surpluses->sortBy(
                            function ($surplus, $key) use ($orderByField) {
                                if (!empty($surplus->organisation)) {
                                    return $surplus->organisation->name;
                                }
                            }
                        );
                    }
                } else {
                    if ($orderByDirection == 'desc') {
                        $surpluses = $surpluses->sortByDesc(
                            function ($surplus, $key) use ($orderByField) {
                                return $surplus->$orderByField;
                            }
                        );
                    } else {
                        $surpluses = $surpluses->sortBy(
                            function ($surplus, $key) use ($orderByField) {
                                return $surplus->$orderByField;
                            }
                        );
                    }
                }
            }

            if (isset($request['filter_same_surplus']) && !$sw_list) {
                $filterData = Arr::add($filterData, 'filter_same_surplus', 'Same surplus: ' . $request['filter_same_surplus']);

                $surpluses = $surpluses->groupBy(['organisation_id', 'animal_id', 'origin', 'area_region_id']);
            }
        }

        return [
            "filterData" => $filterData,
            "surpluses" => $surpluses,
            "orderByDirection" => $orderByDirection,
            "orderByField" => $orderByField,
            "surplusListSelected" => $surplusListSelected,
            "recordsPerPage" => $recordsPerPage,
            "filter_imagen_species" => $request["filter_imagen_species"] ?? "",
            "filter_upload_images" => $request["filter_upload_images"] ?? ""
        ];
    }

    /**
     * DetailsSurplusSpecimens
     *
     * @param int $id Id
     *
     * @return ?
     */
    public function detailsSurplusSpecimens($id)
    {
        $surplus = Surplus::findOrFail($id);

        if ($surplus->organisation != null) {
            $email_from = 'info@zoo-services.com';

            $email_to = '';
            foreach ($surplus->organisation->contacts as $contact) {
                if ($contact->email != null) {
                    $email_to .= $contact->email . ', ';
                }
            }
            $number_email = '#SU-' . $surplus->id;

            $email_subject = 'Apply more details of surplus-specimens. ' . $number_email;
            $email_body    = view('emails.send-details-surplus-specimens', compact('surplus'))->render();

            return view('surplus.email_details_specimen', compact('surplus', 'email_from', 'email_to', 'email_subject', 'email_body'));
        } else {
            return redirect()->back()->with('error', 'This surplus record has not institution assigned.');
        }
    }

    /**
     * Send surplus details specimens
     *
     * @param Request $request the request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSurplusDetailsSpecimens(Request $request)
    {
        $surplus = Surplus::findOrFail($request->id_surplus);

        $email_to_array = array_map('trim', explode(',', $request->email_to));

        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        foreach ($email_to_array as $email) {
            $email = trim($email);
            if ($email != '') {
                $contact = Contact::GetContacts()->where('email', $email)->first();

                $email_body = Str::of($request->email_body)->replace('contact_name', $contact->letter_name);

                try{
                    $email_create = $this->createSentEmail($request->email_subject, $request->email_from, $email, $email_body, $surplus->id);
                    $email_options = new SendGeneralEmail($request->email_from, $request->email_subject, $email_body, $email_create["id"]);
                    if (App::environment('production')) {
                        $email_options->sendEmail($email, $email_options->build());
                    } else {
                        Mail::to($email)->send(new SendGeneralEmail($request->email_from, $request->email_subject, $email_body, $email_create["id"]));
                    }
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Failed to send mail correctly');
                }
            }
        }

        return redirect(route('surplus.show', [$surplus->id]))->with('success', 'Email successfully sent.');
    }

    /**
     * Update field "to_members_date" if param is true.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDate(Request $request)
    {
        if ($request->has('surplus_id')) {
            $surplus             = Surplus::findOrFail($request->surplus_id);
            $surplus->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $surplus->update();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update field "to_members_date" if param is true.
     *
     * @param Request $request The request
     *
     * @return \Illuminate\Http\Response
     */
    public function duplicateSurplus(Request $request)
    {
        $animal_id         = $request['animal_id'];
        $supplier          = $request['organisation_id'];
        $surplus_duplicate = Surplus::where('animal_id', $animal_id)
            ->where('organisation_id', $supplier)
            ->first();
        if (!empty($surplus_duplicate)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Remove the selected actions.
     *
     * @param string $subject The subject
     * @param string $from    From
     * @param string $email   Email
     * @param string $body    Body
     * @param int    $id      The ID
     *
     * @return \App\Models\Email
     */
    public function createSentEmail($subject, $from, $email, $body, $id = null)
    {
        $label     = Labels::where('name', 'surplus')->first();
        $contact   = Contact::where('email', $email)->first();
        $new_email = new Email();
        if (!empty($contact) && $contact->count() > 0) {
            $first_name              = $contact['first_name'] ?? '';
            $last_name               = $contact['last_name']  ?? '';
            $name                    = $first_name . ' ' . $last_name;
            $new_email['contact_id'] = $contact['id'] ?? null;
        } else {
            $organisation                 = Organisation::where('email', $email)->first();
            $new_email['organisation_id'] = $organisation['id']   ?? null;
            $name                         = $organisation['name'] ?? '';
        }
        $new_email['from_email'] = $from;
        $new_email['to_email']   = $email;
        $new_email['body']       = $body;
        $new_email['guid']       = rand(1, 100);
        $new_email['subject']    = $subject;
        $new_email['name']       = $name;
        if (!empty($id)) {
            $new_email['surplu_id'] = $id;
        }
        $new_email['is_send'] = 1;
        $new_email->save();
        $new_email->labels()->attach($label);

        return $new_email;
    }

    /**
     * Execute the console command.
     *
     * @param object $email Email
     *
     * @return mixed
     */
    public function saveSentEmail($email)
    {
        $GraphService = new GraphService();
        $GraphService->initializeGraphForUserAuth();
        if (!empty($email)) {
            $userToken = $GraphService->getAllUserToken();
            if (!empty($userToken)) {
                foreach ($userToken as $row) {
                    $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                    $user_id = $GraphService->getUserByEmail($token, $email["from_email"]);
                    if (!empty($token)) {
                        $email_attachment = [];
                        if (!empty($email->attachments)) {
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
                        if (!empty($result)) {
                            $result = $GraphService->updateIsDraftEmailInbox($token,  $user_id->getId(), $result["id"]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reset list email new surplus
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function resetListEmailNewSurplu()
    {
        $surplu = Surplus::where("new_animal", 1)->get();
        if (!empty($surplu)) {
            foreach ($surplu as $row) {
                $row['new_animal'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Surplus';

        return view('components.reset_list_email_new', compact('title_dash'));
    }

    /**
     * Upload picture
     *
     * @param Request $request THe request
     *
     * @return void
     */
    public function upload_picture(Request $request) //phpcs:ignore
    {
        $request->validate(
            [
            'file' => "image|mimes:jpeg,jpg,bmp,png",
            'file' => 'dimensions:max_width=600,max_height=600'
            ]
        );

        $surplur = Surplus::findOrFail($request->surplusId);

        if ($request->hasFile('file')) {
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

            Storage::delete('public/surpluses_pictures/'.$request->surplusId."/".$surplur->catalog_pic);
            $surplur->update(['catalog_pic' => $file_name]);

            $path = Storage::putFileAs(
                'public/surpluses_pictures/'.$request->surplusId, $file, $file_name
            );
        }

        return response()->json(['success' => true]);
    }
}
