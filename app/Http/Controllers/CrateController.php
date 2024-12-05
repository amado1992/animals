<?php

namespace App\Http\Controllers;

use App\Enums\CrateOrderByOptions;
use App\Enums\Currency;
use App\Exports\CratesExport;
use App\Http\Requests\CrateCreateRequest;
use App\Http\Requests\CrateUpdateRequest;
use App\Models\Animal;
use App\Models\Cites;
use App\Models\Classification;
use App\Models\Crate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CrateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $crates = Crate::where('iata_code', '<>', 0);

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $orderByOptions   = CrateOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('crate.filter')) {
            $request = session('crate.filter');

            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_crate_name'])) {
                $crates->where('name', 'like', '%' . $request['filter_crate_name'] . '%');

                $filterData = Arr::add($filterData, 'filter_crate_name', 'Name: ' . $request['filter_crate_name']);
            }

            if (isset($request['filter_crate_iata_number'])) {
                $crates->where('iata_code', $request['filter_crate_iata_number']);

                $filterData = Arr::add($filterData, 'filter_crate_iata_number', 'Iata: ' . $request['filter_crate_iata_number']);
            }

            if (isset($request['filter_crate_animal_id'])) {
                $filterAnimal = Animal::where('id', $request['filter_crate_animal_id'])->first();

                $crates->whereHas('animals', function ($query) use ($filterAnimal) {
                    $query->where('animals.id', $filterAnimal->id);
                });

                $filterData = Arr::add($filterData, 'filter_crate_animal_id', 'Animal: ' . $filterAnimal->common_name);
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
                    $crates->whereHas('animals', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $crates->whereHas('animals', function ($query) use ($ids_genera) {
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
                    $crates->whereHas('animals', function ($query) use ($ids_genera) {
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
                    $crates->whereHas('animals', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    });
                }
            }

            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                $crates->orderBy($orderByField, $orderByDirection);
            }

            if (isset($request) && isset($request['recordsPerPage'])) {
                $crates = $crates->paginate($request['recordsPerPage']);
            } else {
                $crates = $crates->paginate(20);
            }

            if (isset($request['filter_crate_has_files']) && $request['filter_crate_has_files'] != 'all') {
                $index = 0;
                foreach ($crates as $crate) {
                    $files = Storage::allFiles('public/crates_docs/' . $crate->id);

                    if ($request['filter_crate_has_files'] == 'yes') {
                        if ($files == null || count($files) == 0) {
                            Arr::forget($crates, $index);
                        }
                    } else {
                        if ($files != null && count($files) > 0) {
                            Arr::forget($crates, $index);
                        }
                    }

                    $index++;
                }

                $filterData = Arr::add($filterData, 'filter_crate_has_files', 'Has files: ' . $request['filter_crate_has_files']);
            }
        //dump(DB::getQueryLog()); // Show results of log
        } else {
            $crates = $crates->orderByDesc('updated_at')->paginate(20);
        }

        return view('crates.index', compact(
            'crates',
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
        session()->forget('crate.filter');

        return redirect(route('crates.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::get();

        return view('crates.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CrateCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CrateCreateRequest $request)
    {
        Crate::create($request->all());

        return redirect(route('crates.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Crate  $crate
     * @return \Illuminate\Http\Response
     */
    public function show(Crate $crate)
    {
        $animals = $crate->animals()->paginate(5);

        return view('crates.show', compact('crate', 'animals'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $crate = Crate::findOrFail($id);

        $currencies = Currency::get();

        return view('crates.edit', compact('crate', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CrateUpdateRequest  $request
     * @param  \App\Models\Crate  $crate
     * @return \Illuminate\Http\Response
     */
    public function update(CrateUpdateRequest $request, Crate $crate)
    {
        if ($crate->cost_price != $request->cost_price) {
            $request['cost_price_changed'] = true;
        }
        if ($crate->sale_price != $request->sale_price) {
            $request['sale_price_changed'] = true;
        }

        $crate->update($request->all());

        return redirect(route('crates.show', [$crate->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Crate  $crate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Crate $crate)
    {
        Storage::deleteDirectory('public/crates_docs/' . $crate->id);
        $crate->delete();

        return redirect(route('crates.index'));
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
                $deleteItem = Crate::findOrFail($id);
                Storage::deleteDirectory('public/crates_docs/' . $deleteItem->id);
                $deleteItem->delete();
            }
        }

        return response()->json();
    }

    /**
     * Filter crates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterCrates(Request $request)
    {
        // Set session crate filter
        session(['crate.filter' => $request->query()]);

        return redirect(route('crates.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('crate.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['crate.filter' => $query]);

        return redirect(route('crates.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('crate.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['crate.filter' => $query]);

        return redirect(route('crates.index'));
    }

    /**
     * Remove from crate session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromCrateSession($key)
    {
        $query = session('crate.filter');
        Arr::forget($query, $key);
        session(['crate.filter' => $query]);

        return redirect(route('crates.index'));
    }

    //Upload crate file
    public function upload_file(Request $request)
    {
        if ($request->hasFile('file')) {
            $crate = Crate::findOrFail($request->crateId);

            $file = $request->file('file');

            //File Name
            $file_name = $file->getClientOriginalName();

            $path = Storage::putFileAs('public/crates_docs/' . $crate->id, $file, $file_name);
        }

        //return redirect()->back()->with('status', 'Successfully uploaded file');
        return response()->json(['success' => true], 200);
    }

    /**
     * Delete crate file.
     *
     * @param  int id
     * @param  string file_name
     * @return \Illuminate\Http\Response
     */
    public function delete_file($crate_id, $file_name)
    {
        Storage::delete('public/crates_docs/' . $crate_id . '/' . $file_name);

        return redirect()->back();
    }

    //Export excel document with crates info.
    public function export(Request $request)
    {
        $file_name = 'Crates list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $export = new CratesExport(Crate::whereIn('id', explode(',', $request->items))->orderBy('name')->get());

        return Excel::download($export, $file_name);
    }

    /**
     * Add species to crate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addSpeciesToCrate(Request $request)
    {
        $crate = Crate::findOrFail($request->crateId);
        if (count($request->items) > 0) {
            $crate->animals()->syncWithoutDetaching($request->items);
        }

        return response()->json(['url' => route('crates.show', [$crate->id])]);
    }

    /**
     * Delete species from crate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteSpeciesFromCrate(Request $request)
    {
        $crate = Crate::findOrFail($request->id);
        if (count($request->items) > 0) {
            $crate->animals()->detach($request->items);
        }

        return response()->json(['url' => route('crates.show', [$crate->id])]);
    }

    /**
     * Get crates by iata.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCratesByIata(Request $request)
    {
        $crates = collect();
        if ($request->iata != 0) {
            $result = Crate::where('iata_code', $request->iata)->orderBy('name')->get();

            foreach ($result as $crate) {
                $text = $crate->name . ' - ' . $crate->full_dimensions;
                $crates->put($crate->id, $text);
            }
        }

        return response()->json(['crates' => $crates->toArray()]);
    }
}
