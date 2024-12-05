<?php

namespace App\Http\Controllers;

use App\Enums\AgeGroup;
use App\Enums\LookingFor;
use App\Enums\OurWantedOrderByOptions;
use App\Exports\OurWantedsExport;
use App\Http\Requests\OurWantedCreateRequest;
use App\Http\Requests\OurWantedUpdateRequest;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Classification;
use App\Models\Origin;
use App\Models\OurWanted;
use App\Models\OurWantedList;
use Carbon\Carbon;
use DOMPDF;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OurWantedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $ourWanteds = OurWanted::join('animals', 'our_wanted.animal_id', '=', 'animals.id')
        //                         ->select('*', 'our_wanted.id as ourWantedId', 'our_wanted.created_at as created_date')
        //                         ->orderByDesc('our_wanted.updated_at');

        $ourWantedLists = OurWantedList::get();

        $areas = AreaRegion::all();

        $origin     = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $orderByOptions = OurWantedOrderByOptions::get();

        $result_array          = $this->get_records_by_filter();
        $filterData            = $result_array['filterData'];
        $ourWanteds            = $result_array['ourWanteds'];
        $orderByDirection      = $result_array['orderByDirection'];
        $orderByField          = $result_array['orderByField'];
        $ourWantedListSelected = $result_array['ourWantedListSelected'];

        $array_object_results = [];
        foreach ($ourWanteds as $ourWanted) {
            array_push($array_object_results, $ourWanted);
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $perPage = $result_array['recordsPerPage'];

        $currentItems = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);

        $ourWanteds = new LengthAwarePaginator($currentItems, count($array_object_results), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        //dd($ourWanteds);

        return view('our_wanted.index', compact(
            'ourWanteds',
            'areas',
            'ourWantedLists',
            'ourWantedListSelected',
            'origin',
            'ageGroup',
            'lookingFor',
            'classes',
            'orderByOptions',
            'orderByDirection',
            'orderByField',
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
        session()->forget('our_wanted.filter');

        return redirect(route('our-wanted.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = AreaRegion::all();

        $ourWantedLists = OurWantedList::pluck('name', 'id');

        $origin     = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        return view('our_wanted.create', compact(
            'areas',
            'ourWantedLists',
            'origin',
            'ageGroup',
            'lookingFor'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OurWantedCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OurWantedCreateRequest $request)
    {
        $ourWanted = OurWanted::create($request->all());

        $ourWanted->area_regions()->sync($request->area_id);
        $ourWanted->ourwanted_lists()->sync($request->ourWantedLists);

        return redirect(route('our-wanted.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ourWanted = OurWanted::findOrFail($id);

        $animalRelatedWanted    = $ourWanted->animal->wanteds()->paginate(10);
        $animalRelatedStdWanted = $ourWanted->animal->our_wanteds()->paginate(10);

        return view('our_wanted.show', compact(
            'ourWanted',
            'animalRelatedWanted',
            'animalRelatedStdWanted'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ourWanted           = OurWanted::find($id);
        $ourWanted['animal'] = ($ourWanted->animal != null) ? $ourWanted->animal->common_name . ' (' . $ourWanted->animal->scientific_name . ')' : 'No animal selected.';

        $areas                  = AreaRegion::all();
        $ourWantedAreasSelected = $ourWanted->area_regions()->pluck('area_region_id');

        $ourWantedLists         = OurWantedList::pluck('name', 'id');
        $ourWantedListsSelected = $ourWanted->ourwanted_lists()->pluck('our_wanted_list_id');

        $origin     = Origin::orderBy('id', 'ASC')->pluck('name', 'short_cut');
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        return view('our_wanted.edit', compact(
            'ourWanted',
            'origin',
            'ageGroup',
            'lookingFor',
            'areas',
            'ourWantedAreasSelected',
            'ourWantedLists',
            'ourWantedListsSelected'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OurWantedUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OurWantedUpdateRequest $request, $id)
    {
        $updateItem = OurWanted::findOrFail($id);
        $updateItem->update($request->all());

        $updateItem->area_regions()->sync($request->area_id);
        $updateItem->ourwanted_lists()->sync($request->ourWantedLists);

        return redirect(route('our-wanted.show', [$updateItem->id]));
    }

    /**
     * Check same record by: species.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkSameRecord(Request $request)
    {
        $ourWantedId           = $request->ourwanted_id;
        $ourWantedAlreadyExist = OurWanted::where('animal_id', $request->animal_id)
            ->where('origin', $request->origin)
            ->when($ourWantedId, function ($query, $ourWantedId) {
                return $query->where('id', '<>', $ourWantedId);
            })
            ->first();

        if ($ourWantedAlreadyExist != null) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteItem = OurWanted::findOrFail($id);
        $deleteItem->delete();

        return redirect(route('our-wanted.index'));
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
                $deleteItem = OurWanted::findOrFail($id);
                $deleteItem->delete();
            }
        }

        return response()->json();
    }

    /**
     * Select wanted list.
     *
     * @param Request $Request
     * @return \Illuminate\Http\Response
     */
    public function selectOurWantedList(Request $request)
    {
        session()->forget('our_wanted.filter');

        session(['our_wanted.filter' => ['ourWantedListsTopSelect' => $request->ourWantedListsTopSelect]]);

        return redirect(route('our-wanted.index'));
    }

    /**
     * Add wanted list.
     *
     * @param Request $Request
     * @return \Illuminate\Http\Response
     */
    public function saveOurWantedList(Request $request)
    {
        OurWantedList::create($request->all());

        return response()->json();
    }

    /**
     * Remove wanted list.
     *
     * @param Request $Request
     * @return \Illuminate\Http\Response
     */
    public function deleteOurWantedList(Request $request)
    {
        $itemDelete = OurWantedList::findOrFail($request->id);

        if ($itemDelete != null) {
            $itemDelete->delete();
            session()->forget('our_wanted.filter');
        }

        return response()->json();
    }

    /**
     * Filter standard wanteds.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterOurWanted(Request $request)
    {
        // Check if filter is set on session
        if (session()->has('our_wanted.filter')) {
            $query = session('our_wanted.filter');
            $query = Arr::collapse([$query, $request->query()]);
            session(['our_wanted.filter' => $query]);
        } else { // Set session wanted filter
            session(['our_wanted.filter' => $request->query()]);
        }

        return redirect(route('our-wanted.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('our_wanted.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['our_wanted.filter' => $query]);

        return redirect(route('our-wanted.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('our_wanted.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['our_wanted.filter' => $query]);

        return redirect(route('our-wanted.index'));
    }

    /**
     * Remove from our_wanted session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromOurWantedSession($key)
    {
        $query = session('our_wanted.filter');
        Arr::forget($query, $key);
        session(['our_wanted.filter' => $query]);

        return redirect(route('our-wanted.index'));
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
                $ourWanted = OurWanted::findOrFail($id);

                if (isset($request->origin)) {
                    $ourWanted->update(['origin' => $request->origin]);
                }

                if (isset($request->age_group)) {
                    $ourWanted->update(['age_group' => $request->age_group]);
                }

                if ($request->areas > 0) {
                    $ourWanted->area_regions()->sync($request->areas);
                }

                if (isset($request->add_to_wanted_lists)) {
                    $ourWanted->ourwanted_lists()->syncWithoutDetaching($request->add_to_wanted_lists);
                }

                if (isset($request->remove_from_wanted_lists)) {
                    $ourWanted->ourwanted_lists()->detach($request->remove_from_wanted_lists);
                }
            }
        }

        return response()->json();
    }

    /**
     * Generate pdf or html wanted list.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printOurWantedList(Request $request)
    {
        $document = $request->document_type;
        $language = $request->language;

        if ($request->export_option !== 'all') {
            $wantedToPrint = OurWanted::whereIn('id', $request->items)->get();
        } else {
            $result_array  = $this->get_records_by_filter(true);
            $wantedToPrint = $result_array['ourWanteds'];

            if ($document == 'pdf' && $wantedToPrint->count() > 300) {
                return response()->json(['success' => false, 'message' => 'You cannot print more than 300 records in PDF. Please, select HTML and convert HTML file to PDF.']);
            }
        }

        $wantedToPrint = $wantedToPrint->sortBy(function ($our_wanted, $key) {
            return [$our_wanted->animal->code_number, $our_wanted->animal->classification->order->common_name, $our_wanted->animal->scientific_name];
        });
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

    //Export excel document with standard wanted info.
    public function export(Request $request)
    {
        $file_name = 'Standard wanted list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $standard_wanteds = OurWanted::whereIn('id', explode(',', $request->items))->orderBy('created_at')->get();

        $export = new OurWantedsExport($standard_wanteds);

        return Excel::download($export, $file_name);
    }

    public function get_records_by_filter()
    {
        $orderByDirection      = null;
        $orderByField          = null;
        $ourWantedListSelected = null;
        $recordsPerPage        = 50;
        $filterData            = [];

        // Check if filter is set on session
        if (session()->has('our_wanted.filter')) {
            $request = session('our_wanted.filter');
        }

        $ourWantedListSelected = null;
        if (isset($request['ourWantedListsTopSelect']) && $request['ourWantedListsTopSelect'] > 0) {
            $ourWantedListSelected = OurWantedList::find($request['ourWantedListsTopSelect']);

            $ourWanteds = $ourWantedListSelected->our_wanteds()->orderByDesc('updated_at');

            $filterData = Arr::add($filterData, 'ourWantedListsTopSelect', 'Wanted list: ' . $ourWantedListSelected->name);
        } else {
            $ourWanteds = OurWanted::with(['animal', 'area_regions'])->orderByDesc('updated_at');
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

                        $ourWanteds->where('animal_id', $filterAnimal->id);

                        $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
                    }
                } elseif ($request['filter_animal_option'] === 'by_name') {
                    if (isset($request['filter_animal_name'])) {
                        $ourWanteds->whereHas('animal', function ($query) use ($request) {
                            $query->where('common_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('common_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('scientific_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('scientific_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('spanish_name', 'like', '%' . $request['filter_animal_name'] . '%');
                        });

                        $filterData = Arr::add($filterData, 'filter_animal_name', 'Animal name: ' . $request['filter_animal_name']);
                    }
                } else {
                    $ourWanteds->whereNull('animal_id');

                    $filterData = Arr::add($filterData, 'filter_animal_option', 'Animal: empty');
                }
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
                    $ourWanteds->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $ourWanteds->whereHas('animal', function ($query) use ($ids_genera) {
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
                    $ourWanteds->whereHas('animal', function ($query) use ($ids_genera) {
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
                    $ourWanteds->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                }
            }

            if (isset($request['filter_remarks'])) {
                $ourWanteds->where('remarks', 'like', '%' . $request['filter_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_remarks', 'Remarks: ' . $request['filter_remarks']);
            }

            if (isset($request['filter_intern_remarks'])) {
                $ourWanteds->where('intern_remarks', 'like', '%' . $request['filter_intern_remarks'] . '%');

                $filterData = Arr::add($filterData, 'filter_intern_remarks', 'Intern remarks: ' . $request['filter_intern_remarks']);
            }

            if (isset($request['filter_updated_at_from'])) {
                $ourWanteds->whereDate('updated_at', '>=', $request['filter_updated_at_from']);

                $filterData = Arr::add($filterData, 'filter_updated_at_from', 'Updated start at: ' . $request['filter_updated_at_from']);
            }

            if (isset($request['filter_updated_at_to'])) {
                $ourWanteds->whereDate('updated_at', '<=', $request['filter_updated_at_to']);

                $filterData = Arr::add($filterData, 'filter_updated_at_to', 'Updated end at: ' . $request['filter_updated_at_to']);
            }

            if (isset($request['filter_areas_empty'])) {
                $ourWanteds->whereDoesntHave('area_regions');

                $filterData = Arr::add($filterData, 'filter_areas_empty', 'Offer to: empty');
            } elseif (isset($request['filter_area_id'])) {
                $filter_areas = $request['filter_area_id'];

                $filterAreas = AreaRegion::whereIn('id', $filter_areas)->get();

                $ourWanteds->whereHas('area_regions', function ($query) use ($filter_areas) {
                    $query->whereIn('area_region_id', $filter_areas);
                });

                $areasLabel = '';
                foreach ($filterAreas as $filterArea) {
                    $areasLabel .= $filterArea->short_cut . '-';
                }

                $filterData = Arr::add($filterData, 'filter_area_id', 'Wanted to: ' . trim($areasLabel));
            }
        }

        $ourWanteds = $ourWanteds->get();
        //dump(DB::getQueryLog());

        if (isset($request)) {
            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByField == 'common_name' || $orderByField == 'scientific_name') {
                    if ($orderByDirection == 'desc') {
                        $ourWanteds = $ourWanteds->sortByDesc(function ($our_wanted, $key) use ($orderByField) {
                            return $our_wanted->animal->$orderByField;
                        });
                    } else {
                        $ourWanteds = $ourWanteds->sortBy(function ($our_wanted, $key) use ($orderByField) {
                            return $our_wanted->animal->$orderByField;
                        });
                    }
                } else {
                    if ($orderByDirection == 'desc') {
                        $ourWanteds = $ourWanteds->sortByDesc(function ($our_wanted, $key) use ($orderByField) {
                            return $our_wanted->$orderByField;
                        });
                    } else {
                        $ourWanteds = $ourWanteds->sortBy(function ($our_wanted, $key) use ($orderByField) {
                            return $our_wanted->$orderByField;
                        });
                    }
                }
            }
        }

        return [
            'filterData'            => $filterData,
            'ourWanteds'            => $ourWanteds,
            'orderByDirection'      => $orderByDirection,
            'orderByField'          => $orderByField,
            'ourWantedListSelected' => $ourWantedListSelected,
            'recordsPerPage'        => $recordsPerPage,
        ];
    }
}
