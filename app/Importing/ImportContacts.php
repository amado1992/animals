<?php

namespace App\Importing;

use App\Enums\ContactApprovedStatus;
use App\Enums\ContactMailingCategory;
use App\Models\Association;
use App\Models\Contact;
use App\Models\Country;
use App\Models\InterestSection;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use DateTimeHelper;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportContacts
{
    public $batch;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $batchSize = 5000;

        $skip = ($this->batch ? $this->batch : 0) * $batchSize;
        $take = $batchSize;

        //$mappedArray = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();

        try {
            DB::beginTransaction();

            $users = DB::connection('mysql_old')->select('select * from user where id > 112556 and (rol != "admin" and rol != "transport") and deleteduser != 1 order by id LIMIT ' . $skip . ', ' . $take);

            $oldAdmins = DB::connection('mysql_old')->select('select * from user where deleteduser = 0 and rol = "admin" order by id');

            $roles  = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
            $admins = User::whereRoleIs(Arr::pluck($roles, 'name'))->get();

            $countries         = Country::pluck('id', 'name');
            $organisations     = Organisation::select(['id', 'name', 'country_id'])->get();
            $approvedStatuses  = ContactApprovedStatus::get();
            $mailingCategories = ContactMailingCategory::get();
            $contactTitles     = ['Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'];
            $interest_sections = InterestSection::pluck('label', 'key');
            $associations      = Association::pluck('label', 'key');

            $websiteRole = Role::where('name', 'website-user')->firstOrFail();

            foreach ($users as $user) {
                if (trim($user->nameinstitution) == '' && trim($user->email) == '') {
                    continue;
                }

                // skip user that has no personal details
                // if( !trim($user->firstname) && !trim($user->lastname) ) {
                //     $count_skipped_records++;
                //     $skipped_records->push($user->id . '. User ('. $user->email . ') has no personal details');
                //     continue;
                // }

                $domain_name = (trim($user->domainName) != '') ? $user->domainName : substr($user->email, strpos($user->email, '@') + 1);
                $country     = isset($countries[$user->country]) ? $countries[$user->country] : null;
                $first_name  = (trim($user->firstname) != '') ? $this->content_iconv(trim($user->firstname)) : null;
                $last_name   = (trim($user->lastname)  != '') ? $this->content_iconv(substr(trim($user->lastname), 0, 49)) : null;

                $contactsSameEmailWithOffersOrders = [];
                // check if user email address is already in database
                if (trim($user->email) != '' && Contact::where('email', $user->email)->exists()) {
                    if (Contact::where('email', $user->email)->exists() && $user->ismember == 'yes' && $user->registered_himself == 1) {
                        $contactsWithSameEmail = Contact::where('email', $user->email)->get();
                        foreach ($contactsWithSameEmail as $contact) {
                            if ($contact->surpluses->count() > 0 || $contact->wanteds->count() > 0 || $contact->offers->count() > 0 || $contact->orders_contact_client->count() > 0 || $contact->orders_contact_supplier->count() > 0 || $contact->orders_contact_origin->count() > 0 || $contact->orders_contact_destination->count() > 0 || $contact->invoices->count() > 0) {
                                $contact->update(['email' => 'to_delete' . Str::random(5) . '@delete.com']);
                                array_push($contactsSameEmailWithOffersOrders, $contact);
                            } else {
                                $contact->forceDelete();
                            }
                        }
                    } elseif (trim($user->email2) == '' || Contact::where('email', $user->email2)->exists()) { // try email2, if that is empty or already in db, set email to empty
                        $user->email = null;
                    } else {
                        // email2 is not known yet, take that one
                        $user->email = $user->email2;
                    }
                }

                $organisation = null;
                if ($country != null && trim($user->nameinstitution) != '') {
                    $organisation = $organisations->where('country_id', $country)->where('name', trim($user->nameinstitution))->first();
                }

                $contactTitle    = (trim($user->tittle)          != '' && isset($contactTitles[trim($user->tittle)]) ? $user->tittle : null);
                $mailingCategory = (trim($user->mailingCategory) != '' && collect($mailingCategories)->contains($user->mailingCategory)) ? ContactMailingCategory::getIndex($user->mailingCategory) : null;

                $approvedStatus = null;
                $active_value   = $user->active;
                if ($user->registered_himself && trim($active_value) != '') {
                    switch ($active_value) {
                        case 'yes':
                            $approvedStatus = 'active';
                            break;
                        case 'no':
                            $approvedStatus = 'no_active';
                            break;
                        default:
                            $approvedStatus = (isset($approvedStatuses[trim($active_value)])) ? $active_value : null;
                            break;
                    }
                }

                $interest_array = [];
                if ($user->interest != null && trim($user->interest) != '') {
                    $sections = explode(',', $user->interest);
                    foreach ($sections as $section) {
                        if (isset($interest_sections[$section])) {
                            array_push($interest_array, $section);
                        }
                    }
                }

                $associations_array = [];
                if ($organisation != null && count($organisation->associations) == 0 && $user->pAssociations != null && trim($user->pAssociations) != '') {
                    $user_associations = explode(',', $user->pAssociations);
                    foreach ($user_associations as $user_association) {
                        if (isset($associations[$user_association])) {
                            array_push($associations_array, $user_association);
                        }
                    }

                    $organisation->associations()->sync($associations_array);
                }

                $oldAdmin = null;
                $admin    = null;
                if ($user->whoInserted != null && $user->whoInserted != 0) {
                    $oldAdmin = collect($oldAdmins)->where('id', $user->whoInserted)->first();

                    if (!is_null($oldAdmin)) {
                        $admin = $admins->where('email', $oldAdmin->email)->first();
                    }
                }

                $contact = Contact::create([
                    'old_id'                 => $user->id,
                    'first_name'             => $first_name,
                    'last_name'              => $last_name,
                    'title'                  => $contactTitle,
                    'position'               => trim($user->position),
                    'email'                  => (trim($user->email) != '') ? $user->email : null,
                    'domain_name'            => (trim($domain_name) != '') ? $domain_name : null,
                    'country_id'             => $country,
                    'city'                   => (trim($user->city) != '') ? $user->city : null,
                    'mobile_phone'           => ($user->mobilephone ? trim($user->mobilephone) : null),
                    'organisation_id'        => ($organisation != null) ? $organisation->id : null,
                    'source'                 => ($user->registered_himself ? 'website' : 'crm'),
                    'member_approved_status' => $approvedStatus,
                    'mailing_category'       => $mailingCategory,
                    'inserted_by'            => (!is_null($admin)) ? $admin->id : null,
                    'created_at'             => ($user->datecreated_user  != null && DateTimeHelper::validateDate($user->datecreated_user)) ? date('Y-m-d H:i:s', strtotime($user->datecreated_user)) : null,
                    'updated_at'             => ($user->datemodified_user != null && DateTimeHelper::validateDate($user->datemodified_user)) ? date('Y-m-d H:i:s', strtotime($user->datemodified_user)) : null,
                ]);

                if (count($interest_array) > 0) {
                    $contact->interest_sections()->sync($interest_array);
                }

                foreach ($contactsSameEmailWithOffersOrders as $contactToDelete) {
                    $contact->surpluses()->saveMany($contactToDelete->surpluses);
                    $contact->wanteds()->saveMany($contactToDelete->wanteds);
                    $contact->offers()->saveMany($contactToDelete->offers);
                    $contact->orders_contact_client()->saveMany($contactToDelete->orders_contact_client);
                    $contact->orders_contact_supplier()->saveMany($contactToDelete->orders_contact_supplier);
                    $contact->orders_contact_origin()->saveMany($contactToDelete->orders_contact_origin);
                    $contact->orders_contact_destination()->saveMany($contactToDelete->orders_contact_destination);
                    $contact->invoices()->saveMany($contactToDelete->invoices);
                    $contact->update();

                    $contactToDelete->surpluses()->forceDelete();
                    $contactToDelete->wanteds()->forceDelete();
                    $contactToDelete->offers()->forceDelete();
                    $contactToDelete->orders_contact_client()->forceDelete();
                    $contactToDelete->orders_contact_supplier()->forceDelete();
                    $contactToDelete->orders_contact_origin()->forceDelete();
                    $contactToDelete->orders_contact_destination()->forceDelete();
                    $contactToDelete->invoices()->forceDelete();
                    $contactToDelete->interest_sections()->forceDelete();
                    $contactToDelete->forceDelete();
                }

                if ($user->ismember == 'yes' && $user->registered_himself == 1) {
                    if (trim($user->email) == '' || $user->email == null) { // Skip if there is no email
                        $skipped_records->push($first_name . ' ' . $last_name . ' has no email');
                        $count_skipped_records++;
                        continue;
                    } elseif (!$user->password_clear) { // Skip if there is no password clear
                        $skipped_records->push($first_name . ' ' . $last_name . ' has no password clear');
                        $count_skipped_records++;
                        continue;
                    } elseif (User::where('email', $user->email)->exists()) {
                        $skipped_records->push($first_name . ' ' . $last_name . ' email already exist in users table');
                        $count_skipped_records++;
                        continue;
                    } else {
                        $user = User::create([
                            'name'      => ($first_name != null) ? $first_name : 'no name',
                            'last_name' => ($last_name  != null) ? $last_name : 'no lastname',
                            'email'     => $user->email,
                            'password'  => bcrypt($user->password_clear),
                        ]);
                        $user->attachRole($websiteRole);

                        $contact->user()->associate($user);
                        $contact->save();
                    }
                }

                $count_imported_records++;
            }

            DB::commit();

            if (count($skipped_records) > 0) {
                \Storage::disk('local')->put('import_skip_contacts', $skipped_records->toArray());
            }

            return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
        } catch (\Throwable $th) {
            DB::rollBack();

            echo 'Rolled back import contacts after ' . $count_imported_records . ' succesfull records and skipped ' . $count_skipped_records . ' records';

            throw $th;
        }
    }

    /**
     * Test and change encoded
     *
     * @return int
     */
    public function content_iconv($data, $to = 'utf-8')
    {
        $encode_array = ['UTF-8', 'ASCII', 'GBK', 'GB2312', 'BIG5', 'JIS', 'eucjp-win', 'sjis-win', 'EUC-JP'];
        $encoded      = mb_detect_encoding($data, $encode_array);
        $to           = strtoupper($to);
        if ($encoded != $to) {
            dump($encoded);
            $data = mb_convert_encoding($data, 'utf-8', $encoded);
            dd($data);
        }

        return $data;
    }
}
