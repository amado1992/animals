<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserWebsiteContactRequest;
use App\Http\Requests\UserWebsiteLoginRequest;
use App\Http\Requests\UserWebsiteRegisterRequest;
use App\Http\Requests\UserWebsiteUpdateRequest;
use App\Mail\CustomerRegistered;
use App\Mail\UserContactUs;
use App\Mail\UserContactUsNoMember;
use App\Models\Contact;
use App\Models\GenericDomain;
use App\Models\InterestSection;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ContactWebsiteController extends Controller
{
    /**
     * Register contact from website.
     *
     * @param  \App\Http\Requests\UserWebsiteRegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $member_already_exist = Contact::where('email', $request->input('email'))
            ->where('source', 'website')
            ->first();

        if ($member_already_exist != null) {
            return response()->json(['success' => false, 'message' => 'The email is already in use.']);
        }

        $contact_same_email = Contact::where('email', $request->input('email'))
            ->where(function ($query) {
                $query->whereNull('source')
                    ->orWhere('source', 'crm');
            })
            ->first();

        $websiteRole = Role::where('name', 'website-user')->firstOrFail();

        $domain_name = substr($request->email, strpos($request->input('email'), '@') + 1);
        ///////////////////////////////////
        $interest          = [];
        $interest_sections = InterestSection::pluck('label', 'key');
        if (isset($request->interests['zoo_animals'])) {
            array_push($interest, 'Z');
        }
        if (isset($request->interests['birds'])) {
            array_push($interest, 'B');
        }
        if (isset($request->interests['aquatic_species'])) {
            array_push($interest, 'AA');
        }
        if (isset($request->interests['primates'])) {
            array_push($interest, 'PR');
        }
        if (isset($request->interests['small_mammals'])) {
            array_push($interest, 'SM');
        }
        if (isset($request->interests['reptiles'])) {
            array_push($interest, 'RAF');
        }
        if (isset($request->interests['carnivores'])) {
            array_push($interest, 'C');
        }
        ///////////////////////////////////

        $title                  = $request->title;
        $first_name             = trim($request->first_name);
        $last_name              = trim($request->last_name);
        $countryId              = $request->country;
        $city                   = trim($request->city);
        $position               = trim($request->position);
        $mobile_phone           = (isset($request->mobile_phone)) ? $request->mobile_phone : null;
        $receive_surplus_wanted = ($request->receive_surplus_wanted) ? 'all_mailings' : 'no_mailings';

        $organisation_name          = trim($request->organisation['name']);
        $organisation_type          = $request->organisation['type'];
        $phone                      = trim($request->phone);
        $website                    = trim($request->website);
        $facebook_page              = trim($request->facebook_page);
        $short_description          = trim($request->short_description);
        $public_zoos_relation       = trim($request->public_zoos_relation);
        $animal_related_association = trim($request->animal_related_association);
        $phone_extra_description    = trim($request->phone_extra_description);

        if ($contact_same_email != null) {
            $contact_same_email->source                 = 'website';
            $contact_same_email->member_approved_status = null;
            $contact_same_email->title                  = $title;
            $contact_same_email->first_name             = $first_name;
            $contact_same_email->last_name              = $last_name;
            $contact_same_email->country_id             = $countryId;
            $contact_same_email->city                   = $city;
            $contact_same_email->position               = $position;
            $contact_same_email->mobile_phone           = $mobile_phone;
            $contact_same_email->mailing_category       = $receive_surplus_wanted;
            $contact_same_email->update();

            $user = User::create([
                'name'      => trim($contact_same_email->first_name),
                'last_name' => trim($contact_same_email->last_name),
                'email'     => $contact_same_email->email,
                'password'  => bcrypt($request->password),
            ]);
            $user->attachRole($websiteRole);

            $contact_same_email->user()->associate($user);
            $contact_same_email->update();
            ///////////////////////////////////
            if (count($interest) > 0) {
                $contact_same_email->interest_sections()->sync($interest);
            }
            ///////////////////////////////////

            if ($contact_same_email->organisation == null) {
                $organization                    = new Organisation();
                $organization->name              = $organisation_name;
                $organization->organisation_type = $organisation_type;
                $organization->phone             = $phone;
                $organization->country_id        = $countryId;
                $organization->city              = $city;
                $organization->email             = $request->email;
                $organization->domain_name       = $domain_name;

                if ($website != '' || $facebook_page != '') {
                    $organization->website       = $website;
                    $organization->facebook_page = $facebook_page;
                } else {
                    $organization->short_description          = $short_description;
                    $organization->public_zoos_relation       = $public_zoos_relation;
                    $organization->animal_related_association = $animal_related_association;
                    $organization->phone_extra_description    = $phone_extra_description;
                }

                $organization->save();
                ///////////////////////////////////
                if (count($interest) > 0) {
                    $organization->interest()->sync($interest);
                }
                ///////////////////////////////////

                $contact_same_email->organisation_id = $organization->id;
                $contact_same_email->update();
            } else {
                if ($contact_same_email->organisation->name == null) {
                    $contact_same_email->organisation->name = $organisation_name;
                }
                if ($contact_same_email->organisation->organisation_type == null) {
                    $contact_same_email->organisation->organisation_type = $organisation_type;
                }
                if ($contact_same_email->organisation->phone == null) {
                    $contact_same_email->organisation->phone = $phone;
                }
                if ($contact_same_email->organisation->country_id == null) {
                    $contact_same_email->organisation->country_id = $countryId;
                }
                if ($contact_same_email->organisation->city == null) {
                    $contact_same_email->organisation->city = $city;
                }
                if ($contact_same_email->organisation->email == null) {
                    $contact_same_email->organisation->email = $request->email;
                }
                if ($contact_same_email->organisation->domain_name == null) {
                    $contact_same_email->organisation->domain_name = $request->domain_name;
                }
                if ($contact_same_email->organisation->website == null) {
                    if ($website != '' || $facebook_page != '') {
                        $contact_same_email->organisation->website       = $website;
                        $contact_same_email->organisation->facebook_page = $facebook_page;
                    } else {
                        $contact_same_email->organisation->short_description          = $short_description;
                        $contact_same_email->organisation->public_zoos_relation       = $public_zoos_relation;
                        $contact_same_email->organisation->animal_related_association = $animal_related_association;
                        $contact_same_email->organisation->phone_extra_description    = $phone_extra_description;
                    }
                }
                ///////////////////////////////////
                if ($contact_same_email->organisation->interest()->count() == 0 && count($interest) > 0) {
                    $contact_same_email->organisation->interest()->sync($interest);
                }
                ///////////////////////////////////

                $contact_same_email->organisation->update();
            }

            Mail::to($request->email)->send(new CustomerRegistered($contact_same_email));
        //Mail::to('johnrens@zoo-services.com')->cc('development@zoo-services.com')->send(new CustomerRegistered($contact_same_email));
        } else {
            $organization = null;

            $institutionAlreadyExistByName = Organisation::where('name', $organisation_name)->first();

            $genericDomains                         = GenericDomain::pluck('domain');
            $institutionAlreadyExistByDomainAndCity = Organisation::whereNotIn('domain_name', $genericDomains)
                ->where('domain_name', $domain_name)
                ->where('city', $city)
                ->first();

            if ($institutionAlreadyExistByName != null) {
                $organization = $institutionAlreadyExistByName;
            } elseif ($institutionAlreadyExistByDomainAndCity != null) {
                $organization = $institutionAlreadyExistByDomainAndCity;
            } else {
                $organization                    = new Organisation();
                $organization->name              = $organisation_name;
                $organization->organisation_type = $organisation_type;
                $organization->phone             = $phone;
                $organization->country_id        = $countryId;
                $organization->city              = $city;
                $organization->email             = $request->email;
                $organization->domain_name       = $domain_name;

                if ($website != '' || $facebook_page != '') {
                    $organization->website       = $website;
                    $organization->facebook_page = $facebook_page;
                } else {
                    $organization->short_description          = $short_description;
                    $organization->public_zoos_relation       = $public_zoos_relation;
                    $organization->animal_related_association = $animal_related_association;
                    $organization->phone_extra_description    = $phone_extra_description;
                }

                $organization->save();
                ///////////////////////////////////
                if (count($interest) > 0) {
                    $organization->interest()->sync($interest);
                }
                ///////////////////////////////////
            }

            $contact                   = new Contact();
            $contact->title            = $title;
            $contact->first_name       = $first_name;
            $contact->last_name        = $last_name;
            $contact->position         = $position;
            $contact->email            = $request->email;
            $contact->domain_name      = $domain_name;
            $contact->country_id       = $countryId;
            $contact->city             = $city;
            $contact->mobile_phone     = $mobile_phone;
            $contact->organisation_id  = $organization->id;
            $contact->source           = 'website';
            $contact->mailing_category = $receive_surplus_wanted;
            $contact->save();
            ///////////////////////////////////
            if (count($interest) > 0) {
                $contact->interest_sections()->sync($interest);
            }
            ///////////////////////////////////

            $user = User::create([
                'name'      => trim($contact->first_name),
                'last_name' => trim($contact->last_name),
                'email'     => $contact->email,
                'password'  => bcrypt($request->password),
            ]);
            $user->attachRole($websiteRole);

            $contact->user()->associate($user);
            $contact->save();

            Mail::to($request->email)->send(new CustomerRegistered($contact));
            //Mail::to('johnrens@zoo-services.com')->cc('development@zoo-services.com')->send(new CustomerRegistered($contact));
        }

        return response()->json(['success' => true, 'message' => 'The user is successfully registered.']);
    }

    /**
     * Member login from website.
     *
     * @param  \App\Http\Requests\UserWebsiteLoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(UserWebsiteLoginRequest $request)
    {
        $user_website = User::whereRoleIs('website-user')->where('email', $request->input('email'))->first();

        if ($user_website && Auth::attempt($request->only(['email', 'password']))) {
            $token = $user_website->createToken($user_website->email)->plainTextToken;

            return response()->json(['success' => true, 'message' => 'Welcome.', 'token' => $token], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid username or password.'], 403);
        }
    }

    /**
     * Member logout from website.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            session()->flush();

            return response()->json(['success' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false], 400);
        }
    }

    /**
     * Reset password link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['success' => true, 'status' => __($status)], 200)
                    : response()->json(['success' => false, 'status' => __($status)], 400);

        /*return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);*/
    }

    /**
     * Update member user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? response()->json(['success' => true, 'status' => __($status)], 200)
                    : response()->json(['success' => false, 'status' => __($status)], 400);

        /*return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);*/
    }

    /**
     * Get member info from website.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMemberSettings(Request $request)
    {
        // validate if given userId is the same as the loggedin user
        /*if( $request->id !== auth()->user()->id )
            return response()->json(['success'=> false, 'message' => 'Invalid user.'], 404);*/

        $user_member = User::where('id', $request->input('id'))->first();

        if ($user_member == null) {
            return response()->json(['success' => false, 'message' => 'Invalid user.']);
        } elseif ($user_member->contact == null) {
            return response()->json(['success' => false, 'message' => 'Invalid user.']);
        } else {
            $contact = $user_member->contact;

            $data = [
                'id'                         => $contact->id,
                'title'                      => $contact->title,
                'first_name'                 => $contact->first_name,
                'last_name'                  => $contact->last_name,
                'position'                   => $contact->position,
                'email'                      => $contact->email,
                'mobile_phone'               => $contact->mobile_phone,
                'country'                    => $contact->country->name,
                'city'                       => $contact->organisation->city,
                'organisation'               => ['name' => $contact->organisation->name, 'type' => $contact->organisation->organisation_type],
                'phone'                      => $contact->organisation->phone,
                'website'                    => $contact->organisation->website,
                'facebook_page'              => $contact->organisation->facebook_page,
                'short_description'          => $contact->organisation->short_description,
                'public_zoos_relation'       => $contact->organisation->public_zoos_relation,
                'animal_related_association' => $contact->organisation->animal_related_association,
                'phone_extra_description'    => $contact->organisation->phone_extra_description,
                'receive_surplus_wanted'     => ($contact->mailing_category == 'all_mailings') ? true : false,
            ];

            $interests = [];
            foreach ($contact->organisation->interest as $interest_section) {
                switch ($interest_section->key) {
                    case 'Z':
                        $interests['zoo_animals'] = true;
                        break;
                    case 'B':
                        $interests['birds'] = true;
                        break;
                    case 'AA':
                        $interests['aquatic_species'] = true;
                        break;
                    case 'PR':
                        $interests['primates'] = true;
                        break;
                    case 'SM':
                        $interests['small_mammals'] = true;
                        break;
                    case 'RAF':
                        $interests['reptiles'] = true;
                        break;
                    case 'C':
                        $interests['carnivores'] = true;
                        break;
                }
            }
            $data['interests'] = $interests;

            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    /**
     * Update member info from website.
     *
     * @param  \App\Http\Requests\UserWebsiteUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateMemberSettings(UserWebsiteUpdateRequest $request)
    {
        /*
            Things to have in mind:
            1- If the organization name is different, then the organization name will change.
            We will not create a new organization.
        */

        // validate if given userId is the same as the loggedin user
        /*if( $request->id !== auth()->user()->id )
            return response()->json(['success'=> false, 'message' => 'Invalid user.'], 404);*/

        $member = Contact::where('user_id', $request->id)->first();

        ///////////////////////////////////
        $interest          = [];
        $interest_sections = InterestSection::pluck('label', 'key');
        if ($request->filled('interests')) {
            if (isset($request->interests['zoo_animals'])) {
                array_push($interest, 'Z');
            }
            if (isset($request->interests['birds'])) {
                array_push($interest, 'B');
            }
            if (isset($request->interests['aquatic_species'])) {
                array_push($interest, 'AA');
            }
            if (isset($request->interests['primates'])) {
                array_push($interest, 'PR');
            }
            if (isset($request->interests['small_mammals'])) {
                array_push($interest, 'SM');
            }
            if (isset($request->interests['reptiles'])) {
                array_push($interest, 'RAF');
            }
            if (isset($request->interests['carnivores'])) {
                array_push($interest, 'C');
            }
        }
        ///////////////////////////////////

        if (empty($member->email) || $member->email != $request->email) {
            $member_already_exist = Contact::IsApproved()
                ->where('email', $request->email)
                ->first();

            if ($member_already_exist != null) {
                return response()->json(['success' => false, 'message' => 'The email is already in use by other member.']);
            } else {
                $contact_same_email = Contact::where('email', $request->email)
                    ->where(function ($query) {
                        $query->whereNull('source')
                            ->orWhere('source', 'crm');
                    })
                    ->first();

                if ($contact_same_email != null) {
                    if ($contact_same_email->surpluses->count() > 0) {
                        $member->surpluses()->saveMany($contact_same_email->surpluses);
                    }
                    if ($contact_same_email->wanteds->count() > 0) {
                        $member->wanteds()->saveMany($contact_same_email->wanteds);
                    }
                    if ($contact_same_email->offers->count() > 0) {
                        $member->offers()->saveMany($contact_same_email->offers);
                    }
                    if ($contact_same_email->offers->count() > 0) {
                        $member->orders()->saveMany($contact_same_email->orders);
                    }
                    if ($contact_same_email->offers->count() > 0) {
                        $member->invoices()->saveMany($contact_same_email->invoices);
                    }

                    $contact_same_email->delete();
                }

                $member->email       = $request->email;
                $member->user->email = $request->email;

                $domain_name                       = substr($request->email, strpos($request->email, '@') + 1);
                $member->organisation->domain_name = $domain_name;
                $member->domain_name               = $domain_name;
            }
        }

        if ($request->filled('title') && $member->title != $request->title) {
            $member->title = $request->title;
        }
        if ($request->filled('first_name') && $member->first_name != $request->first_name) {
            $member->first_name = trim($request->first_name);
            $member->user->name = trim($request->first_name);
        }
        if ($request->filled('last_name') && $member->last_name != $request->last_name) {
            $member->last_name       = trim($request->last_name);
            $member->user->last_name = trim($request->last_name);
        }
        if ($request->filled('country') && $member->country_id != $request->country) {
            $member->country_id = $request->country;
        }
        if ($request->filled('city') && $member->city != $request->city) {
            $member->city = trim($request->city);
        }
        if ($request->filled('position') && trim($request->position) != '' && $member->position != $request->position) {
            $member->position = trim($request->position);
        }
        if ($request->filled('mobile_phone')) {
            $member->mobile_phone = trim($request->mobile_phone);
        }
        if ($request->filled('receive_surplus_wanted')) {
            if ($request->receive_surplus_wanted == true) {
                $member->mailing_category = 'all_mailings';
            } else {
                $member->mailing_category = 'no_mailings';
            }
        }

        $member->update();

        if ($request->filled('organisation')) {
            if ($member->organisation->name != $request->organisation['name']) {
                $member->organisation->name = trim($request->organisation['name']);
            }
            if ($member->organisation->organisation_type != $request->organisation['type']) {
                $member->organisation->organisation_type = $request->organisation['type'];
            }
        }
        if ($request->filled('country') && $member->organisation->country_id != $request->country) {
            $member->organisation->country_id = $request->country;
        }
        if ($request->filled('city') && $member->organisation->city != $request->city) {
            $member->organisation->city = trim($request->city);
        }
        if ($request->filled('website')) {
            $member->organisation->website = trim($request->website);
        }
        if ($request->filled('facebook_page')) {
            $member->organisation->facebook_page = trim($request->facebook_page);
        }
        if ($request->filled('short_description')) {
            $member->organisation->short_description = trim($request->short_description);
        }
        if ($request->filled('public_zoos_relation')) {
            $member->organisation->public_zoos_relation = trim($request->public_zoos_relation);
        }
        if ($request->filled('animal_related_association')) {
            $member->organisation->animal_related_association = trim($request->animal_related_association);
        }
        if ($request->filled('phone_extra_description')) {
            $member->organisation->phone_extra_description = trim($request->phone_extra_description);
        }

        if (count($interest) > 0) {
            $member->organisation->interest()->sync($interest);
            $member->interest_sections()->sync($interest);
        }

        $member->organisation->update();

        if (!Hash::check($request->password, $member->user->password)) {
            $member->user()->password = bcrypt($request->password);
        }

        $member->user->update();

        return response()->json(['success' => true, 'message' => 'Contact updated successfully.']);
    }

    /**
     * Member contact us.
     *
     * @param  \App\Http\Requests\UserWebsiteContactRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function contactUs(UserWebsiteContactRequest $request)
    {
        // Available parameters
        //          department
        //          message
        //          user ( via auth()->user() )

        // validate if given userId is the same as the loggedin user
        if ($request->id !== auth()->user()->id) {
            return response()->json(['success' => false, 'message' => 'Invalid user.'], 404);
        }

        $contact = Contact::where('user_id', auth()->user()->id);

        $department = $request->department;
        $message    = $request->message;

        // Send message, based on the selected department
        Mail::to('izs@zoo-services.com')->send(new UserContactUs($contact, $department, $message));

        return response()->json(['success' => true]);
    }

    /**
     * No members contact us.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function contactUsNoMember(Request $request)
    {
        // Available parameters
        //          name
        //          institution
        //          email
        //          country
        //          message

        // Send message, based on the selected department
        Mail::to('izs@zoo-services.com')->send(
            new UserContactUsNoMember(
                $request->name,
                $request->institution,
                $request->email,
                $request->country,
                $request->message
            )
        );

        return response()->json(['success' => true]);
    }
}
