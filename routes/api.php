<?php

use App\Models\Action;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Contact;
use App\Models\Country;
use App\Models\DomainNameLink;
use App\Models\GenericDomain;
use App\Models\Offer;
use App\Models\OfferAction;
use App\Models\OfferAirfreightPallet;
use App\Models\OfferSpecies;
use App\Models\OfferSpeciesAirfreight;
use App\Models\OfferSpeciesCrate;
use App\Models\OfferTransportTruck;
use App\Models\Order;
use App\Models\OrderAction;
use App\Models\Organisation;
use App\Models\OurSurplus;
use App\Models\Region;
use App\Models\Surplus;
use App\Models\Task;
use App\Models\Wanted;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/member-settings', 'ContactWebsiteController@getMemberSettings')->name('contact.getMemberSettings');
Route::post('/member-settings', 'ContactWebsiteController@updateMemberSettings')->name('contact.updateMemberSettings');
Route::post('/contact-us', 'ContactWebsiteController@contactUs')->name('contact.contact-us');
Route::middleware('auth:sanctum')->group(function () {
    //Route::get('/user', 'UserController@AuthRouteAPI');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/create-request', 'RequestWebsiteController@createRequest')->name('api.create-request');
    //Public website logout endpoint
    Route::post('/logout', 'ContactWebsiteController@logout')->name('contact.logout');
});

Route::middleware('guest')->group(function () {
    Route::post('/register', 'ContactWebsiteController@register')->name('contact.register');
    Route::post('/login', 'ContactWebsiteController@login')->name('contact.login');
    Route::post('/forgot-password', 'ContactWebsiteController@forgotPassword')->name('contact.password.email');
    //Route::get('/reset-password/{token}', 'ContactWebsiteController@resetPassword')->name('contact.password.reset');
    Route::post('/reset-password', 'ContactWebsiteController@updatePassword')->name('contact.password.update');
    Route::post('/contact-us-no-member', 'ContactWebsiteController@contactUsNoMember')->name('contact.contact-us-no-member');
});

Route::get('/available-animals', 'AnimalWebsiteController@allAvailableAnimals')->name('api.available-animals');
Route::get('/available-animals-by-order', 'AnimalWebsiteController@availableAnimalsByOrder')->name('api.available-animals-by-order');
Route::get('/available-animals-by-search', 'AnimalWebsiteController@availableAnimalsBySearchCriteria')->name('api.available-animals-by-search');
Route::get('/get-class-menu', 'AnimalWebsiteController@getClassificationClassMenu')->name('api.get-class-menu');
Route::get('/surplus-by-id', 'AnimalWebsiteController@availableAnimalById')->name('api.surplus-by-id');
Route::get('/surplus-by-slug', 'AnimalWebsiteController@availableAnimalBySlug')->name('api.surplus-by-slug');
Route::get('/inventory-list', 'AnimalWebsiteController@printInventoryList')->name('api.inventory-list');

// API endpoints for inside the CRM. These endpoints are protected by default 'auth' //

