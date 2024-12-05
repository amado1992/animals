<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\OurSurplus;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AnimalWebsiteController extends Controller
{
    /**
     * Get all standard surplus available.
     *
     * @return \Illuminate\Http\Response
     */
    public function allAvailableAnimals(Request $request)
    {
        $batchSize = 24;
        $skip      = $request->startat * $batchSize;
        $take      = $batchSize;

        $available_surpluses = OurSurplus::whereHas('animal')->where('is_public', 1)->orderByDesc('updated_at')->get();

        // sort on by option
        if ($request->filled('sorting') && $request->input('sorting') !== 'updated_at') {
            switch ($request->input('sorting')) {
                case 'common_name':
                    $sort_on = (!$request->filled('lang') || $request->input('lang') !== 'es' ? 'common_name' : 'spanish_name');
                    break;
                default:
                    $sort_on = $request->input('sorting');
                    break;
            }

            if ($request->filled('direction') && $request->input('direction') === 'desc') {
                $available_surpluses = $available_surpluses->sortByDesc(function ($surplus, $key) use ($sort_on) {
                    return $surplus->animal->{$sort_on};
                });
            } else {
                $available_surpluses = $available_surpluses->sortBy(function ($surplus, $key) use ($sort_on) {
                    return $surplus->animal->{$sort_on};
                });
            }
        }

        $total_specimens = OurSurplus::whereHas('animal')->where('is_public', 1)->count();

        $data = [];

        foreach ($available_surpluses->skip($skip)->take($take) as $surplus) {
            $ids_genera = [];

            $genus  = $surplus->animal->classification;
            $family = $genus->above;
            $order  = $family->above;
            $class  = $order->above;

            $record = [
                'total_specimens'      => $total_specimens,
                'id'                   => $surplus->id,
                'class_name'           => $class->common_name_slug,
                'order_name'           => $order->common_name_slug,
                'common_name'          => $surplus->animal->common_name,
                'scientific_name'      => $surplus->animal->scientific_name,
                'scientific_name_slug' => $surplus->animal->scientific_name_slug,
                'spanish_name'         => $surplus->animal->spanish_name,
                'available'            => [
                    'option' => $surplus->availability_field,
                ],
                'description' => $surplus->complete_remarks,
                'located_at'  => $surplus->location,
            ];

            $images = [];
            $files  = Storage::allFiles('public/animals_pictures/' . $surplus->animal->id);
            foreach ($files as $file) {
                $file = pathinfo($file);

                array_push($images, [
                    'src'                  => asset('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                    'alt'                  => $surplus->animal->common_name,
                    'is_cover_old_website' => ($file['basename'] == $surplus->animal->catalog_pic) ? true : false,
                    'is_cover_new_website' => (strpos($file['basename'], 'newWebsite') !== false) ? true : false,
                ]);
            }
            $record['images'] = $images;

            array_push($data, $record);
        }

        return response()->json($data);
    }

    /**
     * Get the orders related with a class.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getClassificationClassMenu(Request $request)
    {
        $classAmphibians = null;
        if ($request->class == 'reptiles-amphibians') {
            $class           = Classification::where('common_name_slug', 'reptiles')->first();
            $classAmphibians = Classification::where('common_name_slug', 'amphibians')->first();
        } else {
            $class = Classification::where('common_name_slug', $request->class)->first();
        }

        $data = [];
        if ($class) {
            $orders = $class->under->all();

            $orders = array_values(Arr::sort($orders, function ($value) {
                return $value['common_name'];
            }));

            foreach ($orders as $order) {
                array_push($data, [
                    'common_name'         => $order->common_name,
                    'common_name_slug'    => $order->common_name_slug,
                    'common_name_spanish' => ($order->common_name_spanish != null) ? $order->common_name_spanish : $order->common_name,
                    'scientific_name'     => $order->scientific_name,
                ]);
            }
        }

        if ($classAmphibians) {
            $orders = $classAmphibians->under->all();

            $orders = array_values(Arr::sort($orders, function ($value) {
                return $value['common_name'];
            }));

            foreach ($orders as $order) {
                array_push($data, [
                    'common_name'         => $order->common_name,
                    'common_name_slug'    => $order->common_name_slug,
                    'common_name_spanish' => ($order->common_name_spanish != null) ? $order->common_name_spanish : $order->common_name,
                    'scientific_name'     => $order->scientific_name,
                ]);
            }
        }

        $data = array_values(Arr::sort($data, function ($value) {
            return $value['common_name'];
        }));

        return response()->json($data);
    }

    /**
     * Get all standard surplus available by order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function availableAnimalsByOrder(Request $request)
    {
        $batchSize = 24;
        $skip      = $request->startat * $batchSize;
        $take      = $batchSize;

        $ids_genera      = [];
        $class           = null;
        $classAmphibians = null;
        $order           = null;

        if ($request->slug == 'reptiles-amphibians') {
            $class           = Classification::where('common_name_slug', 'reptiles')->first();
            $classAmphibians = Classification::where('common_name_slug', 'amphibians')->first();
        } elseif (strpos($request->slug, '/') == false) {
            $class = Classification::where('common_name_slug', $request->slug)->first();
        } else {
            $orderCommonNameSlug = substr($request->slug, strpos($request->slug, '/') + 1);
            $order               = Classification::where('common_name_slug', $orderCommonNameSlug)->first();
        }

        if ($class) {
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
        }

        if ($classAmphibians) {
            $orders = $classAmphibians->under->all();
            foreach ($orders as $class_order) {
                $class_order_families = $class_order->under->all();
                foreach ($class_order_families as $class_order_family) {
                    $class_order_family_genera = $class_order_family->under->toArray();
                    foreach ($class_order_family_genera as $family_genus) {
                        array_push($ids_genera, $family_genus['id']);
                    }
                }
            }
        }

        if ($order) {
            $families = $order->under->all();
            foreach ($families as $order_family) {
                $order_family_genera = $order_family->under->toArray();
                foreach ($order_family_genera as $family_genus) {
                    array_push($ids_genera, $family_genus['id']);
                }
            }
        }

        $total_specimens = OurSurplus::where('is_public', 1)->whereHas('animal', function ($query) use ($ids_genera) {
            $query->whereIn('genus_id', $ids_genera);
        })->count();

        $available_surpluses = OurSurplus::where('is_public', 1)
            ->whereHas('animal', function ($query) use ($ids_genera) {
                $query->whereIn('genus_id', $ids_genera);
            })
            ->orderByDesc('updated_at')
            ->get();

        // sort on by option
        if ($request->filled('sorting') && $request->input('sorting') !== 'updated_at') {
            switch ($request->input('sorting')) {
                case 'common_name':
                    $sort_on = (!$request->filled('lang') || $request->input('lang') !== 'es' ? 'common_name' : 'spanish_name');
                    break;
                default:
                    $sort_on = $request->input('sorting');
                    break;
            }

            if ($request->filled('direction') && $request->input('direction') === 'desc') {
                $available_surpluses = $available_surpluses->sortByDesc(function ($surplus, $key) use ($sort_on) {
                    return $surplus->animal->{$sort_on};
                });
            } else {
                $available_surpluses = $available_surpluses->sortBy(function ($surplus, $key) use ($sort_on) {
                    return $surplus->animal->{$sort_on};
                });
            }
        }

        $data = [];
        foreach ($available_surpluses->skip($skip)->take($take) as $surplus) {
            $surplusAnimalGenus  = $surplus->animal->classification;
            $surplusAnimalFamily = $surplusAnimalGenus->above;
            $surplusAnimalOrder  = $surplusAnimalFamily->above;
            $surplusAnimalClass  = $surplusAnimalOrder->above;

            $record = [
                'id'              => $surplus->id,
                'total_specimens' => $total_specimens,
                'class_name'      => $surplusAnimalClass->common_name_slug,
                'order_name'      => $surplusAnimalOrder->common_name_slug,
                'common_name'     => $surplus->animal->common_name,
                'scientific_name' => $surplus->animal->scientific_name,
                'spanish_name'    => $surplus->animal->spanish_name,
                'available'       => [
                    'option' => $surplus->availability_field,
                ],
                'description' => $surplus->origin,
                'located_at'  => $surplus->location,
            ];

            $images = [];
            $files  = Storage::allFiles('public/animals_pictures/' . $surplus->animal->id);
            foreach ($files as $file) {
                $file = pathinfo($file);

                array_push($images, [
                    'src'                  => asset('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                    'alt'                  => $surplus->animal->common_name,
                    'is_cover_old_website' => ($file['basename'] == $surplus->animal->catalog_pic) ? true : false,
                    'is_cover_new_website' => (strpos($file['basename'], 'newWebsite') !== false) ? true : false,
                ]);
            }
            $record['images'] = $images;

            array_push($data, $record);
        }

        return response()->json($data);
    }

    /**
     * Get available surplus by search criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function availableAnimalsBySearchCriteria(Request $request)
    {
        $available_surpluses = OurSurplus::where('is_public', 1)
            ->whereHas('animal', function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('scientific_name', 'like', '%' . $request->q . '%')
                        ->orWhere('common_name', 'like', '%' . $request->q . '%');
                });
            })
            ->get();

        $data = [];
        foreach ($available_surpluses as $surplus) {
            $surplusAnimalGenus  = $surplus->animal->classification;
            $surplusAnimalFamily = $surplusAnimalGenus->above;
            $surplusAnimalOrder  = $surplusAnimalFamily->above;
            $surplusAnimalClass  = $surplusAnimalOrder->above;

            if ($surplusAnimalClass['common_name_slug'] == 'reptiles') {
                $surplusAnimalClass['common_name_slug'] = 'reptiles-amphibians';
            }

            if ($surplusAnimalClass['common_name_slug'] == 'amphibians') {
                $surplusAnimalClass['common_name_slug'] = 'reptiles-amphibians';
            }

            $record = [
                'id'                   => $surplus->id,
                'class_name'           => $surplusAnimalClass->common_name_slug,
                'order_name'           => $surplusAnimalOrder->common_name_slug,
                'common_name'          => $surplus->animal->common_name,
                'scientific_name'      => $surplus->animal->scientific_name,
                'scientific_name_slug' => $surplus->animal->scientific_name_slug,
                'spanish_name'         => $surplus->animal->spanish_name,
                'available'            => [
                    'option' => $surplus->availability_field,
                ],
                'description' => $surplus->complete_remarks,
                'located_at'  => $surplus->location,
            ];

            $images = [];
            $files  = Storage::allFiles('public/animals_pictures/' . $surplus->animal->id);
            foreach ($files as $file) {
                $file = pathinfo($file);

                array_push($images, [
                    'src'                  => asset('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                    'alt'                  => $surplus->animal->common_name,
                    'is_cover_old_website' => ($file['basename'] == $surplus->animal->catalog_pic) ? true : false,
                    'is_cover_new_website' => (strpos($file['basename'], 'newWebsite') !== false) ? true : false,
                ]);
            }
            $record['images'] = $images;

            array_push($data, $record);
        }

        $data = array_values(Arr::sort($data, function ($value) {
            return $value['scientific_name'];
        }));

        return response()->json($data);
    }

    /**
     * Get surplus info by id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function availableAnimalById(Request $request)
    {
        $surplus = OurSurplus::where('id', $request->surplus_id)->first();

        if ($surplus) {
            $ids_genera = [];

            $genus = $surplus->animal->classification;
            if ($genus) {
                $family = $genus->above;
                $order  = $family->above;
                $class  = $order->above;

                $family_genuses = $family->under->all();
                foreach ($family_genuses as $genus) {
                    array_push($ids_genera, $genus['id']);
                }
            }

            $related_animals = [];
            $related_surplus = [];
            if (count($ids_genera) > 0) {
                $related_surplus = OurSurplus::where('is_public', 1)
                    ->where('animal_id', '<>', $surplus->animal->id)
                    ->whereHas('animal', function ($query) use ($ids_genera) {
                        $query->whereIn('genus_id', $ids_genera);
                    })->get();
            }

            if (count($related_surplus) > 0) {
                foreach ($related_surplus as $related) {
                    $relatedSurplusGenus = $related->animal->classification;
                    if ($genus) {
                        $relatedSurplusFamily = $relatedSurplusGenus->above;
                        $relatedSurplusOrder  = $relatedSurplusFamily->above;
                        $relatedSurplusClass  = $relatedSurplusOrder->above;
                    }

                    $relatedFiles       = Storage::allFiles('public/animals_pictures/' . $related->animal->id);
                    $foundImgNewWebsite = false;
                    $imgNewWebsite      = '';
                    foreach ($relatedFiles as $file) {
                        $file = pathinfo($file);

                        if (strpos($file['basename'], 'newWebsite') !== false) {
                            $foundImgNewWebsite = true;
                            $imgNewWebsite      = $file['basename'];
                        }
                    }

                    array_push($related_animals, [
                        'surplus_id'             => $related->id,
                        'class_common_name'      => $relatedSurplusClass->common_name,
                        'class_common_name_slug' => $relatedSurplusClass->common_name_slug,
                        'order_common_name'      => $relatedSurplusOrder->common_name,
                        'order_common_name_slug' => $relatedSurplusOrder->common_name_slug,
                        'common_name'            => $related->animal->common_name,
                        'scientific_name'        => $related->animal->scientific_name,
                        'scientific_name_slug'   => $related->animal->scientific_name_slug,
                        'spanish_name'           => $related->animal->spanish_name,
                        'img_src'                => ($foundImgNewWebsite) ? asset('storage/animals_pictures/' . $related->animal->id . '/' . $imgNewWebsite) : asset('storage/animals_pictures/' . $related->animal->id . '/' . $related->animal->catalog_pic),
                        'img_alt'                => $related->animal->common_name,
                    ]);
                }
            }

            $data = [
                'animal_row_id'             => $surplus->animal->id,
                'class_common_name'         => $class->common_name,
                'class_common_name_slug'    => $class->common_name_slug,
                'order_common_name'         => $order->common_name,
                'order_common_name_slug'    => $order->common_name_slug,
                'order_common_name_spanish' => ($order->common_name_spanish != null) ? $order->common_name_spanish : $order->common_name,
                'common_name'               => $surplus->animal->common_name,
                'scientific_name'           => $surplus->animal->scientific_name,
                'scientific_name_slug'      => $surplus->animal->scientific_name_slug,
                'spanish_name'              => $surplus->animal->spanish_name,
                'available'                 => [
                    'surplus_id' => $surplus->id,
                    'option'     => $surplus->availability_field,
                ],
                'description' => $surplus->complete_remarks,
                'located_at'  => $surplus->location,
            ];

            $surplusses               = [];
            $surplussesWithSameAnimal = OurSurplus::whereHas('animal')
                ->where('is_public', 1)
                ->where('id', '<>', $surplus->id)
                ->where('animal_id', $surplus->animal->id)
                ->get();
            foreach ($surplussesWithSameAnimal as $surplusWithSameAnimal) {
                array_push($surplusses, [
                    'available' => [
                        'surplus_id' => $surplusWithSameAnimal->id,
                        'option'     => $surplusWithSameAnimal->availability_field,
                    ],
                    'description' => $surplusWithSameAnimal->complete_remarks,
                    'located_at'  => $surplusWithSameAnimal->location,
                ]);
            }
            $data['surplusses'] = $surplusses;

            $images = [];
            $files  = Storage::allFiles('public/animals_pictures/' . $surplus->animal->id);
            foreach ($files as $file) {
                $file = pathinfo($file);

                array_push($images, [
                    'src'                  => asset('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                    'alt'                  => $surplus->animal->common_name,
                    'is_cover_old_website' => ($file['basename'] == $surplus->animal->catalog_pic) ? true : false,
                    'is_cover_new_website' => (strpos($file['basename'], 'newWebsite') !== false) ? true : false,
                ]);
            }
            $data['images'] = $images;

            $data['related_surplus'] = $related_animals;

            return response()->json($data);
        } else {
            return response()->json([]);
        }
    }

    /**
     * Get surplus info by slug.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function availableAnimalBySlug(Request $request)
    {
        if (trim($request->slug) != '') {
            $animalScientificNameSlug = substr($request->slug, strpos($request->slug, '/') + 1);
            $surplus                  = OurSurplus::where('is_public', 1)
                ->whereHas('animal', function ($query) use ($animalScientificNameSlug) {
                    $query->where('scientific_name_slug', $animalScientificNameSlug);
                })->first();

            if ($surplus) {
                $ids_genera = [];

                $genus = $surplus->animal->classification;
                if ($genus) {
                    $family = $genus->above;
                    $order  = $family->above;
                    $class  = $order->above;

                    $family_genuses = $family->under->all();
                    foreach ($family_genuses as $genus) {
                        array_push($ids_genera, $genus['id']);
                    }
                }

                $related_animals = [];
                $related_surplus = [];
                if (count($ids_genera) > 0) {
                    $related_surplus = OurSurplus::where('is_public', 1)
                        ->where('animal_id', '<>', $surplus->animal->id)
                        ->whereHas('animal', function ($query) use ($ids_genera) {
                            $query->whereIn('genus_id', $ids_genera);
                        })->get();
                }

                if (count($related_surplus) > 0) {
                    foreach ($related_surplus as $related) {
                        $relatedSurplusGenus = $related->animal->classification;
                        if ($genus) {
                            $relatedSurplusFamily = $relatedSurplusGenus->above;
                            $relatedSurplusOrder  = $relatedSurplusFamily->above;
                            $relatedSurplusClass  = $relatedSurplusOrder->above;
                        }

                        $relatedFiles       = Storage::allFiles('public/animals_pictures/' . $related->animal->id);
                        $foundImgNewWebsite = false;
                        $imgNewWebsite      = '';
                        foreach ($relatedFiles as $file) {
                            $file = pathinfo($file);

                            if (strpos($file['basename'], 'newWebsite') !== false) {
                                $foundImgNewWebsite = true;
                                $imgNewWebsite      = $file['basename'];
                            }
                        }

                        array_push($related_animals, [
                            'surplus_id'             => $related->id,
                            'class_common_name'      => $relatedSurplusClass->common_name,
                            'class_common_name_slug' => $relatedSurplusClass->common_name_slug,
                            'order_common_name'      => $relatedSurplusOrder->common_name,
                            'order_common_name_slug' => $relatedSurplusOrder->common_name_slug,
                            'common_name'            => $related->animal->common_name,
                            'scientific_name'        => $related->animal->scientific_name,
                            'scientific_name_slug'   => $related->animal->scientific_name_slug,
                            'spanish_name'           => $related->animal->spanish_name,
                            'img_src'                => ($foundImgNewWebsite) ? asset('storage/animals_pictures/' . $related->animal->id . '/' . $imgNewWebsite) : asset('storage/animals_pictures/' . $related->animal->id . '/' . $related->animal->catalog_pic),
                            'img_alt'                => $related->animal->common_name,
                        ]);
                    }
                }

                $data = [
                    'animal_row_id'             => $surplus->animal->id,
                    'class_common_name'         => $class->common_name,
                    'class_common_name_slug'    => $class->common_name_slug,
                    'order_common_name'         => $order->common_name,
                    'order_common_name_slug'    => $order->common_name_slug,
                    'order_common_name_spanish' => ($order->common_name_spanish != null) ? $order->common_name_spanish : $order->common_name,
                    'common_name'               => $surplus->animal->common_name,
                    'scientific_name'           => $surplus->animal->scientific_name,
                    'scientific_name_slug'      => $surplus->animal->scientific_name_slug,
                    'spanish_name'              => $surplus->animal->spanish_name,
                    'available'                 => [
                        'surplus_id' => $surplus->id,
                        'option'     => $surplus->availability_field,
                    ],
                    'description' => $surplus->remarks,
                    'located_at'  => $surplus->location,
                ];

                $surplusses               = [];
                $surplussesWithSameAnimal = OurSurplus::whereHas('animal')
                    ->where('is_public', 1)
                    ->where('id', '<>', $surplus->id)
                    ->where('animal_id', $surplus->animal->id)
                    ->get();
                foreach ($surplussesWithSameAnimal as $surplusWithSameAnimal) {
                    array_push($surplusses, [
                        'available' => [
                            'surplus_id' => $surplusWithSameAnimal->id,
                            'option'     => $surplusWithSameAnimal->availability_field,
                        ],
                        'description' => $surplusWithSameAnimal->origin,
                        'remarks'     => $surplusWithSameAnimal->remarks,
                        'located_at'  => $surplusWithSameAnimal->location,
                    ]);
                }
                $data['surplusses'] = $surplusses;

                $images = [];
                $files  = Storage::allFiles('public/animals_pictures/' . $surplus->animal->id);
                foreach ($files as $file) {
                    $file = pathinfo($file);
                    if (
                        str_contains(
                            mime_content_type('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                            'image'
                        )
                    ) {
                        array_push($images, [
                            'src'                  => asset('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                            'alt'                  => $surplus->animal->common_name,
                            'is_cover_old_website' => ($file['basename'] == $surplus->animal->catalog_pic) ? true : false,
                            'is_cover_new_website' => (strpos($file['basename'], 'newWebsite') !== false) ? true : false,
                            'mime'                 => mime_content_type('storage/animals_pictures/' . $surplus->animal->id . '/' . $file['basename']),
                        ]);
                    }
                }
                $data['images'] = $images;

                $data['related_surplus'] = $related_animals;

                return response()->json($data);
            }
        }

        return response()->json([]);
    }

    /**
     * Generate inventory list from website.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInventoryList(Request $request)
    {
        try {
            if (isset($request->language)) {
                $language = $request->language;
            } else {
                $language = 'english';
            }

            $name = ($language == 'english') ? 'Inventory_list' : 'Lista_inventario';

            $fileName = $name . '.pdf';

            $url = Storage::url('inventory/' . $fileName);

            return response()->json(['success' => true, 'url' => URL::to($url), 'fileName' => $fileName]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error reading the file.']);
        }
    }
}
