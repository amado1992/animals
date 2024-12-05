<?php

namespace App\Http\Controllers;

use App\Enums\ContactApprovedStatus;
use App\Enums\ContactMailingCategory;
use App\Enums\ContactOrderByOptions;
use App\Enums\OrganisationLevel;
use App\Enums\Specialty;
use App\Exports\ContactsAddressListExport;
use App\Exports\ContactsExport;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactMergeRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Mail\SendGeneralEmail;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Email;
use App\Models\InterestSection;
use App\Models\Labels;
use App\Models\Mailing;
use App\Models\Organisation;
use App\Models\OrganisationType;
use App\Models\Region;
use App\Models\Surplus;
use App\Models\User;
use App\Models\Wanted;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', Auth::id())->first();

        $contacts = Contact::GetContacts();

        $organization_types_first = OrganisationType::whereIn('key',
            ['AS', 'PBF', 'ZCON', 'Z'])->orderBy('key')->pluck('label', 'key');
        $organization_types_last  = OrganisationType::whereNotIn('key',
            ['AS', 'PBF', 'ZCON', 'Z'])->orderBy('key')->pluck('label', 'key');
        $organization_types       = Arr::collapse([$organization_types_first, $organization_types_last]);
        $organization_levels      = OrganisationLevel::get();

        $countries = Country::orderBy('name')->pluck('name', 'id');
        $regions   = Region::orderBy('name')->pluck('name', 'id');
        $areas     = AreaRegion::orderBy('name')->pluck('name', 'id');

        $mailing_categories = ContactMailingCategory::get();

        $orderByOptions   = ContactOrderByOptions::get();
        $orderByDirection = null;
        $orderByField     = null;

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('contact.filter')) {
            $request = session('contact.filter');

            if (isset($request['filter_title'])) {
                if ($request['filter_title'] === 'empty') {
                    $contacts->whereNull('title');
                } else {
                    $contacts->where('title', $request['filter_title']);
                }

                $filterData = Arr::add($filterData, 'filter_title', 'Title: ' . $request['filter_title']);
            }

            if (!isset($request['filter_name_empty'])) {
                if (isset($request['filter_name'])) {
                    $filterData = Arr::add($filterData, 'filter_name', 'Name: ' . $request['filter_name']);

                    $contacts->where(function ($query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request['filter_name'] . '%')
                              ->orWhere('last_name', 'like', '%' . $request['filter_name'] . '%');
                    });
                }
            } else {
                $filterData = Arr::add($filterData, 'filter_name_empty',
                    'Empty name: ' . $request['filter_name_empty']);

                $contacts->where(function ($query) {
                    $query->whereNull('first_name')
                          ->orWhere(DB::raw('TRIM(first_name)'), '');
                });
                $contacts->where(function ($query) {
                    $query->whereNull('last_name')
                          ->orWhere(DB::raw('TRIM(last_name)'), '');
                });
            }

            if (!isset($request['filter_mobile_empty'])) {
                if (isset($request['filter_mobile_phone'])) {
                    $filterData = Arr::add($filterData, 'filter_mobile_phone',
                        'Mobile: ' . $request['filter_mobile_phone']);

                    $contacts->where('mobile_phone', 'like', '%' . $request['filter_mobile_phone'] . '%');
                }
            } else {
                $filterData = Arr::add($filterData, 'filter_mobile_empty',
                    'Empty mobile: ' . $request['filter_mobile_empty']);

                $contacts->whereNull('mobile_phone');
            }

            if (isset($request['filter_deleted'])) {
                $filterData = Arr::add($filterData, 'filter_deleted', 'Deleted: ' . $request['filter_deleted']);

                $contacts->onlyTrashed();
            }

            if (!isset($request['filter_email_empty'])) {
                if (isset($request['filter_email'])) {
                    $filterData = Arr::add($filterData, 'filter_email', 'Email: ' . $request['filter_email']);

                    $contacts->where('email', 'like', '%' . $request['filter_email'] . '%');
                }
            } else {
                $filterData = Arr::add($filterData, 'filter_email_empty',
                    'Empty email: ' . $request['filter_email_empty']);

                $contacts->whereNull('email');
            }

            if (!isset($request['filter_city_empty'])) {
                if (isset($request['filter_city'])) {
                    $contacts->where('city', 'like', '%' . $request['filter_city'] . '%');

                    $filterData = Arr::add($filterData, 'filter_city', 'City: ' . $request['filter_city']);
                }
            } else {
                $filterData = Arr::add($filterData, 'filter_city_empty',
                    'Empty city: ' . $request['filter_city_empty']);

                $contacts->whereNull('city');
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();
                $filterData    = Arr::add($filterData, 'filter_country',
                    'Country: ' . ($filterCountry != null ? $filterCountry->name : 'Empty'));

                if ($request['filter_country'] == 0) {
                    $contacts->whereNull('country_id');
                } else {
                    $contacts->where('country_id', $request['filter_country']);
                }
            }

            if (isset($request['filter_continent'])) {
                $filterRegion = Region::where('id', $request['filter_continent'])->first();
                $filterData   = Arr::add($filterData, 'filter_continent', 'Region: ' . $filterRegion->name);

                $contacts->whereHas('organisation.country', function ($query) use ($request) {
                    $query->where('region_id', $request['filter_continent']);
                });
            }

            if (!isset($request['filter_institution_empty'])) {
                if (isset($request['filter_institution_type'])) {
                    $filterData = Arr::add($filterData, 'filter_institution_type',
                        'Institution type: ' . $request['filter_institution_type']);

                    if ($request['filter_institution_type'] === 'empty') {
                        $contacts->whereHas('organisation', function ($query) use ($request) {
                            $query->whereNull('organisation_type');
                        });
                    } else {
                        $contacts->whereHas('organisation', function ($query) use ($request) {
                            $query->where('organisation_type', $request['filter_institution_type']);
                        });
                    }
                }
                if (isset($request['filter_institution_name'])) {
                    $filterData = Arr::add($filterData, 'filter_institution_name',
                        'Institution name: ' . $request['filter_institution_name']);

                    $contacts->whereHas('organisation', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request['filter_institution_name'] . '%');
                    });
                }
            } else {
                $filterData = Arr::add($filterData, 'filter_institution_empty',
                    'Empty Institution: ' . $request['filter_institution_empty']);

                $contacts->doesntHave('organisation');
            }

            if (isset($request['filter_institution_level'])) {
                if ($request['filter_institution_level'] === 'empty') {
                    $contacts->whereHas('organisation', function ($query) use ($request) {
                        $query->whereNull('level');
                    });
                } else {
                    $contacts->whereHas('organisation', function ($query) use ($request) {
                        $query->where('level', $request['filter_institution_level']);
                    });
                }

                $filterData = Arr::add($filterData, 'filter_institution_level',
                    'Level: ' . $request['filter_institution_level']);
            }

            if (isset($request['filter_mailing_category'])) {
                $filterData = Arr::add($filterData, 'filter_mailing_category',
                    'Mailing Category: ' . $request['filter_mailing_category']);

                if ($request['filter_mailing_category'] == 'empty') {
                    $contacts->whereNull('mailing_category');
                } else {
                    $contacts->where('mailing_category', $request['filter_mailing_category']);
                }
            }

            if (isset($request['filter_is_member']) && $request['filter_is_member'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_is_member', 'Is member: ' . $request['filter_is_member']);

                if ($request['filter_is_member'] == 'yes') {
                    $contacts->where('source', 'website');
                } else {
                    $contacts->where('source', '<>', 'website');
                }
            }

            if (isset($request['filter_is_active']) && $request['filter_is_active'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_is_active', 'Is active: ' . $request['filter_is_active']);

                if ($request['filter_is_active'] == 'yes') {
                    $contacts->where('member_approved_status', 'active');
                } else {
                    $contacts->where('member_approved_status', '<>', 'active');
                }
            }

            if (isset($request['filter_has_surplus']) && $request['filter_has_surplus'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_surplus',
                    'Has surplus: ' . $request['filter_has_surplus']);

                if ($request['filter_has_surplus'] == 'yes') {
                    if (isset($request['filter_animal_id']) && $request['filter_animal_id'] != null) {
                        $contacts->whereHas('surpluses', function ($query) use ($request) {
                            $query->where('animal_id', $request['filter_animal_id']);
                        });
                    } else {
                        $contacts->has('surpluses');
                    }
                } else {
                    $contacts->doesntHave('surpluses');
                }
            }

            if (isset($request['filter_has_wanted']) && $request['filter_has_wanted'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_wanted',
                    'Has wanted: ' . $request['filter_has_wanted']);

                if ($request['filter_has_wanted'] == 'yes') {
                    if (isset($request['filter_animal_id']) && $request['filter_animal_id'] != null) {
                        $contacts->whereHas('wanteds', function ($query) use ($request) {
                            $query->where('animal_id', $request['filter_animal_id']);
                        });
                    } else {
                        $contacts->has('wanteds');
                    }
                } else {
                    $contacts->doesntHave('wanteds');
                }
            }

            if (isset($request['filter_has_requests']) && $request['filter_has_requests'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_requests',
                    'Has requests: ' . $request['filter_has_requests']);

                if ($request['filter_has_requests'] == 'yes') {
                    if (isset($request['filter_animal_id']) && $request['filter_animal_id'] != null) {
                        $contacts->whereHas('offers.offer_species.oursurplus', function ($query) use ($request) {
                            $query->where('animal_id', $request['filter_animal_id']);
                        });
                    } else {
                        $contacts->has('offers');
                    }
                } else {
                    $contacts->doesntHave('offers');
                }
            }

            if (isset($request['filter_has_orders']) && $request['filter_has_orders'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_orders',
                    'Has orders: ' . $request['filter_has_orders']);

                if ($request['filter_has_orders'] == 'yes') {
                    if (isset($request['filter_animal_id']) && $request['filter_animal_id'] != null) {
                        $contacts->whereHas('orders_contact_client.offer.offer_species.oursurplus',
                            function ($query) use ($request) {
                                $query->where('animal_id', $request['filter_animal_id']);
                            });
                    } else {
                        $contacts->has('orders_contact_client');
                    }
                } else {
                    $contacts->doesntHave('orders_contact_client');
                }
            }

            if (isset($request['filter_animal_id']) && (Arr::exists($filterData,
                        'filter_has_surplus') || Arr::exists($filterData,
                        'filter_has_wanted') || Arr::exists($filterData,
                        'filter_has_requests') || Arr::exists($filterData, 'filter_has_orders'))) {
                $filterAnimal = Animal::where('id', $request['filter_animal_id'])->first();

                $filterData = Arr::add($filterData, 'filter_animal_id', 'With animal: ' . $filterAnimal->common_name);
            }

            if (isset($request['filter_has_invoices']) && $request['filter_has_invoices'] != 'all') {
                $filterData = Arr::add($filterData, 'filter_has_invoices',
                    'Has invoices: ' . $request['filter_has_invoices']);

                if ($request['filter_has_invoices'] == 'yes') {
                    $contacts->has('invoices');
                } else {
                    $contacts->doesntHave('invoices');
                }
            }

            if (isset($request['filter_relation_type'])) {
                if ($request['filter_relation_type'] !== 'all') {
                    $filterData = Arr::add($filterData, 'filter_relation_type',
                        'Relation type: ' . $request['filter_relation_type']);

                    if ($request['filter_relation_type'] === 'both') {
                        $contacts->where('relation_type', '=', 'both');
                    }

                    if ($request['filter_relation_type'] === 'supplier') {
                        $contacts->where('relation_type', '=', 'supplier');
                    }

                    if ($request['filter_relation_type'] === 'client') {
                        $contacts->where('relation_type', '=', 'client');
                    }
                }
            }

            if (isset($request['orderByDirection']) && isset($request['orderByField'])) {
                $orderByDirection = $request['orderByDirection'];
                $orderByField     = $request['orderByField'];

                if ($orderByDirection == 'desc') {
                    if ($orderByField == 'name') {
                        $contacts->orderByRaw('CONCAT(first_name, " ", last_name) DESC');
                    } else {
                        $contacts->orderBy($orderByField, $orderByDirection);
                    }
                } else {
                    if ($orderByField == 'name') {
                        $contacts->orderByRaw('CONCAT(first_name, " ", last_name)');
                    } else {
                        $contacts->orderBy($orderByField, $orderByDirection);
                    }
                }
            } else {
                $contacts->orderBy('updated_at', 'desc');
            }
        }

        if (isset($request) && isset($request['recordsPerPage'])) {
            $contacts = $contacts->paginate($request['recordsPerPage']);
        } else {
            $contacts = $contacts->paginate(20);
        }

        return view('contacts.index', compact(
            'contacts',
            'organization_types',
            'organization_levels',
            'countries',
            'regions',
            'areas',
            'mailing_categories',
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
        session()->forget('contact.filter');

        return redirect(route('contacts.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($organisation_id = null)
    {
        $mailing_categories  = ContactMailingCategory::get();
        $matchedInstitutions = [];
        $organization_types  = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries           = Country::orderBy('name')->pluck('name', 'id');
        $organization_levels = OrganisationLevel::get();
        $interest_sections   = InterestSection::orderBy('key', 'desc')->get();
        $specialties         = Specialty::get();

        $params = compact(
            'mailing_categories',
            'matchedInstitutions',
            'organization_types',
            'countries',
            'organization_levels',
            'interest_sections',
            'specialties'
        );

        if (request()->filled('preset')) {
            $params["first_name"] = request()->input('first_name');
            $params["last_name"]  = request()->input('last_name');
            $params["domain"]     = request()->input('domain');
            $params["city"]       = request()->input('city');
            $params["country_id"] = request()->input('country_id');
        }

        return view('contacts.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ContactCreateRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContactCreateRequest $request)
    {
        $domain_name = ($request->contact_email != null) ? substr($request->contact_email,
            strpos($request->contact_email, '@') + 1) : null;

        $selectInstitutionOption = $request->select_institution_option;
        if ($selectInstitutionOption === 'matched_institution') {
            if ($request->select_institution == null) {
                return redirect()->back()->withInput()->with('contact_msg',
                    'There is not a matched institution selected.');
            } else {
                $organisation_id = $request->select_institution;
            }
        } elseif ($selectInstitutionOption === 'searched_institution') {
            if ($request->organisation_id == null) {
                return redirect()->back()->withInput()->with('contact_msg',
                    'There is not a searched institution selected.');
            } else {
                $organisation_id = $request->organisation_id;
            }
        } elseif ($selectInstitutionOption === 'self_institution') {
            $organization = new Organisation();
            if ($request->first_name !== null || $request->last_name !== null) {
                $organization->name = trim($request->title . ' ' . $request->first_name . ' ' . $request->last_name);
            } else {
                $organization->name = $request->contact_email;
            }
            $organization->specialty     = $request->specialty;
            $organization->relation_type = $request->relation_type;
            $organization->email         = $request->contact_email;
            $organization->domain_name   = $domain_name;
            $organization->country_id    = $request->country_id;
            $organization->city          = $request->city;
            $organization->phone         = $request->mobile_phone;
            $organization->save();
            $organisation_id = $organization->id;
        }

        $contact                   = new Contact();
        $contact->specialty        = $request->specialty;
        $contact->relation_type    = $request->relation_type;
        $contact->email            = $request->contact_email;
        $contact->domain_name      = $domain_name;
        $contact->source           = 'crm';
        $contact->title            = $request->title;
        $contact->first_name       = $request->first_name;
        $contact->last_name        = $request->last_name;
        $contact->country_id       = $request->country_id;
        $contact->city             = $request->city;
        $contact->mobile_phone     = $request->mobile_phone;
        $contact->organisation_id  = $organisation_id;
        $contact->position         = $request->position;
        $contact->mailing_category = $request->mailing_category;
        $contact->inserted_by      = Auth::id();
        $contact->new_contact      = 1;
        $contact->save();

        $contact->interest_sections()->sync($request->interest_section);

        $label       = Labels::where('name', 'new_contact')->first();
        $email_inbox = Email::where('from_email', $request->contact_email)->get();
        foreach ($email_inbox as $row) {
            $row['contact_id']      = $contact->id;
            $row['organisation_id'] = $organisation_id;
            $row->labels()->detach($label);
            $row->save();
        }

        return redirect(route('organisations.index'));
        //return redirect(route('contacts.show', [$contact->id]))->with(['status' => 'Contact is succes{sfully created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = Contact::withTrashed()->where('id', $id)->first();

        $user = Auth::user();
        if (!empty($user->id) && $user->id === 2) {
            $contact['new_contact'] = 0;
            $contact->save();
        }

        $related_organizations = [];
        if ($contact->organisation != null) {
            $related_organizations = Organisation::where('id', '<>', $contact->organisation->id)
                                                 ->where(function ($query) use ($contact) {
                                                     $query->where('name', $contact->organisation->name)
                                                           ->orWhere('domain_name',
                                                               $contact->organisation->domain_name);
                                                 })
                                                 ->get();
        }

        $contactSurpluses      = $contact->surpluses()->paginate(5, ['*'], 'surpluses');
        $contactWanteds        = $contact->wanteds()->paginate(5, ['*'], 'wanteds');
        $contactPendingOffers  = $contact->offers()->where('offer_status', 'Pending')->paginate(5, ['*'],
            'pending_offers');
        $contactPendingOrders  = $contact->orders_contact_client()->where('order_status', 'Pending')->paginate(5, ['*'],
            'pending_orders');
        $contactRealizedOrders = $contact->orders_contact_client()->where('order_status', 'Realized')->paginate(5,
            ['*'], 'realized_orders');
        $emails_received       = Email::where('from_email', $contact['email'])->where('is_send',
            0)->orderBy('created_at', 'DESC')->paginate(10);
        $emails                = Email::where('is_send', 1)->where('to_email', $contact['email'])->orderBy('created_at',
            'DESC')->paginate(10);

        //return redirect( route('contacts.edit', [$contact->id]) );
        return view('contacts.show', compact(
            'contact',
            'related_organizations',
            'contactSurpluses',
            'contactWanteds',
            'contactPendingOffers',
            'contactPendingOrders',
            'contactRealizedOrders',
            'emails',
            'emails_received'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, Request $request)
    {
        $contact->contact_email = $contact->email;
        if ($contact->organization != null) {
            $contact->organisation = $contact->organization->name . ' (' . $contact->organization->email . ')';
        }

        $mailing_categories     = ContactMailingCategory::get();
        $member_approved_status = ContactApprovedStatus::get();

        $matchedInstitutions = Organisation::where('domain_name', $contact->domain_name)
                                           ->where('city', $contact->city)
                                           ->orderBy('name')
                                           ->pluck('name', 'id');

        $organization_types = OrganisationType::orderBy('key')->pluck('label', 'key');

        $countries           = Country::orderBy('name')->pluck('name', 'id');
        $organization_levels = OrganisationLevel::get();

        $interest_sections       = InterestSection::orderBy('key', 'desc')->get();
        $contactInterestSections = $contact->interest_sections()->pluck('key');
        $specialties             = Specialty::get();
        $edit                    = $request->edit ?? "";
        $edit_id                 = $request->edit_id ?? "";

        return view('contacts.edit', compact(
            'contact',
            'matchedInstitutions',
            'mailing_categories',
            'member_approved_status',
            'organization_types',
            'countries',
            'organization_levels',
            'interest_sections',
            'contactInterestSections',
            'specialties',
            'edit',
            'edit_id'
        ));
    }

    /**
     * @return JsonResponse
     */
    public function checkForExistence(Request $request)
    {
        $firstName = $request->input('first_name');
        $lastName  = $request->input('last_name');
        $domain    = $request->input('domain');
        $city      = $request->input('city');
        $country   = $request->input('country');

        $organisations = Organisation::where("city", "like", "%{$city}")
                                     ->where("country_id", "=", $country)
                                     ->where(static function ($query) use ($firstName, $lastName, $domain) {
                                         $query->where('name', 'like', "%{$firstName}%")
                                               ->orWhere('name', 'like', "%{$lastName}%")
                                               ->orWhere('synonyms', 'like', "%{$firstName}%")
                                               ->orWhere('synonyms', 'like', "%{$lastName}%")
                                               ->when($domain, static function ($query) use ($domain) {
                                                   $query->orWhere("domain_name", "like", "%{$domain}%");
                                               });
                                     })
                                     ->get();

        $contacts = Contact::where("city", "like", "%{$city}")
                           ->where("country_id", "=", $country)
                           ->where(static function ($query) use ($firstName, $lastName, $domain) {
                               $query->where("first_name", "like", "%{$firstName}%")
                                     ->orWhere("last_name", "like", "%{$lastName}%")
                                     ->when($domain, static function ($query) use ($domain) {
                                         $query->orWhere("domain_name", "like", "%{$domain}%");
                                     });
                           })
                           ->get();


        return response()->json(compact('organisations', 'contacts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ContactUpdateRequest  $request
     * @param  \App\Models\Contact  $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactUpdateRequest $request, Contact $contact)
    {
        $contact->specialty     = $request->specialty;
        $contact->relation_type = $request->relation_type;
        $contact->email         = $request->contact_email;
        $domain_name            = ($request->contact_email != null) ? substr($request->contact_email,
            strpos($request->contact_email, '@') + 1) : null;
        $contact->domain_name   = $domain_name;
        $contact->title         = $request->title;
        $contact->first_name    = $request->first_name;
        $contact->last_name     = $request->last_name;
        $contact->mobile_phone  = $request->mobile_phone;
        $contact->country_id    = $request->country_id;
        $contact->city          = $request->city;
        $contact->position      = $request->position;

        $selectInstitutionOption = $request->select_institution_option;
        if ($selectInstitutionOption === 'matched_institution') {
            if ($request->select_institution == null) {
                return redirect()->back()->withInput()->with('contact_msg',
                    'There is not a matched institution selected.');
            } else {
                $contact->organisation_id = $request->select_institution;
            }
        } elseif ($selectInstitutionOption === 'searched_institution') {
            if ($request->organisation_id == null) {
                return redirect()->back()->withInput()->with('contact_msg',
                    'There is not a searched institution selected.');
            } else {
                $contact->organisation_id = $request->organisation_id;
            }
        } elseif ($selectInstitutionOption === 'none_institution') {
            $contact->organisation_id = null;
        }

        if ($request->organisation_type != null && $request->organisation_type != $contact->organisation->organisation_type) {
            $contact->organisation()->update(['organisation_type' => $request->organisation_type]);
        }

        if ($request->organisation_level != null && $request->organisation_level != $contact->organisation->level) {
            $contact->organisation()->update(['level' => $request->organisation_level]);
        }

        if ($request->mailing_category != null) {
            $contact->mailing_category = $request->mailing_category;
        }
        if ($contact->source == 'website' && $request->member_approved_status != null) {
            $contact->member_approved_status = $request->member_approved_status;
        }
        $contact->update();

        $contact->interest_sections()->sync($request->interest_section);

        if (!empty($request->edit) && !empty($request->edit_id) && $request->edit == "offer") {
            return redirect(route('offers.show', $request->edit_id));
        } else {
            return redirect(route('contacts.show', $contact->id));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  Contact  $contact
     *
     * @return JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function destroy(Request $request, Contact $contact)
    {
        $handOver     = $request->input('handover_id');
        $handOverType = $request->input('handover_type');
        $toDelete     = $request->input('to_delete_id');

        $contact->load(['wanteds', 'surpluses']);

        if ($handOver && $handOverType) {
            $newParent = Organisation::find($handOver);

            try {
                DB::beginTransaction();
                $contact->surpluses->each(static function (Surplus $surplus) use ($newParent) {
                    $surplus->update(['contact_id' => $newParent->id]);
                });

                $contact->wanteds->each(static function (Wanted $wanted) use ($newParent) {
                    $wanted->update(['contact_id' => $newParent->id]);
                });

                $contact->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                ]);
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $validator = Validator::make($contact->toArray(), []);

        if (!$validator->errors()->has('surpluses') && $contact->surpluses->count() > 0) {
            $validator->errors()->add('surpluses', 'The contact has related surplus records.');
        }

        if (!$validator->errors()->has('wanteds') && $contact->wanteds->count() > 0) {
            $validator->errors()->add('wanteds', 'The contact has related wanted records.');
        }

        if ($request->expectsJson()) {
            if ($validator->errors()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'data'    => $validator->getMessageBag(),
                ]);
            } else {
                $contact->delete();

                return response()->json(['success' => true]);
            }
        }

        if ($validator->errors()->count() > 0) {
            return redirect(route('contacts.show', $contact))->withErrors($validator);
        } else {
            $contact->delete();

            return redirect(route('contacts.index'));
        }
    }

    /**
     * Remove the selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete_items(Request $request)
    {
        if (count($request->items) > 0) {
            $contactsNotDeleted = [];

            foreach ($request->items as $id) {
                $contactError = false;

                $deleteContact = Contact::findOrFail($id);

                if ($deleteContact->offers->count() > 0) {
                    $contactError = true;
                } elseif ($deleteContact->orders_contact_client->count() > 0 || $deleteContact->orders_contact_supplier->count() > 0 || $deleteContact->orders_contact_origin->count() > 0 || $deleteContact->orders_contact_destination->count() > 0) {
                    $contactError = true;
                } elseif ($deleteContact->invoices->count() > 0) {
                    $contactError = true;
                }

                if ($contactError) {
                    array_push($contactsNotDeleted, $deleteContact->id);
                } else {
                    $deleteContact->delete();
                }
            }
        }

        return response()->json(['message' => (count($contactsNotDeleted) > 0) ? 'Some contacts were not deleted because are related with: offers, or orders, or invoices.' : null]);
    }

    /**
     * Restore deleted contact.
     *
     * @param  Request  $Request
     *
     * @return \Illuminate\Http\Response
     */
    public function restoreContactDeleted(Request $request)
    {
        $contact = Contact::withTrashed()->findOrFail($request->id);
        $contact->restore();

        return redirect(route('contacts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @param  \App\Models\Contact  $contact
     * @param  string  $from
     * @param  int  $contact_id
     *
     * @return \Illuminate\Http\Response
     */
    public function compare(Contact $contact, $contactToMerge, $from, $contact_id)
    {
        $contactToMerge = Contact::findOrFail($contactToMerge);

        if ($contact->source == 'website' && $contact->member_approved_status == 'active' && $contactToMerge->member_approved_status != 'active') {
            $tempContact    = $contactToMerge;
            $contactToMerge = $contact;
            $contact        = $tempContact;
        }

        return view('contacts.compare', compact('contact', 'contactToMerge', 'from', 'contact_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ContactMergeRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function merge(ContactMergeRequest $request)
    {
        //dd($request->all());
        $contact        = Contact::findOrFail($request->contact_id);
        $contactToMerge = Contact::findOrFail($request->contactToMerge_id);

        $email = null;
        if ($request->input('check_title')) {
            $contactToMerge->title = $contact->title;
        }
        if ($request->input('check_first_name')) {
            $contactToMerge->first_name = $contact->first_name;
        }
        if ($request->input('check_last_name')) {
            $contactToMerge->last_name = $contact->last_name;
        }
        if ($request->input('check_email')) {
            $email = $contact->email;
        }
        if ($request->input('check_position')) {
            $contactToMerge->position = $contact->position;
        }
        if ($request->input('check_mailing_category')) {
            $contactToMerge->mailing_category = $contact->mailing_category;
        }
        if ($request->input('check_mobile_phone')) {
            $contactToMerge->mobile_phone = $contact->mobile_phone;
        }

        if ($contactToMerge->source != 'website' && $contact->source == 'website') {
            $contactToMerge->source = $contact->source;
        }

        if ($contactToMerge->member_approved_status == null && $contact->member_approved_status != null) {
            $contactToMerge->member_approved_status = $contact->member_approved_status;
        }

        if ($contactToMerge->organisation_id == null && $contact->organisation_id != null) {
            $contactToMerge->organisation_id = $contact->organisation_id;
        }

        $contactToMerge->surpluses()->saveMany($contact->surpluses);
        $contactToMerge->wanteds()->saveMany($contact->wanteds);
        $contactToMerge->offers()->saveMany($contact->offers);
        $contactToMerge->orders_contact_client()->saveMany($contact->orders_contact_client);
        $contactToMerge->orders_contact_supplier()->saveMany($contact->orders_contact_supplier);
        $contactToMerge->orders_contact_origin()->saveMany($contact->orders_contact_origin);
        $contactToMerge->orders_contact_destination()->saveMany($contact->orders_contact_destination);
        $contactToMerge->invoices()->saveMany($contact->invoices);
        $contactToMerge->interest_sections()->saveMany($contact->interest_sections);

        $contact->delete();

        if ($email != null) {
            $contactToMerge->email = $email;
        }

        $contactToMerge->update();

        return redirect(route('contacts.show', [$contactToMerge->id]));
    }

    /**
     * Filter contacts.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function filterContacts(Request $request)
    {
        // Set session organization filter
        $data = session('contact.filter');
        foreach ($request->query() as $key => $row) {
            if (!empty($row) || $row == "0") {
                $data[$key] = $row;
            }
        }
        session(['contact.filter' => $data]);

        return redirect(route('contacts.index'));
    }

    /**
     * Order by.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function orderBy(Request $request)
    {
        $query                     = session('contact.filter');
        $query['orderByDirection'] = $request->orderByDirection;
        $query['orderByField']     = $request->orderByField;
        session(['contact.filter' => $query]);

        return redirect(route('contacts.index'));
    }

    /**
     * Records per page.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function recordsPerPage(Request $request)
    {
        $query                   = session('contact.filter');
        $query['recordsPerPage'] = $request->recordsPerPage;
        session(['contact.filter' => $query]);

        return redirect(route('contacts.index'));
    }

    /**
     * Remove from contact session.
     *
     * @param  string  $key
     *
     * @return \Illuminate\Http\Response
     */
    public function removeFromContactSession($key)
    {
        $query = session('contact.filter');
        Arr::forget($query, $key);
        session(['contact.filter' => $query]);

        return redirect(route('contacts.index'));
    }

    /**
     * Get doubles view.
     *
     * @return \Illuminate\Http\Response
     */
    public function doublesView()
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $criteria  = null;

        $organization_types_first = OrganisationType::whereIn('key',
            ['AS', 'PBF', 'ZCON', 'Z'])->orderBy('key')->pluck('label', 'key');
        $organization_types_last  = OrganisationType::whereNotIn('key',
            ['AS', 'PBF', 'ZCON', 'Z'])->orderBy('key')->pluck('label', 'key');
        $organization_types       = Arr::collapse([$organization_types_first, $organization_types_last]);

        $organization_levels = OrganisationLevel::get();

        $mailing_categories = ContactMailingCategory::get();

        return view('contacts.find_doubles', compact(
            'countries',
            'criteria',
            'organization_types',
            'organization_levels',
            'mailing_categories'
        ));
    }

    /**
     * Filter doubles.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function filterDoubles(Request $request)
    {
        //DB::enableQueryLog(); // Enable query log
        $contacts = Contact::GetContacts();

        if (isset($request->filter_country)) {
            $contacts->whereHas('organisation', function ($query) use ($request) {
                $query->where('country_id', $request->filter_country);
            });
        }
        if (isset($request->filter_city)) {
            $contacts->whereHas('organisation', function ($query) use ($request) {
                $query->where('city', $request->filter_city);
            });
        }

        $criteria = $request->filter_doubles_by;
        if (isset($criteria)) {
            switch ($criteria) {
                case 'full_name':
                    $contacts->whereNotNull(DB::raw("CONCAT(title,' ',first_name,' ',last_name)"))
                             ->where(DB::raw("TRIM(CONCAT(title,' ',first_name,' ',last_name))"), '<>', '');
                    break;
                case 'email':
                    $contacts->whereNotNull('email')
                             ->where(DB::raw('TRIM(email)'), '<>', '');
                    break;
                case 'domain_name':
                    $contacts->whereNotNull('domain_name')
                             ->where(DB::raw('TRIM(domain_name)'), '<>', '');
                    break;
            }
        }

        $contacts = $contacts->get();

        if (isset($criteria)) {
            $contacts = $contacts->sortBy($criteria);
            $contacts = $contacts->groupBy($criteria);
        }
        //dump(DB::getQueryLog()); // Show results of log

        $array_object_results = [];
        foreach ($contacts as $contact_group) {
            if (count($contact_group) > 1) {
                foreach ($contact_group as $contactDouble) {
                    array_push($array_object_results, $contactDouble);
                }
            }
        }

        $total        = count($array_object_results);
        $per_page     = 100;
        $current_page = $request->input('page') ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;
        $contacts       = array_slice($array_object_results, $starting_point, $per_page, true);
        $contacts       = new Paginator($contacts, $total, $per_page, $current_page, [
            'path'  => $request->url(),   //esto se puede sustituir por la url, por ejemplo “our-wanted”
            'query' => $request->query(), //este parámetro creo que no es necesario
        ]);

        $countries          = Country::orderBy('name')->pluck('name', 'id');
        $mailing_categories = ContactMailingCategory::get();

        return view('contacts.find_doubles', compact('contacts', 'countries', 'criteria', 'mailing_categories'));
    }

    /**
     * Edit selected items.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function editSelectedRecords(Request $request)
    {
        $msg   = 'The selection records has been updated successfully';
        $error = false;
        if (count($request->items) > 0) {
            $contacts = Contact::whereIn('id', $request->items)->get();

            $changeInstitutionName = false;
            if (isset($request->institution_name) || $request->make_institution_name == 1) {
                $firstElement = Arr::first($contacts->toArray(), function ($value, $key) {
                    return $value['organisation_id'] != null;
                });

                if ($firstElement != null) {
                    $filteredSameInstitution = Arr::where($contacts->toArray(),
                        function ($value, $key) use ($firstElement) {
                            return $value['organisation_id'] == $firstElement['organisation_id'];
                        });

                    $filteredNullInstitution = Arr::where($contacts->toArray(), function ($value, $key) {
                        return $value['organisation_id'] == null;
                    });

                    $count = count($request->items) - count($filteredNullInstitution);
                    if ($count != count($filteredSameInstitution)) {
                        $msg   = 'Selected contacts belong to different institutions, you cannot change the institution name.';
                        $error = false;
                    } else {
                        $changeInstitutionName = true;
                    }
                } else {
                    $msg   = 'Selected contacts have not institution assigned, you cannot change the institution name.';
                    $error = true;
                }
            }

            foreach ($contacts as $contact) {
                if (isset($request->title)) {
                    $contact->update(['origin' => $request->title]);
                }

                if (isset($request->first_name)) {
                    $contact->update(['first_name' => $request->first_name]);
                }

                if (isset($request->last_name)) {
                    $contact->update(['last_name' => $request->last_name]);
                }

                if (isset($request->institution_id)) {
                    $contact->update(['organisation_id' => $request->institution_id]);
                }

                if (isset($request->institution_name) && $contact->organisation != null && $changeInstitutionName) {
                    $contact->organisation()->update(['name' => $request->institution_name]);
                }

                if ($request->make_institution_name == 1 && $contact->organisation != null && $contact->organisation->organisation_type != null && $contact->organisation->city != null && $changeInstitutionName) {
                    $institution_name = strtoupper($contact->organisation->city) . ' ' . $contact->organisation->type->key;
                    $contact->organisation()->update(['name' => $institution_name]);
                }

                if (isset($request->institution_level) && $contact->organisation != null) {
                    if ($request->institution_level === 'empty') {
                        $contact->organisation()->update(['level' => null]);
                    } else {
                        $contact->organisation()->update(['level' => $request->institution_level]);
                    }
                }

                if ($request->make_country == 1 && $contact->email !== null) {
                    $emailExtension = substr($contact->email, strripos($contact->email, '.') + 1);
                    $country        = Country::where('country_code', strtoupper($emailExtension))->first();
                    if ($country !== null) {
                        $contact->update(['country_id' => $country->id]);
                    }
                } elseif (isset($request->country_id)) {
                    $contact->update(['country_id' => $request->country_id]);
                }

                if (isset($request->city)) {
                    $contact->update(['city' => $request->city]);
                }

                if (isset($request->mailing_category)) {
                    $contact->update(['mailing_category' => $request->mailing_category]);
                }
            }
        }

        return response()->json(['error' => $error, 'message' => $msg]);
    }

    //Export excel document with contacts info.
    public function export(Request $request)
    {
        if (session()->has('contact.filter')) {
            $filter = session('contact.filter');
        } else {
            $filter = [];
        }

        if (!empty($filter) && !empty($filter['orderByField']) && !empty($filter['orderByDirection'])) {
            $contacts = Contact::whereIn('id', explode(',', $request->items))->orderBy($filter['orderByField'],
                $filter['orderByDirection'])->get();
        } else {
            $contacts = Contact::whereIn('id', explode(',', $request->items))->get();
        }

        if ($request->file_type === 'csv') {
            $file_name = 'Contacts list ' . Carbon::now()->format('Y-m-d') . '.csv';

            $export = new ContactsAddressListExport($contacts);
        } else {
            $file_name = 'Contacts list ' . Carbon::now()->format('Y-m-d') . '.xlsx';

            $export = new ContactsExport($contacts);
        }

        return Excel::download($export, $file_name);
    }

    //Export contacts address list in csv format.
    public function createContactAddressList(Request $request)
    {
        $file_name = 'Contacts address list ' . Carbon::now()->format('Y-m-d') . '.csv';

        //DB::enableQueryLog();
        $contacts = Contact::GetContacts()->where('mailing_category', 'all_mailings');

        $contacts->where(function ($query) {
            $query->whereNotNull('email')
                  ->where(DB::raw('TRIM(email)'), '<>', '');
        });

        $newMailing               = new Mailing();
        $newMailing->date_created = Carbon::now()->format('Y-m-d H:i:s');

        if (isset($request->level)) {
            $newMailing->institution_level = $request->level;

            $contacts->whereHas('organisation', function ($query) use ($request) {
                $query->where('level', $request->level);
            });
        }

        if (isset($request->itypes)) {
            $newMailing->institution_types = $request->itypes;

            $contacts->whereHas('organisation', function ($query) use ($request) {
                $query->whereIn('organisation_type', explode(',', $request->itypes));
            });
        }

        $newMailing->language = $request->language;
        if (isset($request->language) && $request->language != 'all') {
            $contacts->whereHas('country', function ($query) use ($request) {
                $query->where('language', $request->language);
            });
        }

        $exclude_continents = [];
        if (isset($request->exclude_continents)) {
            $exclude_continents = explode(',', $request->exclude_continents);

            $exclude_continents_names       = Region::whereIn('id', $exclude_continents)->pluck('name')->all();
            $newMailing->exclude_continents = implode(', ', $exclude_continents_names);
        }

        $exclude_countries = [];
        if (isset($request->exclude_countries)) {
            $exclude_countries = explode(',', $request->exclude_countries);

            $exclude_countries_names       = Country::select('name')->whereIn('id',
                $exclude_countries)->pluck('name')->all();
            $newMailing->exclude_countries = implode(', ', $exclude_countries_names);
        }

        if (isset($request->world_region)) {
            $world_regions = explode(',', $request->world_region_selection);

            if (in_array('0', $world_regions)) {
                $world_regions_name = AreaRegion::select('name')->pluck('name')->all();
            } else {
                $world_regions_name = AreaRegion::select('name')->whereIn('id', $world_regions)->pluck('name')->all();
            }

            $newMailing->part_of_world = implode(', ', $world_regions_name);

            switch ($request->world_region) {
                case 'country':
                    if (!in_array('0', $world_regions)) {
                        $contacts->whereHas('country', function ($query) use ($world_regions) {
                            $query->whereIn('id', $world_regions);
                        });
                    }
                    break;

                case 'region':
                    if (!in_array('0', $world_regions)) {
                        $contacts->whereHas('country', function ($query) use ($world_regions, $exclude_countries) {
                            $query->whereIn('region_id', $world_regions)
                                  ->when((count($exclude_countries) > 0), function ($query) use ($exclude_countries) {
                                      return $query->whereNotIn('id', $exclude_countries);
                                  });
                        });
                    }
                    break;
                default:
                    if (!in_array('0', $world_regions)) {
                        $contacts->whereHas('country',
                            function ($query) use ($world_regions, $exclude_continents, $exclude_countries) {
                                $query->whereHas('region', function ($query) use ($world_regions) {
                                    $query->whereIn('area_region_id', $world_regions);
                                })
                                      ->when((count($exclude_continents) > 0),
                                          function ($query) use ($exclude_continents) {
                                              return $query->whereNotIn('region_id', $exclude_continents);
                                          })
                                      ->when((count($exclude_countries) > 0),
                                          function ($query) use ($exclude_countries) {
                                              return $query->whereNotIn('id', $exclude_countries);
                                          });
                            });
                    }
                    break;
            }
        }

        $contacts = $contacts->orderBy('id')->get();
        //dump(DB::getQueryLog());

        $newMailing->save();
        $export = new ContactsAddressListExport($contacts);

        return Excel::download($export, $file_name);
    }

    /**
     * Email to client.
     *
     * @return \Illuminate\Http\Response
     */
    public function contactsSendEmail()
    {
        $email_from    = 'info@zoo-services.com';
        $email_to      = '';
        $email_subject = '';
        $email_body    = view('emails.general-mail')->render();

        return view('contacts.email_to_contacts', compact('email_from', 'email_to', 'email_subject', 'email_body'));
    }

    /**
     * Send email option.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sendEmailTemplate(Request $request)
    {
        $email_cc_array = [];
        if ($request->email_cc != null) {
            $email_cc_array = array_map('trim', explode(',', $request->email_cc));
        }

        Mail::to('johnrens@zoo-services.com')->cc([
            'development@zoo-services.com', 'rossmery@zoo-services.com',
        ])->send(new SendGeneralEmail($request->email_from, $request->email_subject, $request->email_body));

        return redirect(route('contacts.index'));
    }

    public function resetListEmailNewContact()
    {
        $contact = Contact::where('new_contact', 1)->get();
        if (!empty($contact)) {
            foreach ($contact as $row) {
                $row['new_contact'] = 0;
                $row->save();
            }
        }
        $title_dash = 'Contacts';

        return view('components.reset_list_email_new', compact('title_dash'));
    }
}