Route::middleware('auth')->group(function () {
    // Get animal by id
    Route::post('/animal-by-id', function (Request $request) {
        $animal = Animal::where('id', $request->id)->first();

        return response()->json(['animal' => $animal]);
    })->name('api.animal-by-id');

    Route::get('/animals', function (Request $request) {
        $animals = Animal::select('id', 'common_name', 'scientific_name', 'scientific_name_alt')->get();

        $data = collect();

        foreach ($animals as $animal) {
            $text = $animal->common_name . ' (' . $animal->scientific_name . ')';
            $data->put($text, $animal->id);
        }

        return $data->toArray();
    })->name('api.animals');

    // Get animals for select2 component
    Route::get('/animals-select2', function (Request $request) {
        switch ($request->type) {
            case 'filter_offer_species':
                $offersSpecies = OfferSpecies::with(['oursurplus.animal'])
                    ->whereHas('oursurplus.animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request->q . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('oursurplus.animal.id');

                $animals = [];
                foreach ($offersSpecies as $key => $offerSpecies) {
                    $animal = Animal::where('id', $key)->first();
                    array_push($animals, $animal);
                }
                break;
            case 'filter_order_species':
                $offersSpecies = OfferSpecies::with(['offer', 'oursurplus.animal'])
                    ->whereHas('offer', function ($query) {
                        $query->where('offer_status', 'Ordered');
                    })
                    ->whereHas('oursurplus.animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request->q . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('oursurplus.animal.id');

                $animals = [];
                foreach ($offersSpecies as $key => $orderSpecies) {
                    $animal = Animal::where('id', $key)->first();
                    array_push($animals, $animal);
                }
                break;
            case 'filter_std_surplus':
                $stdSurpluses = OurSurplus::with(['animal'])
                    ->whereHas('animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request->q . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('animal.id');

                $animals = [];
                foreach ($stdSurpluses as $key => $stdSurplus) {
                    $animal = Animal::where('id', $key)->first();
                    array_push($animals, $animal);
                }
                break;
            case 'filter_surplus_supplier':
                $surpluses = Surplus::with(['animal'])
                    ->whereHas('animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request->q . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('animal.id');

                $animals = [];
                foreach ($surpluses as $key => $surplus) {
                    $animal = Animal::where('id', $key)->first();
                    array_push($animals, $animal);
                }
                break;
            case 'filter_surplus_collection':
                $surplusesCollection = Surplus::with(['animal'])
                    ->where('surplus_status', 'collection')
                    ->whereHas('animal', function ($query) use ($request) {
                        $query->where('common_name', 'like', '%' . $request->q . '%')
                            ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('animal.id');

                $animals = [];
                foreach ($surplusesCollection as $key => $surplusCollection) {
                    $animal = Animal::where('id', $key)->first();
                    array_push($animals, $animal);
                }
                break;
            default:
                $animals = Animal::where(function ($query) use ($request) {
                    $query->where('common_name', 'like', '%' . $request->q . '%')
                        ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                })->orderBy('common_name')->get();
                break;
        }

        $total = count($animals);

        /*$array_object_results = [];
        foreach($animals as $animal)
            array_push($array_object_results, $animal);

        $currentPage = $request->page;
        $perPage = 10;
        $results = array_slice($array_object_results, $perPage * ($currentPage - 1), $perPage);*/

        return response()->json(['items' => $animals, 'total_count' => $total]);
    })->name('api.animals-select2');

    ///////////////////////////////////////////////////////////////////////////

    // Get contact by id
    Route::post('/contact-by-id', function (Request $request) {
        $contact = Contact::where('id', $request->id)->first();

        return response()->json(['contact' => $contact]);
    })->name('api.contact-by-id');

    // Check if contact already exist in database
    Route::post('/check-contact', function (Request $request) {
        if ($request->id != null) {
            $contact = Contact::withTrashed()
                ->with(['organisation'])
                ->where('id', '<>', $request->id)
                ->where('email', $request->email)
                ->first();
        } else {
            $contact = Contact::withTrashed()
                ->with(['organisation'])
                ->where('email', $request->email)
                ->first();
        }

        $matchedInstitutions = [];
        if ($request->email != null && $request->city != null) {
            $domain_name         = substr($request->email, strpos($request->email, '@') + 1);
            $matchedInstitutions = Organisation::where('domain_name', $domain_name)
                ->where('city', $request->city)
                ->orderBy('name')
                ->pluck('name', 'id');
        }

        return response()->json([
            'success'                  => true,
            'contact'                  => $contact,
            'matchedInstitutions'      => $matchedInstitutions,
            'matchedInstitutionsTotal' => count($matchedInstitutions),
        ]);
    })->name('api.check-contact');

    // Get contacts for autocomplete
    Route::get('/contacts', function () {
        $contacts = Contact::GetContacts()
            ->orderByDesc('updated_at')
            ->pluck('id', 'email');

        return $contacts->toArray();
    })->name('api.contacts');

    Route::get('/contacts-email', function () {
        $contacts = Contact::whereNotNull('email')
            ->orderBy('email')
            ->pluck('id', 'email');

        return $contacts->toArray();
    })->name('api.contacts-email');

    // Get contacts for select2 component
    Route::get('/contacts-select2', function (Request $request) {
        switch ($request->type) {
            case 'filter_offer_client':
                $offersClients = Offer::with(['client'])
                    ->whereHas('client', function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->q . '%')
                            ->orWhere('last_name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhereHas('organisation', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->q . '%');
                            });
                    })->get()->groupBy('client.id');

                if ($offersClients->count() > 0) {
                    $contacts = [];
                    foreach ($offersClients as $key => $offerClient) {
                        $contact = Contact::with(['organisation'])->where('id', $key)->first();
                        array_push($contacts, $contact);
                    }
                } else {
                    $offersClients = Offer::with(['organisation'])
                        ->whereHas('organisation', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->q . '%')
                                ->orWhere('email', 'like', '%' . $request->q . '%');
                        })->get()->groupBy('organisation.id');

                    $contacts = [];
                    foreach ($offersClients as $key => $offerClient) {
                        $contact = Organisation::where('id', $key)->first();
                        array_push($contacts, $contact);
                    }
                }

                break;
            case 'filter_offer_supplier':
                $offersSuppliers = Offer::with(['supplier'])
                    ->whereHas('supplier', function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->q . '%')
                            ->orWhere('last_name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhereHas('organisation', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->q . '%');
                            });
                    })->get()->groupBy('supplier.id');

                $contacts = [];
                foreach ($offersSuppliers as $key => $offerSupplier) {
                    $contact = Contact::with(['organisation'])->where('id', $key)->first();
                    array_push($contacts, $contact);
                }
                break;
            case 'filter_order_client':
                $ordersClients = Order::with(['client'])
                    ->whereHas('client', function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->q . '%')
                            ->orWhere('last_name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhereHas('organisation', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->q . '%');
                            });
                    })->get()->groupBy('client.id');

                $contacts = [];
                foreach ($ordersClients as $key => $orderClient) {
                    $contact = Contact::with(['organisation'])->where('id', $key)->first();
                    array_push($contacts, $contact);
                }
                break;
            case 'filter_order_supplier':
                $ordersSuppliers = Order::with(['supplier'])
                    ->whereHas('supplier', function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->q . '%')
                            ->orWhere('last_name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhereHas('organisation', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->q . '%');
                            });
                    })->get()->groupBy('supplier.id');

                $contacts = [];
                foreach ($ordersSuppliers as $key => $orderSupplier) {
                    $contact = Contact::with(['organisation'])->where('id', $key)->first();
                    array_push($contacts, $contact);
                }
                break;
            case 'filter_transport_offer':
                $contacts = Contact::with(['organisation'])
                    ->GetContacts()
                    ->where(function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->q . '%')
                            ->orWhere('last_name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhereHas('organisation', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->q . '%');
                            });
                    })
                    ->whereHas('organisation', function ($query) use ($request) {
                        $query->where('organisation_type', "TR");
                    })
                    ->orderBy('email')->get();
                //->where('mailing_category', 'all_mailings')
                break;
            default:
                $contacts = Contact::with(['organisation'])
                    ->GetContacts()
                    ->where(function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->q . '%')
                            ->orWhere('last_name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%')
                            ->orWhereHas('organisation', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->q . '%');
                            });
                    })
                    ->orderBy('email')->get();
                //->where('mailing_category', 'all_mailings')
                break;
        }

        $total = count($contacts);

        return response()->json(['items' => $contacts, 'total_count' => $total]);
    })->name('api.contacts-select2');

    Route::get('/contacts-select2-email', function (Request $request) {
        $contacts = Contact::with(['organisation'])
            ->GetContacts()
            ->where(function ($query) use ($request) {
                $query->orWhere('email', 'like', '%' . $request->q . '%')
                    ->orWhereHas('organisation', function ($query) use ($request) {
                        $query->where('email', 'like', '%' . $request->q . '%');
                    });
            })
            ->orderBy('email')->get();

        $total = count($contacts);

        return response()->json(['items' => $contacts, 'total_count' => $total]);
    })->name('api.contacts-select2-email');

    Route::get('/contacts-select2-filter-email', function (Request $request) {

        $contacts = Contact::GetContacts()
        ->select("id", "email", "first_name", "last_name")
        ->where('email', 'like', '%' . $request->q . '%')
        ->orderBy('email')
        ->get();

        $organisations = Organisation::select("id", "email", "name as organisation")
        ->where('email', 'like', '%' . $request->q . '%')
        ->orderBy('email')
        ->get();

        $contacts = array_merge($contacts->toArray(), $organisations->toArray());

        $total = count($contacts);

        return response()->json(['items' => $contacts, 'total_count' => $total]);
    })->name('api.contacts-select2-filter-email');

    ///////////////////////////////////////////////////////////////////////////

    // Get institution by id
    Route::post('/institution-by-id', function (Request $request) {
        $institution = Organisation::where('id', $request->id)->first();

        return response()->json(['institution' => $institution]);
    })->name('api.institution-by-id');

    // Get institution by name
    Route::post('/institution-by-name', function (Request $request) {
        $institution = Organisation::where('name', $request->name)->first();

        return response()->json(['institution' => $institution]);
    })->name('api.institution-by-name');

    // Check if insitution already exist in database
    Route::post('/check-institution', function (Request $request) {
        if ($request->id != null) {
            $institution = Organisation::where('id', '<>', $request->id)->where('email', $request->email)->first();
        } else {
            $institution = Organisation::where('email', $request->email)->first();
        }

        $domain_name = '';
        if ($request->email != null) {
            $domain_name          = substr($request->email, strpos($request->email, '@') + 1);
            $matchedGenericDomain = GenericDomain::where('domain', $domain_name)->first();
        }

        return response()->json([
            'success'        => true,
            'already_in'     => ($institution          != null) ? true : false,
            'generic_domain' => ($matchedGenericDomain != null) ? true : false,
            'domain_name'    => $domain_name,
        ]);
    })->name('api.check-institution');

    Route::get('/organizations', function (Request $request) {
        $organizations = Organisation::get();

        $data = collect();

        foreach ($organizations as $organization) {
            $text = $organization->name . ' (' . $organization->email . ')';
            $data->put($text, $organization->id);
        }

        return $data->toArray();
    })->name('api.organizations');

    // Get contacts for select2 component
    Route::get('/institutions-select2', function (Request $request) {
        switch ($request->type) {
            case 'filter_surplus_institution':
                $filteredSurpluses = Surplus::with(['organisation'])
                    ->whereHas('organisation', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('organisation.id');

                $institutions = [];
                foreach ($filteredSurpluses as $key => $filteredSurplus) {
                    $institution = Organisation::where('id', $key)->first();
                    array_push($institutions, $institution);
                }
                $total = count($institutions);
                break;
            case 'filter_surplus_collection_institution':
                $filteredSurpluses = Surplus::with(['organisation'])
                    ->where('surplus_status', 'collection')
                    ->whereHas('organisation', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('organisation.id');

                $institutions = [];
                foreach ($filteredSurpluses as $key => $filteredSurplus) {
                    $institution = Organisation::where('id', $key)->first();
                    array_push($institutions, $institution);
                }
                $total = count($institutions);
                break;
            case 'filter_wanted_institution':
                $filteredWanteds = Wanted::with(['organisation'])
                    ->whereHas('organisation', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->q . '%')
                            ->orWhere('email', 'like', '%' . $request->q . '%');
                    })->get()->groupBy('organisation.id');

                $institutions = [];
                foreach ($filteredWanteds as $key => $filteredWanted) {
                    $institution = Organisation::where('id', $key)->first();
                    array_push($institutions, $institution);
                }
                $total = count($institutions);
                break;
            default:
                $institutions = Organisation::where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->q . '%')
                        ->orWhere('email', 'like', '%' . $request->q . '%');
                })
                    ->orderBy('name')->get();

                $total = count($institutions);
                break;
        }

        return response()->json(['items' => $institutions, 'total_count' => $total]);
    })->name('api.institutions-select2');

    Route::get('/canonical-name-select2', function (Request $request) {
        if (!empty($request->email)) {
            $email  = $request->email ?? '';
            $email  = explode('@', $email);
            $domain = DomainNameLink::where('domain_name', $email[1])->first();
            if (!empty($domain)) {
                $institutions[0]['canonical_name'] = $domain['canonical_name'];
                $institutions[0]['name']           = '';
            } else {
                $domain       = DomainNameLink::select('canonical_name', 'domain_name')->where('canonical_name', 'like', '%' . $request->search . '%')->orderBy('canonical_name')->get();
                $institutions = Organisation::select('canonical_name', 'name')->where('canonical_name', 'like', '%' . $request->search . '%')->orderBy('canonical_name')->get();
                $institutions = array_merge($institutions->toArray(), $domain->toArray());
            }
        } else {
            $domain       = DomainNameLink::select('canonical_name', 'domain_name')->where('canonical_name', 'like', '%' . $request->search . '%')->orderBy('canonical_name')->get();
            $institutions = Organisation::select('canonical_name', 'name')->where('canonical_name', 'like', '%' . $request->search . '%')->orderBy('canonical_name')->get();
            $institutions = array_merge($institutions->toArray(), $domain->toArray());
        }
        $total = count($institutions);

        return response()->json(['items' => $institutions, 'total_count' => $total]);
    })->name('api.canonical-name-select2');

    Route::post('/institution-contacts', function (Request $request) {
        $organization = Organisation::with(['country'])->where('id', $request->value)->first();

        return response()->json(['contacts' => ($organization != null) ? $organization->contacts : [], 'organization' => $organization]);
    })->name('api.institution-contacts');

    Route::post('/contacts-country', function (Request $request) {
        $contacts = Contact::with(['country'])->where('id', $request->value)->first();

        return response()->json(['contacts' => ($contacts != null) ? $contacts : []]);
    })->name('api.contacts-country');

    // Institutions by domain and city
    Route::post('/institutions-by-domain-city', function (Request $request) {
        $matchedInstitutions = [];
        if ($request->email != null && $request->city != null) {
            $domain_name         = substr($request->email, strpos($request->email, '@') + 1);
            $matchedInstitutions = Organisation::where('domain_name', $domain_name)
                ->where('city', $request->city)
                ->orderBy('name')
                ->pluck('name', 'id');
        }

        $contact = null;
        if ($request->id != null) {
            $contact = Contact::with(['organisation'])->where('id', $request->id)->first();
        }

        return response()->json([
            'success'                  => true,
            'contact'                  => $contact,
            'matchedInstitutions'      => $matchedInstitutions,
            'matchedInstitutionsTotal' => count($matchedInstitutions),
        ]);
    })->name('api.institutions-by-domain-city');

    ///////////////////////////////////////////////////////////////////////////

    Route::get('/oursurplus', function (Request $request) {
        $our_surpluses = OurSurplus::join('animals', 'our_surplus.animal_id', '=', 'animals.id')
            ->join('regions', 'our_surplus.region_id', '=', 'regions.id')
            ->select('*', 'our_surplus.id as ourSurplusId', 'our_surplus.created_at as created_date')
            ->get();

        $data = collect();

        foreach ($our_surpluses as $os) {
            $text = ($os->region) ? $os->region->name . '<br>' : '';
            $text .= 'Sales prices in ' . $os->sale_currency . ' - ' . (($os->is_public) ? 'Published' : 'Not published') . '<br>';
            $text .= 'M: ' . number_format($os->salePriceM, 2, '.', '') . ' F: ' . number_format($os->salePriceF, 2, '.', '') . ' U: ' . number_format($os->salePriceU, 2, '.', '') . ' P: ' . number_format($os->salePriceP, 2, '.', '') . '<br>';
            $text .= 'Date: ' . date('F j, Y', strtotime($os->updated_at)) . '<br>';
            $text .= 'Availability: ' . $os->availability_field . ' - ' . $os->common_name . ' (' . $os->scientific_name . ')<br>';
            $text .= 'Remarks: ' . $os->complete_remarks;
            $data->put($text, $os->ourSurplusId);
        }

        return $data->toArray();
    })->name('api.oursurplus');

    // Get surplus in stock for select2 component
    Route::get('/surpluses-select2', function (Request $request) {
        $our_surpluses = OurSurplus::select('*', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as surplus_date'))
            ->with(['animal', 'region'])
            ->whereHas('animal', function ($query) use ($request) {
                $query->where('common_name', 'like', '%' . $request->q . '%')
                    ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
            })
            ->get();

        $result = $our_surpluses->toArray();

        $total = count($result);

        return response()->json(['items' => $result, 'total_count' => $total]);
    })->name('api.surpluses-select2');

    // Get surplus in stock for select2 component
    Route::get('/surpluses-filter-select2', function (Request $request) {
        if(!empty($request->region) && !empty($request->origin)){
            $our_surpluses = OurSurplus::select('*', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as surplus_date'))
                ->with(['animal', 'region'])
                ->whereHas('animal', function ($query) use ($request) {
                    $query->where('common_name', 'like', '%' . $request->q . '%')
                        ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                })
                ->whereHas('region', function ($query) use ($request) {
                    $query->where('id', $request->region);
                })
                ->where("origin", $request->origin)
                ->get();
        }else{
            $our_surpluses = OurSurplus::select('*', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as surplus_date'))
                ->with(['animal', 'region'])
                ->whereHas('animal', function ($query) use ($request) {
                    $query->where('common_name', 'like', '%' . $request->q . '%')
                        ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                })
                ->get();
        }


        $result = $our_surpluses->toArray();

        $total = count($result);

        return response()->json(['items' => $result, 'total_count' => $total]);
    })->name('api.surpluses-filter-select2');

    ///////////////////////////////////////////////////////////////////////////

    // Get offers-orders for select2 component
    Route::get('/offers-orders-select2', function (Request $request) {
        $items = [];
        if (isset($request->type)) {
            switch ($request->type) {
                case 'offer':
                    $items = Offer::select('*', DB::raw("CONCAT('OFFER: ', YEAR(created_at), '-', offer_number) as projectNumber"))
                        ->with(['client', 'supplier'])
                        ->where('offer_status', '<>', 'Ordered')
                        ->where(function ($query) use ($request) {
                            $query->whereHas('offer_species.oursurplus.animal', function ($query) use ($request) {
                                $query->where('common_name', 'like', '%' . $request->q . '%')
                                    ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                            })
                                ->orWhereHas('client', function ($query) use ($request) {
                                    $query->where('first_name', 'like', '%' . $request->q . '%')
                                        ->orWhere('last_name', 'like', '%' . $request->q . '%')
                                        ->orWhere('email', 'like', '%' . $request->q . '%');
                                })
                                ->orWhereHas('supplier', function ($query) use ($request) {
                                    $query->where('first_name', 'like', '%' . $request->q . '%')
                                        ->orWhere('last_name', 'like', '%' . $request->q . '%')
                                        ->orWhere('email', 'like', '%' . $request->q . '%');
                                });
                        })
                        ->orderByDesc(DB::raw('YEAR(created_at)'))
                        ->orderByDesc('offer_number')
                        ->get();
                    break;
                case 'order':
                    $items = Order::select('*', DB::raw("CONCAT('ORDER: ', YEAR(created_at), '-', order_number) as projectNumber"))
                        ->with(['client', 'supplier'])
                        ->orWhereHas('offer.offer_species.oursurplus.animal', function ($query) use ($request) {
                            $query->where('common_name', 'like', '%' . $request->q . '%')
                                ->orWhere('scientific_name', 'like', '%' . $request->q . '%');
                        })
                        ->orWhereHas('client', function ($query) use ($request) {
                            $query->where('first_name', 'like', '%' . $request->q . '%')
                                ->orWhere('last_name', 'like', '%' . $request->q . '%')
                                ->orWhere('email', 'like', '%' . $request->q . '%');
                        })
                        ->orWhereHas('supplier', function ($query) use ($request) {
                            $query->where('first_name', 'like', '%' . $request->q . '%')
                                ->orWhere('last_name', 'like', '%' . $request->q . '%')
                                ->orWhere('email', 'like', '%' . $request->q . '%');
                        })
                        ->orderByDesc(DB::raw('YEAR(created_at)'))
                        ->orderByDesc('order_number')
                        ->get();
                    break;
            }
        }

        $total = count($items);

        return response()->json(['items' => $items, 'total_count' => $total]);
    })->name('api.offers-orders-select2');

    // Get offer or order by id
    Route::post('/offer-order-by-id', function (Request $request) {
        $project = null;

        if ($request->type == 'offer') {
            $project = Offer::select('*', DB::raw("CONCAT('Offer: ', YEAR(created_at), '-', offer_number) as projectNumber"))
                ->where('id', $request->id)->first();
        } elseif ($request->type == 'order') {
            $project = Order::select('*', DB::raw("CONCAT('Order: ', YEAR(created_at), '-', order_number) as projectNumber"))
                ->where('id', $request->id)->first();
        }

        return response()->json(['project' => $project]);
    })->name('api.offer-order-by-id');

    ///////////////////////////////////////////////////////////////////////////

    // Get world region data for contact address list
    Route::post('/getWorldRegionData', function (Request $request) {
        $data = [];
        switch ($request->value) {
            case 'country':
                $data = Country::orderBy('name')->pluck('name', 'id');
                break;

            case 'region':
                $data = Region::orderBy('name')->pluck('name', 'id');
                break;

            default:
                $data = AreaRegion::orderBy('name')->pluck('name', 'id');
                break;
        }

        return response()->json(['success' => true, 'cmbData' => $data]);
    })->name('api.getWorldRegionData');

    ///////////////////////////////////////////////////////////////////////////

    // Get task by id
    Route::post('/task-by-id', function (Request $request) {
        $task = Task::where('id', $request->id)->first();

        return response()->json(['task' => $task]);
    })->name('api.task-by-id');

    ///////////////////////////////////////////////////////////////////////////

    // Get actions per category
    Route::post('/getActionsPerCategory', function (Request $request) {
        $data = Action::where('category', $request->category)
            ->where(function ($query) use ($request) {
                $query->where('belongs_to', $request->belongsTo)
                    ->orWhere('belongs_to', 'Offer_Order');
            })
            ->pluck('action_description', 'id');

        return response()->json(['success' => true, 'cmbData' => $data]);
    })->name('api.getActionsPerCategory');

    // Get order action by id
    Route::post('/order-action-by-id', function (Request $request) {
        $orderAction = OrderAction::where('id', $request->id)->first();

        return response()->json(['orderAction' => $orderAction]);
    })->name('api.order-action-by-id');

    // Get offer action by id
    Route::post('/offer-action-by-id', function (Request $request) {
        $offerAction = OfferAction::where('id', $request->id)->first();

        return response()->json(['offerAction' => $offerAction]);
    })->name('api.offer-action-by-id');

    // Update offer or order _action status
    Route::post('/update-action-status', function (Request $request) {
         $obj = $request->objectType;
         if (!empty($obj)) {
            if ($obj === 'offer') {
               $action = OfferAction::findOrFail($request->id);
            } else {
               $action = OrderAction::findOrFail($request->id);
            }
            $status = ($request->status === 'pending') ? 'done' : 'pending';
            try { 
                $action->update(['status' => $status]);
                return response()->json(['error' => false]);
            } catch(Exception $ex){ 
                return response()->json(['error' => true]);
            }
         }
    })->name('api.update-action-status');

    Route::post('/update-cost-status', function (Request $request) {
       if ($request->table === 'offers_airfreight_pallets') {
          $cost = OfferAirfreightPallet::where('id', $request->cost_id)
                  ->first();
       } elseif ($request->table === 'offers_species_airfreights') {
          $cost = OfferSpeciesAirfreight::where('id', $request->cost_id)
                  ->first();
      } elseif ($request->table === 'offers_species_crates') {
          $cost = OfferSpeciesCrate::where('id', $request->cost_id)
                  ->first();
      } elseif ($request->table === 'offers_transport_truck') {
          $cost = OfferTransportTruck::where('id', $request->cost_id)
                  ->first();
      } elseif ($request->table === 'offers_species') {
          $cost = OfferSpecies::where('id', $request->cost_id)
                  ->first();
      }
      if (!empty($cost)) {
         try {
            $cost->update(['status' => $request->costselected]);
            return response()->json(['error' => false]);
         } catch(Exception $ex){
            return response()->json(['error' => true, 'table' => $request->table]);
         }
      } else {
         return response()->json(['error' => true, 'table' => $request->table]);
      }
    })->name('api.update-cost-status');

    // Search domain name
    Route::get('/search-domain-name', function (Request $request) {
        $domain = explode('@', $request->email);
        if (count($domain) == 2) {
            $domain_name = \App\Models\DomainNameLink::where('domain_name', $domain[1])->first();

            return response()->json(['domain_name' => $domain_name]);
        } else {
            return response()->json();
        }
    })->name('api.search-domain-name');

    Route::post('/duplicate-species-name', function (Request $request) {
        $duplicate = 0;
        if (!empty($request->animalId)) {
            $animal    = Animal::find($request->animalId);
            $duplicate = Animal::where('common_name', $animal->common_name)->get()->count();

            return response()->json(['error' => false, 'duplicate' => $duplicate]);
        } else {
            return response()->json(['error' => true, 'duplicate' => $duplicate]);
        }
    })->name('api.duplicate-species-name');
});
