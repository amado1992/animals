<?php

namespace App\Http\Controllers;

use App\Enums\AnimalOrderByOptions;
use App\Exports\AnimalsExport;
use App\Http\Requests\AnimalCreateRequest;
use App\Http\Requests\AnimalUpdateRequest;
use App\Models\Animal;
use App\Models\Cites;
use App\Models\Classification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $animals = Animal::query();

        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        $orderByOptions   = AnimalOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('animals.filter')) {
            $request = session('animals.filter');
        }

        if (isset($request)) {
            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_animal_option'])) {
                if ($request['filter_animal_option'] === 'by_id') {
                    if (isset($request['filter_animal_id'])) {
                        $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                        $animals->where('id', $filterAnimal->id);

                        $filterData = Arr::add($filterData, 'filter_animal_id', 'Animal: ' . $filterAnimal->common_name);
                    }
                } else {
                    if (isset($request['filter_animal_name'])) {
                        $animals->where(function ($query) use ($request) {
                            $query->where('common_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('common_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('scientific_name', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('scientific_name_alt', 'like', '%' . $request['filter_animal_name'] . '%')
                                ->orWhere('spanish_name', 'like', '%' . $request['filter_animal_name'] . '%');
                        });

                        $filterData = Arr::add($filterData, 'filter_animal_name', 'Animal name: ' . $request['filter_animal_name']);
                    }
                }
            }

            if (isset($request['filter_class_id'])) {
                $ids_genera = [];

                $class      = Classification::where('id', $request['filter_class_id'])->first();
                $filterData = Arr::add($filterData, 'filter_class_id', 'Class: ' . $class->common_name);

                $order  = null;
                $family = null;
                $genus  = null;

                if(isset($request['filter_order_id'])) {
                    if(!empty($class->under)){
                        $order = $class->under->where('id', $request['filter_order_id'])->first();
                        $filterData = Arr::add($filterData, 'filter_order_id', 'Order: ' . $order->common_name);
                    }
                }
                if(isset($request['filter_family_id'])) {
                    if(!empty($class->under)){
                        $family = $order->under->where('id', $request['filter_family_id'])->first();
                        $filterData = Arr::add($filterData, 'filter_family_id', 'Family: ' . $order->common_name);
                    }
                }
                if(isset($request['filter_genus_id'])) {
                    if(!empty($class->under)){
                        $genus = $family->under->where('id', $request['filter_genus_id'])->first();
                        $filterData = Arr::add($filterData, 'filter_genus_id', 'Genus: ' . $order->common_name);
                    }
                }

                if ($genus != null) {
                    array_push($ids_genera, $genus->id);
                    $animals->whereIn('genus_id', $ids_genera);
                } elseif ($family != null) {
                    $genera = $family->under->toArray();
                    foreach ($genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                    $animals->whereIn('genus_id', $ids_genera);
                } elseif ($order != null) {
                    $families = $order->under->all();
                    foreach ($families as $order_family) {
                        $order_family_genera = $order_family->under->toArray();
                        foreach ($order_family_genera as $family_genus) {
                            array_push($ids_genera, $family_genus['id']);
                        }
                    }
                    $animals->whereIn('genus_id', $ids_genera);
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
                    $animals->whereIn('genus_id', $ids_genera);
                }
            }

            if (isset($request['filter_has_spanish_name']) && $request['filter_has_spanish_name'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_spanish_name', 'Spanish name: ' . $request['filter_has_spanish_name']);

                if ($request['filter_has_spanish_name'] == 'yes') {
                    $animals->whereNotNull('spanish_name')->where('spanish_name', '!=', '');
                } else {
                    $animals->whereNull('spanish_name')->orWhere('spanish_name', '');
                }
            }

            if (isset($request['filter_has_crates']) && $request['filter_has_crates'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_crates', 'Has crate: ' . $request['filter_has_crates']);

                if ($request['filter_has_crates'] == 'yes') {
                    $animals->has('crates');
                } else {
                    $animals->doesntHave('crates');
                }
            }

            if (isset($request['filter_in_standard_list']) && $request['filter_in_standard_list'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_in_standard_list', 'In stock: ' . $request['filter_in_standard_list']);

                if ($request['filter_in_standard_list'] == 'yes') {
                    if (isset($request['filter_in_standard_list_public'])) {
                        $filterData = Arr::add($filterData, 'filter_in_standard_list_public', 'Website public: ' . $request['filter_in_standard_list_public']);

                        if ($request['filter_in_standard_list_public'] == 'yes') {
                            $animals->whereHas('our_surpluses', function ($query) use ($request) {
                                $query->where('is_public', 1);
                            });
                        } else {
                            $animals->whereDoesntHave('our_surpluses', function ($query) use ($request) {
                                $query->where('is_public', 1);
                            });
                        }
                    } else {
                        $animals->has('our_surpluses');
                    }
                } else {
                    $animals->doesntHave('our_surpluses');
                }
            }

            if (isset($request['filter_in_surplus_of_suppliers']) && $request['filter_in_surplus_of_suppliers'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_in_surplus_of_suppliers', 'In surplus: ' . $request['filter_in_surplus_of_suppliers']);

                if ($request['filter_in_surplus_of_suppliers'] == 'yes') {
                    $animals->has('our_wanteds');
                } else {
                    $animals->doesntHave('our_wanteds');
                }
            }

            if (isset($request['filter_in_wanted_of_clients']) && $request['filter_in_wanted_of_clients'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_in_wanted_of_clients', 'In our wanted: ' . $request['filter_in_wanted_of_clients']);

                if ($request['filter_in_wanted_of_clients'] == 'yes') {
                    $animals->has('wanteds');
                } else {
                    $animals->doesntHave('wanteds');
                }
            }

            if (isset($request['filter_in_wanted_of_clients']) && $request['filter_in_wanted_of_clients'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_in_wanted_of_clients', 'In wanted: ' . $request['filter_in_wanted_of_clients']);

                if ($request['filter_in_wanted_of_clients'] == 'yes') {
                    $animals->has('wanteds');
                } else {
                    $animals->doesntHave('wanteds');
                }
            }

            if (isset($request['filter_in_offers']) && $request['filter_in_offers'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_in_offers', 'In offers: ' . $request['filter_in_offers']);

                if ($request['filter_in_offers'] == 'yes') {
                    $animals->whereHas('offers.offer', function ($query) use ($request) {
                        $query->where('offers.offer_status', '<>', 'Ordered');
                    });
                } else {
                    $animals->whereDoesntHave('offers.offer', function ($query) use ($request) {
                        $query->where('offers.offer_status', '<>', 'Ordered');
                    });
                }
            }

            if (isset($request['filter_has_catalog_picture']) && $request['filter_has_catalog_picture'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_catalog_picture', 'Catalog picture: ' . $request['filter_has_catalog_picture']);

                if ($request['filter_has_catalog_picture'] == 'yes') {
                    $animals->whereNotNull('catalog_pic');
                } else {
                    $animals->whereNull('catalog_pic');
                }
            }
        }

        if (isset($request) && isset($request['recordsPerPage']))
            $animals = $animals->paginate($request['recordsPerPage']);
        else
            $animals = $animals->paginate(20);

        if (isset($request)) {
            if (isset($request['filter_has_pictures']) && $request['filter_has_pictures'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_pictures', 'Has pictures: ' . $request['filter_has_pictures']);

                $index = 0;
                foreach ($animals as $animal) {
                    $files = Storage::allFiles('public/animals_pictures/' . $animal->id);

                    if ($request['filter_has_pictures'] == 'yes') {
                        if ($files == null || count($files) == 0) {
                            Arr::forget($animals, $index);
                        }
                    } else {
                        if ($files != null && count($files) > 0) {
                            Arr::forget($animals, $index);
                        }
                    }

                    $index++;
                }
            }

            if (isset($request['filter_has_pictures_less_10kb']) && $request['filter_has_pictures_less_10kb'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_pictures_less_10kb', 'Pictures less 10Kb: ' . $request['filter_has_pictures_less_10kb']);

                $index = 0;
                foreach ($animals as $animal) {
                    $files = Storage::allFiles('public/animals_pictures/' . $animal->id);

                    if ($request['filter_has_pictures_less_10kb'] == 'yes') {
                        $has_pictures_less_10kb = false;

                        foreach ($files as $file) {
                            if (Storage::size($file) <= 10240) {
                                $has_pictures_less_10kb = true;
                                break;
                            }
                        }

                        if (!$has_pictures_less_10kb) {
                            Arr::forget($animals, $index);
                        }
                    }

                    $index++;
                }
            }

            if (isset($request['filter_has_more_than_10_pictures']) && $request['filter_has_more_than_10_pictures'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_more_than_10_pictures', 'More than 10 pictures: ' . $request['filter_has_more_than_10_pictures']);

                $index = 0;
                foreach ($animals as $animal) {
                    $files = Storage::allFiles('public/animals_pictures/' . $animal->id);

                    if ($request['filter_has_more_than_10_pictures'] == 'yes') {
                        if ($files == null || count($files) < 10) {
                            Arr::forget($animals, $index);
                        }
                    }

                    $index++;
                }
            }

            if (isset($request['filter_has_wrong_size_catalog_pic']) && $request['filter_has_wrong_size_catalog_pic'] !== 'all') {
                $filterData = Arr::add(
                    $filterData,
                    'filter_has_wrong_size_catalog_pic',
                    'Wrong size catalog picture: ' . $request['filter_has_wrong_size_catalog_pic']
                );
                if ($request['filter_has_wrong_size_catalog_pic'] === 'yes') {
                    $index = 0;
                    foreach ($animals as $animal) {
                        $cover_path = implode(
                            '/',
                            [
                                __DIR__,
                                '../../../public/storage/animals_pictures',
                                $animal->id,
                                $animal->catalog_pic,
                            ]
                        );
                        if (is_file($cover_path)) {
                            $cover = getimagesize($cover_path);
                            if ($cover[0] === 500 && $cover[1] === 500) {
                                Arr::forget($animals, $index);
                            }
                        } else {
                            Arr::forget($animals, $index);
                        }
                        $index++;
                    }
                }
            }

            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByField == 'code_number') {
                    if ($orderByDirection == 'desc') {
                        $animals = $animals->orderByDesc('code_number', SORT_NUMERIC);
                    } else {
                        $animals = $animals->sortBy('code_number', SORT_NUMERIC);
                    }
                } else {
                    if ($orderByDirection == 'desc') {
                        $animals->orderByDesc($orderByField);
                    } else {
                        $animals->orderBy($orderByField);
                    }
                }
            }
        }

        return view('animals.index', compact(
            'animals',
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
        session()->forget('animals.filter');

        return redirect(route('animals.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cites_global = Cites::where('type', 'global')->orderBy('key')->pluck('key', 'key');
        $cites_europe = Cites::where('type', 'europe')->orderBy('key')->pluck('key', 'key');
        $classes      = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');
        //$genuses = Classification::where('rank', 'genus')->orderBy('common_name')->pluck('common_name', 'id');

        return view('animals.create', compact('classes', 'cites_global', 'cites_europe'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AnimalCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnimalCreateRequest $request)
    {
        $request['scientific_name_slug'] = str_replace(' ', '_', strtolower($request->scientific_name));
        $request['code_number']          = $request->code_number_temp . $request->code_number;

        $code_number = Animal::where('code_number', $request['code_number'])->first();

        if (!empty($code_number)) {
            return redirect()->back()->with('error', 'There is already an animal with that code number, change the last 4 values and try again.');
        }
        Animal::create($request->all());

        return redirect(route('animals.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function show(Animal $animal)
    {
        $files = Storage::allFiles('public/animals_pictures/' . $animal->id);

        $files_processed = [];
        $result          = [];
        foreach ($files as $file) {
            $file = pathinfo($file);

            $result['name'] = $file['basename'];

            $size = getimagesize(public_path() . '/storage/animals_pictures/' . $animal->id . '/' . $file['basename']);
            if ($size !== false) {
                $result['dimension'] = $size[0] . ' x ' . $size[1] . ' px';
                array_push($files_processed, $result);
            }
        }

        return view('animals.show', compact('animal', 'files_processed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function edit(Animal $animal)
    {
        $cites_global = Cites::where('type', 'global')->orderBy('key')->pluck('key', 'key');
        $cites_europe = Cites::where('type', 'europe')->orderBy('key')->pluck('key', 'key');
        $classes      = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');
        if (!empty($animal->classification->class)) {
            $orders = Classification::where('rank', 'order')->where('belongs_to', $animal->classification->class->id)->orderBy('common_name')->pluck('common_name', 'id');
        } else {
            $orders = [];
        }
        if (!empty($animal->classification->order)) {
            $families = Classification::where('rank', 'family')->where('belongs_to', $animal->classification->order->id)->orderBy('common_name')->pluck('common_name', 'id');
        } else {
            $families = [];
        }
        if (!empty($animal->classification->family)) {
            $genuses = Classification::where('rank', 'genus')->where('belongs_to', $animal->classification->family->id)->orderBy('common_name')->pluck('common_name', 'id');
        } else {
            $genuses = [];
        }

        $animal->code_number_temp = substr($animal->code_number, 0, 12);
        $animal->code_number      = substr($animal->code_number, 12, 16);

        return view('animals.edit', compact('animal', 'classes', 'orders', 'families', 'genuses', 'cites_global', 'cites_europe'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AnimalUpdateRequest  $request
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function update(AnimalUpdateRequest $request, Animal $animal)
    {
        $request['scientific_name_slug'] = $request->scientific_name;
        $request['code_number']          = $request->code_number_temp . $request->code_number;

        $code_number = Animal::where('code_number', $request['code_number'])->first();

        if(!empty($code_number) && $animal["code_number"] != $request['code_number']){
            return redirect()->back()->with('error', 'There is already an animal with that code number, change the last 4 values and try again.');
        }
        $animal->update($request->all());

        return redirect(route('animals.show', [$animal->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Animal $animal)
    {
        $validator = Validator::make($animal->toArray(), []);

        if ($animal->our_surpluses->count() > 0) {
            $validator->errors()->add('our_surpluses', 'The animal belongs to standard surplus records.');
        }
        if ($animal->surpluses->count() > 0) {
            $validator->errors()->add('surpluses', 'The animal belongs to surplus of suppliers.');
        }
        if ($animal->our_wanteds->count() > 0) {
            $validator->errors()->add('our_wanteds', 'The animal belongs to standard wanted records.');
        }
        if ($animal->wanteds->count() > 0) {
            $validator->errors()->add('wanteds', 'The animal belongs to wanted of clients.');
        }
        if ($animal->offers->count() > 0) {
            $validator->errors()->add('offers', 'The animal belongs to offers.');
        }

        if ($validator->errors()->count() > 0) {
            return redirect(route('animals.show', $animal))->withErrors($validator);
        } else {
            Storage::deleteDirectory('public/animals_pictures/' . $animal->id);
            $animal->delete();

            return redirect(route('animals.index'));
        }
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
            foreach ($request->items as $id) {
                $toDelete = true;

                $deleteItem = Animal::findOrFail($id);

                if ($deleteItem->our_surpluses->count() > 0) {
                    $toDelete = false;
                } elseif ($deleteItem->surpluses->count() > 0) {
                    $toDelete = false;
                } elseif ($deleteItem->our_wanteds->count() > 0) {
                    $toDelete = false;
                } elseif ($deleteItem->wanteds->count() > 0) {
                    $toDelete = false;
                } elseif ($deleteItem->offers->count() > 0) {
                    $toDelete = false;
                }

                if ($toDelete) {
                    Storage::deleteDirectory('public/animals_pictures/' . $deleteItem->id);
                    $deleteItem->delete();
                }
            }
        }

        return response()->json();
    }

    //Upload picture
    public function upload_picture(Request $request)
    {
        $request->validate([
            'upload_option'  => 'required',
            'file_to_upload' => 'image|mimes:jpeg,jpg,bmp,png',
        ]);

        if ($request->upload_option == 'catalog') {
            $request->validate([
                'file_to_upload' => 'dimensions:max_width=600,max_height=600',
            ]);
        }

        /*$validator = Validator::make($request->all(), [
            'upload_option' => 'required'
        ]);*/

        $animal = Animal::findOrFail($request->id);

        if ($request->hasFile('file_to_upload')) {
            $file = $request->file('file_to_upload');

            //File Name
            $file_name = $file->getClientOriginalName();

            $pattern = "/[ '-]/i";
            $request->validate([
                'file_name' => (preg_match($pattern, $file_name)) ? 'required' : '',
            ], [
                'file_name.required' => 'Image name must not contain spaces or strange characters.',
            ]);

            if ($request->upload_option == 'catalog') {
                Storage::delete('public/animals_pictures/' . $request->id . '/' . $animal->catalog_pic);
                $animal->update(['catalog_pic' => $file_name]);
            }

            $path = Storage::putFileAs(
                'public/animals_pictures/' . $request->id, $file, $file_name
            );
        }

        /*if ($validator->fails())
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);*/

        //return redirect(route('animals.show', $request->id));
        return response()->json(['success' => true]);
    }

    /**
     * Remove the selected pictures.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_pictures(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $file_name) {
                Storage::delete('public/animals_pictures/' . $request->id . '/' . $file_name);
                $animal = Animal::find($request->id);
                if(!empty($animal["catalog_pic"]) && $animal["catalog_pic"] == $file_name){
                    $animal["catalog_pic"] = null;
                    $animal->save();
                }
            }
        }

        return response()->json();
    }

     /**
     * Remove the selected pictures.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMainImage(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $file_name) {
                $animal = Animal::find($request->id);
                if(!empty($animal)){
                    $animal["catalog_pic"] = $file_name;
                    $animal->save();
                }
            }
        }

        return response()->json(['error' => false, 'message' => 'Updated main image successfully']);
    }

    /**
     * Filter animals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterAnimals(Request $request)
    {
        // Set session animals filter
        session(['animals.filter' => $request->query()]);

        return redirect(route('animals.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('animals.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['animals.filter' => $query]);

        return redirect(route('animals.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('animals.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['animals.filter' => $query]);

        return redirect(route('animals.index'));
    }

    /**
     * Remove from animal session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromAnimalsSession($key)
    {
        $query = session('animals.filter');
        Arr::forget($query, $key);
        session(['animals.filter' => $query]);

        return redirect(route('animals.index'));
    }

    //Export excel document with animals info.
    public function export(Request $request)
    {
        $file_name = 'Animals list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

        $export = new AnimalsExport(Animal::whereIn('id', explode(',', $request->items))->orderBy('common_name')->get());

        return Excel::download($export, $file_name);
    }

    /**
     * Assign crates to species.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignCratesToSpecies(Request $request)
    {
        $animal = Animal::findOrFail($request->animal_id);
        if ($request->crates_list && count($request->crates_list) > 0) {
            $animal->crates()->sync($request->crates_list);
        }

        return redirect(route('animals.show', [$animal->id]));
    }

    /**
     * Remove species crate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeSpeciesCrates(Request $request)
    {
        $animal = Animal::findOrFail($request->id);
        if (count($request->items) > 0) {
            $animal->crates()->detach($request->items);
        }

        return response()->json();
    }

    /**
     * Assign crates to species group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignCratesToSpeciesGroup(Request $request)
    {
        if (count($request->ids) > 0) {
            foreach ($request->ids as $id) {
                $animal = Animal::findOrFail($id);

                if (count($request->crates_list) > 0) {
                    $animal->crates()->sync($request->crates_list);
                }
            }
        }

        return response()->json();
    }

    /**
     * Manage animals classifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function manage_classifications()
    {
        $classes = Classification::where('rank', 'class')->orderBy('common_name')->pluck('common_name', 'id');

        return view('animals.manage_classifications', compact('classes'));
    }

    /**
     * updateZootierListe
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateZootierListe(Request $request)
    {
        $animal = Animal::find($request->idAnimal);

        if (!is_null($animal)) {
            $animal->update([$request->column => $request->value]);
        }

        $animals      = Animal::orderByDesc('updated_at');
        $ids_genera   = [];
        $classAnimal  = Classification::where('id', $animal->classification->class->id)->first();
        $orderAnimal  = $classAnimal->under->where('id', $animal->classification->order->id)->first();
        $familyAnimal = $orderAnimal->under->where('id', $animal->classification->family->id)->first();
        switch ($request->column) {
            case 'ztl_class':
                if ($classAnimal !== null) {
                    $orders = $classAnimal->under->all();
                    foreach ($orders as $class_order) {
                        $class_order_families = $class_order->under->all();
                        foreach ($class_order_families as $class_order_family) {
                            $class_order_family_genera = $class_order_family->under->toArray();
                            foreach ($class_order_family_genera as $family_genus) {
                                array_push($ids_genera, $family_genus['id']);
                            }
                        }
                    }
                    $animals->whereIn('genus_id', $ids_genera)->update([$request->column => $request->value]);
                }

                break;
            case 'ztl_order':
                $families = $orderAnimal->under->all();
                foreach ($families as $order_family) {
                    $order_family_genera = $order_family->under->toArray();
                    foreach ($order_family_genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                }
                $animals->whereIn('genus_id', $ids_genera)->update([$request->column => $request->value]);
                break;
            case 'ztl_family':
                $genera = $familyAnimal->under->toArray();
                foreach ($genera as $family_genus) {
                    array_push($ids_genera, $family_genus['id']);
                }
                $animals->whereIn('genus_id', $ids_genera)->update([$request->column => $request->value]);
                break;
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function verifiCodeNumber(Request $request)
    {
        if (!empty($request->value)) {
            $number = Animal::select('code_number')->where('code_number', 'LIKE', '%' . $request->value . '%')->max('code_number');

            if (!empty($number)) {
                $number = substr($number, -4, 4);
                $number = $number + 1;
            } else {
                $number = 0000;
            }

            return response()->json(['error' => false, 'number' => $number]);
        } else {
            return response()->json(['error' => false, 'message' => '']);
        }
    }
}
