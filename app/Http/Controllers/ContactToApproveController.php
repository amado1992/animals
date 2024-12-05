<?php

namespace App\Http\Controllers;

use App\Enums\ContactApprovedStatus;
use App\Enums\ContactMailingCategory;
use App\Enums\OrganisationLevel;
use App\Http\Requests\ContactApproveUpdateRequest;
use App\Mail\SendGeneralEmail;
use App\Models\Contact;
use App\Models\Country;
use App\Models\InterestSection;
use App\Models\Organisation;
use App\Models\OrganisationType;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class ContactToApproveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::NeedsApproval()->orderByDesc('updated_at');

        $contact_approved_status = ContactApprovedStatus::get();
        $organization_types      = OrganisationType::orderBy('key')->pluck('label', 'key');
        $countries               = Country::orderBy('name')->pluck('name', 'id');
        $regions                 = Region::orderBy('name')->pluck('name', 'id');
        $mailing_categories      = ContactMailingCategory::get();

        $filterData = [];
        // Check if filter is set on session
        if (session()->has('contact_approve.filter')) {
            $request = session('contact_approve.filter');

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

            if (isset($request['filter_institution_type'])) {
                $contacts->whereHas('organisation', function ($query) use ($request) {
                    $query->where('organisation_type', $request['filter_institution_type']);
                });

                $filterData = Arr::add($filterData, 'filter_institution_type', 'Institution type: ' . $request['filter_institution_type']);
            }

            if (isset($request['filter_institution_name'])) {
                $contacts->whereHas('organisation', function ($query) use ($request) {
                    $query->where('name', $request['filter_institution_name']);
                });

                $filterData = Arr::add($filterData, 'filter_institution_name', 'Institution name: ' . $request['filter_institution_name']);
            }

            if (isset($request['filter_country'])) {
                $filterCountry = Country::where('id', $request['filter_country'])->first();
                $filterData    = Arr::add($filterData, 'filter_country', 'Country: ' . ($filterCountry != null ? $filterCountry->name : 'Empty'));

                if ($request['filter_country'] == 0) {
                    $contacts->whereNull('country_id');
                } else {
                    $contacts->where('country_id', $request['filter_country']);
                }
            }

            if (isset($request['filter_continent'])) {
                $filterRegion = Region::where('id', $request['filter_continent'])->first();

                $contacts->whereHas('organisation.country', function ($query) use ($filterRegion) {
                    $query->where('region_id', $filterRegion->id);
                });

                $filterData = Arr::add($filterData, 'filter_continent', 'Region: ' . $filterRegion->name);
            }
        }

        $contacts = $contacts->paginate(20);

        return view('contacts_to_approve.index', compact(
            'contacts',
            'contact_approved_status',
            'organization_types',
            'countries',
            'regions',
            'mailing_categories',
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
        session()->forget('contact_approve.filter');

        return redirect(route('contacts-approve.index'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $contact               = Contact::findOrFail($id);
        $related_organizations = [];
        if ($contact->organisation != null) {
            $related_organizations = Organisation::where('id', '<>', $contact->organisation->id)
                ->where(function ($query) use ($contact) {
                    $query->where('name', $contact->organisation->name)
                        ->orWhere('domain_name', $contact->organisation->domain_name);
                })
                ->get();
        }

        return view('contacts_to_approve.show', compact('contact', 'related_organizations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $contact = Contact::findOrFail($id);

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

        return view('contacts_to_approve.edit', compact(
            'contact',
            'matchedInstitutions',
            'mailing_categories',
            'member_approved_status',
            'organization_types',
            'countries',
            'organization_levels',
            'interest_sections',
            'contactInterestSections'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ContactApproveUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactApproveUpdateRequest $request, int $id)
    {
        $contact                 = Contact::findOrFail($id);
        $oldMemberApprovedStatus = $contact->member_approved_status;

        $contact->email        = $request->contact_email;
        $domain_name           = substr($request->email, strpos($request->email, '@') + 1);
        $contact->domain_name  = $domain_name;
        $contact->title        = $request->title;
        $contact->first_name   = $request->first_name;
        $contact->last_name    = $request->last_name;
        $contact->mobile_phone = $request->mobile_phone;
        $contact->country_id   = $request->country_id;
        $contact->city         = $request->city;

        $selectInstitutionOption = $request->select_institution_option;
        if ($selectInstitutionOption === 'matched_institution') {
            if ($request->select_institution == null) {
                return redirect()->back()->withInput()->with('contact_msg', 'There is not a matched institution selected.');
            } else {
                $contact->organisation_id = $request->select_institution;
            }
        } elseif ($selectInstitutionOption === 'searched_institution') {
            if ($request->organisation_id == null) {
                return redirect()->back()->withInput()->with('contact_msg', 'There is not a searched institution selected.');
            } else {
                $contact->organisation_id = $request->organisation_id;
            }
        } elseif ($selectInstitutionOption === 'none_institution') {
            $contact->organisation_id = null;
        }

        if ($request->organisation_type != null && $request->organisation_type != $contact->organisation->organisation_type) {
            $contact->organisation()->update(['organisation_type' => $request->organisation_type]);
        }

        if ($request->country_id != null && $request->country_id != $contact->organisation->country_id) {
            $contact->organisation()->update(['country_id' => $request->country_id]);
        }

        if ($request->city != null && $request->city != $contact->organisation->city) {
            $contact->organisation()->update(['city' => $request->city]);
        }

        if ($contact->source == 'website' && $request->member_approved_status != null) {
            $contact->member_approved_status = $request->member_approved_status;
        }
        if ($request->mailing_category != null) {
            $contact->mailing_category = $request->mailing_category;
        }

        $contact->interest_sections()->sync($request->interest_section);

        $contact->update();

        if ($contact->member_approved_status != null && $contact->member_approved_status != 'cancel' && $contact->member_approved_status != $oldMemberApprovedStatus) {
            $email_from = 'request@zoo-services.com';
            $email_to   = $contact->email;

            switch ($contact->member_approved_status) {
                case 'active':
                    $contact->organisation->update(['is_approved' => true]);

                    if ($contact->country != null && $contact->country->language == 'ES') {
                        $email_subject = 'Su cuenta ha sido activada.';
                        $email_body    = view('emails.customer-registered-activated-spanish', compact('contact'))->render();
                    } else {
                        $email_subject = 'Your account has been activated.';
                        $email_body    = view('emails.customer-registered-activated', compact('contact'))->render();
                    }
                    break;
                case 'no_active':
                    $organizationApprovedContacts = $contact->organisation->contacts()->where('source', 'website')->where('member_approved_status', 'active')->get();
                    if (count($organizationApprovedContacts) == 0) {
                        $contact->organisation->update(['is_approved' => false]);
                    }

                    if ($contact->country != null && $contact->country->language == 'ES') {
                        $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                        $email_body    = view('emails.customer-registered-no-activated-spanish', compact('contact'))->render();
                    } else {
                        $email_subject = 'New member registered in International Zoo Services.';
                        $email_body    = view('emails.customer-registered-no-activated', compact('contact'))->render();
                    }
                    break;
                case 'website_not_working':
                    if ($contact->country != null && $contact->country->language == 'ES') {
                        $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                        $email_body    = view('emails.customer-registered-website-not-working-spanish', compact('contact'))->render();
                    } else {
                        $email_subject = 'New member registered in International Zoo Services.';
                        $email_body    = view('emails.customer-registered-website-not-working', compact('contact'))->render();
                    }
                    break;
                case 'question':
                    if ($contact->country != null && $contact->country->language == 'ES') {
                        $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                        $email_body    = view('emails.customer-registered-more-details-spanish', compact('contact'))->render();
                    } else {
                        $email_subject = 'New member registered in International Zoo Services.';
                        $email_body    = view('emails.customer-registered-more-details', compact('contact'))->render();
                    }
                    break;
                case 'no_websites':
                    if ($contact->country != null && $contact->country->language == 'ES') {
                        $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                        $email_body    = view('emails.customer-registered-no-website-spanish', compact('contact'))->render();
                    } else {
                        $email_subject = 'New member registered in International Zoo Services.';
                        $email_body    = view('emails.customer-registered-no-website', compact('contact'))->render();
                    }
                    break;
            }

            Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
            //Mail::to('johnrens@zoo-services.com')->cc('development@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
        }

        return redirect(route('contacts-approve.show', $contact->id));
    }

    /**
     * Quick approval option.
     *
     * @param  int id
     * @param  string option
     * @return \Illuminate\Http\Response
     */
    public function quickApprovalOption($contact_id, $option)
    {
        $contact = Contact::findOrFail($contact_id);

        $email_to   = trim($contact->email);
        $email_from = 'request@zoo-services.com';

        $contact->update(['member_approved_status' => $option]);

        switch ($option) {
            case 'active':
                $contact->organisation->update(['is_approved' => true]);

                if ($contact->country != null && $contact->country->language == 'ES') {
                    $email_subject = 'Su cuenta ha sido activada.';
                    $email_body    = view('emails.customer-registered-activated-spanish', compact('contact'))->render();
                } else {
                    $email_subject = 'Your account has been activated.';
                    $email_body    = view('emails.customer-registered-activated', compact('contact'))->render();
                }
                break;
            case 'no_active':
                $organizationApprovedContacts = $contact->organisation->contacts()->where('source', 'website')->where('member_approved_status', 'active')->get();
                if (count($organizationApprovedContacts) == 0) {
                    $contact->organisation->update(['is_approved' => false]);
                }

                if ($contact->country != null && $contact->country->language == 'ES') {
                    $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                    $email_body    = view('emails.customer-registered-no-activated-spanish', compact('contact'))->render();
                } else {
                    $email_subject = 'New member registered in International Zoo Services.';
                    $email_body    = view('emails.customer-registered-no-activated', compact('contact'))->render();
                }
                break;
            case 'website_not_working':
                if ($contact->country != null && $contact->country->language == 'ES') {
                    $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                    $email_body    = view('emails.customer-registered-website-not-working-spanish', compact('contact'))->render();
                } else {
                    $email_subject = 'New member registered in International Zoo Services.';
                    $email_body    = view('emails.customer-registered-website-not-working', compact('contact'))->render();
                }
                break;
            case 'question':
                if ($contact->country != null && $contact->country->language == 'ES') {
                    $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                    $email_body    = view('emails.customer-registered-more-details-spanish', compact('contact'))->render();
                } else {
                    $email_subject = 'New member registered in International Zoo Services.';
                    $email_body    = view('emails.customer-registered-more-details', compact('contact'))->render();
                }
                break;
            case 'no_websites':
                if ($contact->country != null && $contact->country->language == 'ES') {
                    $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                    $email_body    = view('emails.customer-registered-no-website-spanish', compact('contact'))->render();
                } else {
                    $email_subject = 'New member registered in International Zoo Services.';
                    $email_body    = view('emails.customer-registered-no-website', compact('contact'))->render();
                }
                break;
        }

        if ($option !== 'cancel') {
            Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
        }

        return redirect(route('contacts-approve.index'));
    }

    /**
     * Quick approval option.
     *
     * @param  int id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function quickSelectedApprovalOption(Request $request)
    {
        $option = $request->code;

        $email_from = 'request@zoo-services.com';

        if (count($request->items) > 0) {
            foreach ($request->items as $id) {
                $contact = Contact::where('id', $id)->first();
                $contact->update(['member_approved_status' => $option]);
                $email_to = trim($contact->email);

                switch ($option) {
                    case 'active':
                        $contact->organisation->update(['is_approved' => true]);

                        if ($contact->country != null && $contact->country->language == 'ES') {
                            $email_subject = 'Su cuenta ha sido activada.';
                            $email_body    = view('emails.customer-registered-activated-spanish', compact('contact'))->render();
                        } else {
                            $email_subject = 'Your account has been activated.';
                            $email_body    = view('emails.customer-registered-activated', compact('contact'))->render();
                        }
                        break;
                    case 'no_active':
                        $organizationApprovedContacts = $contact->organisation->contacts()->where('source', 'website')->where('member_approved_status', 'active')->get();
                        if (count($organizationApprovedContacts) == 0) {
                            $contact->organisation->update(['is_approved' => false]);
                        }

                        if ($contact->country != null && $contact->country->language == 'ES') {
                            $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                            $email_body    = view('emails.customer-registered-no-activated-spanish', compact('contact'))->render();
                        } else {
                            $email_subject = 'New member registered in International Zoo Services.';
                            $email_body    = view('emails.customer-registered-no-activated', compact('contact'))->render();
                        }
                        break;
                    case 'website_not_working':
                        if ($contact->country != null && $contact->country->language == 'ES') {
                            $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                            $email_body    = view('emails.customer-registered-website-not-working-spanish', compact('contact'))->render();
                        } else {
                            $email_subject = 'New member registered in International Zoo Services.';
                            $email_body    = view('emails.customer-registered-website-not-working', compact('contact'))->render();
                        }
                        break;
                    case 'question':
                        if ($contact->country != null && $contact->country->language == 'ES') {
                            $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                            $email_body    = view('emails.customer-registered-more-details-spanish', compact('contact'))->render();
                        } else {
                            $email_subject = 'New member registered in International Zoo Services.';
                            $email_body    = view('emails.customer-registered-more-details', compact('contact'))->render();
                        }
                        break;
                    case 'no_websites':
                        if ($contact->country != null && $contact->country->language == 'ES') {
                            $email_subject = 'Nuevo miembro registrado en International Zoo Services.';
                            $email_body    = view('emails.customer-registered-no-website-spanish', compact('contact'))->render();
                        } else {
                            $email_subject = 'New member registered in International Zoo Services.';
                            $email_body    = view('emails.customer-registered-no-website', compact('contact'))->render();
                        }
                        break;
                }

                if ($option !== 'cancel') {
                    Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    /**
     * Filter contacts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterContactsToApprove(Request $request)
    {
        // Set session crate filter
        session(['contact_approve.filter' => $request->query()]);

        return redirect(route('contacts-approve.index'));
    }

    /**
     * Remove from contact_approve session.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function removeFromContactToApproveSession($key)
    {
        $query = session('contact_approve.filter');
        Arr::forget($query, $key);
        session(['contact_approve.filter' => $query]);

        return redirect(route('contacts-approve.index'));
    }
}
