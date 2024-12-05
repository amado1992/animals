<?php

namespace App\Http\Controllers;

use App\Enums\ContactApprovedStatus;
use App\Enums\ContactMailingCategory;
use App\Enums\OrganisationLevel;
use App\Models\Contact;
use App\Models\Country;
use App\Models\InterestSection;
use App\Models\Organisation;
use App\Models\OrganisationType;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;

class ContactDeletedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::onlyTrashed()->orderByDesc('updated_at');

        $organization_types = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries          = Country::orderBy('name')->pluck('name', 'id');
        $regions            = Region::orderBy('name')->pluck('name', 'id');
        $mailing_categories = ContactMailingCategory::get();

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('contact_deleted.filter')) {
            $request = session('contact_deleted.filter');

            //DB::enableQueryLog(); // Enable query log
            if (isset($request['filter_title'])) {
                $contacts->where('title', $request['filter_title']);

                $filterData = Arr::add($filterData, 'filter_title', 'Title: ' . $request['filter_title']);
            }

            if (isset($request['filter_name'])) {
                $contacts->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request['filter_name'] . '%')
                        ->orWhere('last_name', 'like', '%' . $request['filter_name'] . '%');
                });

                $filterData = Arr::add($filterData, 'filter_name', 'Name: ' . $request['filter_name']);
            }

            if (isset($request['filter_email'])) {
                $contacts->where('email', 'like', '%' . $request['filter_email'] . '%');

                $filterData = Arr::add($filterData, 'filter_email', 'Email: ' . $request['filter_email']);
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();

                $contacts->whereHas('organisation', function ($query) use ($filterCountry) {
                    $query->where('country_id', $filterCountry->id);
                });

                $filterData = Arr::add($filterData, 'filter_country', 'Country: ' . $filterCountry->name);
            }

            if (isset($request['filter_continent'])) {
                $filterRegion = Region::where('id', $request['filter_continent'])->first();

                $contacts->whereHas('organisation.country', function ($query) use ($filterRegion) {
                    $query->where('region_id', $filterRegion->id);
                });

                $filterData = Arr::add($filterData, 'filter_continent', 'Region: ' . $filterRegion->name);
            }

            if (isset($request['filter_institution_type'])) {
                $contacts->whereHas('organisation', function ($query) use ($request) {
                    $query->where('organisation_type', $request['filter_institution_type']);
                });

                $filterData = Arr::add($filterData, 'filter_institution_type', 'Institution type: ' . $request['filter_institution_type']);
            }

            if (isset($request['filter_institution_name'])) {
                $contacts->whereHas('organisation', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request['filter_institution_name'] . '%');
                });

                $filterData = Arr::add($filterData, 'filter_institution_name', 'Institution name: ' . $request['filter_institution_name']);
            }

            if (isset($request['filter_mailing_category'])) {
                if ($request['filter_mailing_category'] == 'empty') {
                    $contacts->whereNull('mailing_category');
                } else {
                    $contacts->where('mailing_category', $request['filter_mailing_category']);
                }

                $filterData = Arr::add($filterData, 'filter_mailing_category', 'Mailing category: ' . $request['filter_mailing_category']);
            }

            if (isset($request['filter_is_member']) && $request['filter_is_member'] != 'all') {
                if ($request['filter_is_member'] == 'yes') {
                    $contacts->where('source', 'website');
                } else {
                    $contacts->where('source', '<>', 'website');
                }

                $filterData = Arr::add($filterData, 'filter_is_member', 'Is member: ' . $request['filter_is_member']);
            }

            if (isset($request['filter_is_active']) && $request['filter_is_active'] != 'all') {
                if ($request['filter_is_active'] == 'yes') {
                    $contacts->where('member_approved_status', 'active');
                } else {
                    $contacts->where('member_approved_status', '<>', 'active');
                }

                $filterData = Arr::add($filterData, 'filter_is_active', 'Is active: ' . $request['filter_is_active']);
            }

            if (isset($request['filter_has_surplus']) && $request['filter_has_surplus'] != 'all') {
                if ($request['filter_has_surplus'] == 'yes') {
                    $contacts->has('surpluses');
                } else {
                    $contacts->doesntHave('surpluses');
                }

                $filterData = Arr::add($filterData, 'filter_has_surplus', 'Has surplus: ' . $request['filter_has_surplus']);
            }

            if (isset($request['filter_has_wanted']) && $request['filter_has_wanted'] != 'all') {
                if ($request['filter_has_wanted'] == 'yes') {
                    $contacts->has('wanteds');
                } else {
                    $contacts->doesntHave('wanteds');
                }

                $filterData = Arr::add($filterData, 'filter_has_wanted', 'Has wanted: ' . $request['filter_has_wanted']);
            }

            if (isset($request['filter_has_requests']) && $request['filter_has_requests'] != 'all') {
                if ($request['filter_has_requests'] == 'yes') {
                    $contacts->has('offers');
                } else {
                    $contacts->doesntHave('offers');
                }

                $filterData = Arr::add($filterData, 'filter_has_requests', 'Has requests: ' . $request['filter_has_requests']);
            }

            if (isset($request['filter_has_orders']) && $request['filter_has_orders'] != 'all') {
                if ($request['filter_has_orders'] == 'yes') {
                    $contacts->has('orders');
                } else {
                    $contacts->doesntHave('orders');
                }

                $filterData = Arr::add($filterData, 'filter_has_orders', 'Has orders: ' . $request['filter_has_orders']);
            }

            if (isset($request['filter_has_invoices']) && $request['filter_has_invoices'] != 'all') {
                if ($request['filter_has_invoices'] == 'yes') {
                    $contacts->has('invoices');
                } else {
                    $contacts->doesntHave('invoices');
                }

                $filterData = Arr::add($filterData, 'filter_has_invoices', 'Has invoices: ' . $request['filter_has_invoices']);
            }
        }

        $contacts = $contacts->paginate(20);

        return view('contacts_deleted.index', compact(
            'contacts',
            'organization_types',
            'countries',
            'regions',
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
        session()->forget('contact_deleted.filter');

        return redirect(route('contacts-deleted.index'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);

        $related_organizations = [];
        if ($contact->organisation != null) {
            $related_organizations = Organisation::where('id', '<>', $contact->organisation->id)
                ->where(function ($query) use ($contact) {
                    $query->where('name', $contact->organisation->name)
                        ->orWhere('domain_name', $contact->organisation->domain_name);
                })
                ->get();
        }

        return view('contacts_deleted.show', compact('contact', 'related_organizations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);

        $contact->contact_email = $contact->email;

        $mailing_categories     = ContactMailingCategory::get();
        $member_approved_status = ContactApprovedStatus::get();
        $organisations          = Organisation::orderBy('name')->pluck('name', 'id');
        $organization_types     = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries              = Country::orderBy('name')->pluck('name', 'id');
        $organization_levels    = OrganisationLevel::get();
        $interest_sections      = InterestSection::orderBy('key', 'desc')->get();

        return view('contacts_deleted.edit', compact('contact', 'mailing_categories', 'member_approved_status', 'organisations', 'organization_types', 'countries', 'organization_levels', 'interest_sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);

        $contact->email           = $request->contact_email;
        $contact->title           = $request->title;
        $contact->first_name      = $request->first_name;
        $contact->last_name       = $request->last_name;
        $contact->mobile_phone    = $request->mobile_phone;
        $contact->organisation_id = $request->organisation_id;
        if ($request->mailing_category != null) {
            $contact->mailing_category = $request->mailing_category;
        }
        if ($contact->source == 'website' && $request->member_approved_status != null) {
            $contact->member_approved_status = $request->member_approved_status;
        }
        $contact->update();

        return redirect(route('contacts-deleted.show', $contact->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::withTrashed()->findOrFail($id);
        $contact->forceDelete();

        return redirect(route('contacts-deleted.index'));
    }

    /**
     * Restore selected deleted contacts.
     *
     * @param Request $Request
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $contact = Contact::withTrashed()->findOrFail($id);
                $contact->restore();
            }
        }

        return response()->json();
    }

    /**
     * Filter contacts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterContactsDeleted(Request $request)
    {
        // Set session crate filter
        session(['contact_deleted.filter' => $request->query()]);

        return redirect(route('contacts-deleted.index'));
    }

    /**
     * Remove from contact_deleted session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromContactDeletedSession($key)
    {
        $query = session('contact_deleted.filter');
        Arr::forget($query, $key);
        session(['contact_deleted.filter' => $query]);

        return redirect(route('contacts-deleted.index'));
    }
}
