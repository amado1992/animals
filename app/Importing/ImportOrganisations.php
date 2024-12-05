<?php

namespace App\Importing;

use App\Enums\OrganisationInfoStatus;
use App\Enums\OrganisationLevel;
use App\Models\Association;
use App\Models\Country;
use App\Models\InterestSection;
use App\Models\Organisation;
use App\Models\OrganisationType;
use Carbon\Carbon;
use DB;

class ImportOrganisations
{
    /**
     * Create a new import instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$mappedArray = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();

        try {
            DB::beginTransaction();

            $countries = Country::pluck('id', 'name');
            $types     = OrganisationType::pluck('label', 'key');

            $levels       = OrganisationLevel::get();
            $infoStatuses = OrganisationInfoStatus::get();

            $interest_sections = InterestSection::pluck('label', 'key');
            $associations      = Association::pluck('label', 'key');

            $users = DB::connection('mysql_old')->select('select * from user where id > 112556 and (rol != "admin" and rol != "transport") and email != "" and typeofinstitution != "" and deleteduser != 1');

            foreach ($users as $user) {
                if (trim($user->nameinstitution) == '' && trim($user->email) == '') {
                    continue;
                }

                $organisation_name = (trim($user->nameinstitution) != '') ? $this->content_iconv(trim($user->nameinstitution)) : ($user->email ?: '');

                $country = isset($countries[$user->country]) ? $countries[$user->country] : null;

                $given_level = array_search(trim($user->level), $levels);
                $level       = $given_level ? OrganisationLevel::getIndex($given_level) : null;
                $type        = isset($types[$user->typeofinstitution]) ? $user->typeofinstitution : null;
                $infoStatus  = (trim($user->pStatus) != '' && collect($infoStatuses)->contains($user->pStatus)) ? OrganisationInfoStatus::getIndex($user->pStatus) : null;

                $domain_name = (trim($user->domainName) != '') ? $user->domainName : substr($user->email, strpos($user->email, '@') + 1);

                // is there is a same institution with same country and name, skip for now
                if ($country != null && $organisation_name && trim($organisation_name) != '') {
                    if (Organisation::where(['country_id' => $country, 'name' => $organisation_name])->exists()) {
                        $count_skipped_records++;
                        $skipped_records->push('organisations; ' . $organisation_name . '; name and country already in database \n');
                        continue;
                    }
                }

                if (strlen($user->nameinstitution) > 30) {
                    $skipped_records->push('organisations; ' . $organisation_name . '; name is longer than 30 chars \n');
                }

                $fax = (string) substr(trim(utf8_encode($user->fax)), 0, 30);

                if (!mb_check_encoding($fax, 'UTF-8')) {
                    $skipped_records->push('organisations; ' . $organisation_name . '; fax is not correct \n');
                    $fax = null;
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
                if ($user->pAssociations != null && trim($user->pAssociations) != '') {
                    $user_associations = explode(',', $user->pAssociations);
                    foreach ($user_associations as $user_association) {
                        if (isset($associations[$user_association])) {
                            array_push($associations_array, $user_association);
                        }
                    }
                }

                $organisation = Organisation::create([
                    'name'                       => $organisation_name,
                    'domain_name'                => (trim($domain_name) != '') ? $domain_name : null,
                    'email'                      => (trim($user->email) != '') ? $user->email : null,
                    'phone'                      => $user->phone ? trim($user->phone) : null,
                    'fax'                        => $fax,
                    'city'                       => (trim($user->city) != '') ? $user->city : null,
                    'level'                      => $level,
                    'address'                    => (trim($user->postaladdress) != '') ? $user->postaladdress : null,
                    'zipcode'                    => (trim($user->postalcode)    != '') ? $user->postalcode : null,
                    'country_id'                 => $country,
                    'website'                    => (trim($user->website)  != '') ? $user->website : null,
                    'facebook_page'              => (trim($user->website2) != '') ? $user->website2 : null,
                    'info_status'                => $infoStatus,
                    'open_remarks'               => $user->userremarks ? trim($user->userremarks) : null,
                    'internal_remarks'           => $user->internal_remarks ? trim($user->internal_remarks) : null,
                    'organisation_type'          => $type,
                    'short_description'          => (trim($user->shortDescription)   != '') ? $user->shortDescription : null,
                    'public_zoos_relation'       => (trim($user->publicZoos)         != '') ? $user->publicZoos : null,
                    'animal_related_association' => (trim($user->relatedAssociation) != '') ? $user->relatedAssociation : null,
                    'is_approved'                => ($user->registered_himself && trim($user->active) == 'yes') ? true : false,
                ]);

                if (count($interest_array) > 0) {
                    $organisation->interest()->sync($interest_array);
                }

                if (count($associations_array) > 0) {
                    $organisation->associations()->sync($associations_array);
                }

                $count_imported_records++;
            }

            DB::commit();

            if (count($skipped_records) > 0) {
                \Storage::disk('local')->put('import_skip_organisations.csv', $skipped_records->toArray());
            }

            return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
        } catch (\Throwable $th) {
            DB::rollBack();

            echo 'Rolled back import organisations after ' . $count_imported_records . ' succesfull records and skipped ' . $count_skipped_records . ' records';

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
