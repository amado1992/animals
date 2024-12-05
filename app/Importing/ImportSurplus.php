<?php

namespace App\Importing;

use App\Enums\AgeGroup;
use App\Enums\Origin;
use App\Enums\Size;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Role;
use App\Models\Surplus;
use App\Models\User;
use DateTimeHelper;
use DB;
use Illuminate\Support\Arr;

class ImportSurplus
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
        $surpluses = DB::connection('mysql_old')->select('select * from surplus2 where deletedsurplus2 = 0 order by idsurplus2');

        $oldAdmins = DB::connection('mysql_old')->select('select * from user where deleteduser = 0 and rol = "admin" order by id');

        $contacts    = Contact::pluck('id', 'old_id');
        $roles       = Role::where('name', '<>', 'website-user')->where('name', '<>', 'bookkeeper')->get();
        $admins      = User::whereRoleIs(Arr::pluck($roles, 'name'))->get();
        $countries   = Country::pluck('id', 'old_id');
        $areaRegions = AreaRegion::pluck('name', 'id');
        $animals     = Animal::pluck('id', 'old_id');

        $origin   = Origin::get();
        $ageGroup = AgeGroup::get();
        $size     = Size::get();

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($surpluses as $surplus) {
            if ($surplus->idanimal != null && $surplus->idanimal != 0 && isset($animals[$surplus->idanimal])) {
                $oldAdmin = null;
                $admin    = null;
                if ($surplus->whoInserted != null && $surplus->whoInserted != 0) {
                    $oldAdmin = collect($oldAdmins)->where('id', $surplus->whoInserted)->first();

                    if (!is_null($oldAdmin)) {
                        $admin = $admins->where('email', $oldAdmin->email)->first();
                    }
                }

                $country_id     = null;
                $area_region_id = null;
                if (trim($surplus->surplus2Continent) != '') {
                    $area_region_id = (isset($areaRegions[$surplus->surplus2Continent])) ? $areaRegions[$surplus->surplus2Continent] : null;
                }
                if ($surplus->idcountry > 0) {
                    $country = Country::where('old_id', $surplus->idcountry)->first();

                    if ($country != null) {
                        $country_id = $country->id;

                        if ($area_region_id == null && $country->region != null) {
                            $area_region_id = $country->region->area_region_id;
                        }
                    }
                }

                $surplusStatus = null;
                switch ($surplus->checkedIndication) {
                    case 'N':
                        $surplusStatus = 'archive';
                        break;
                    case 'IC':
                        $surplusStatus = 'collection';
                        break;
                    case 'C':
                        $surplusStatus = 'real_details';
                        break;
                    default:
                        $surplusStatus = 'unknown';
                        break;
                }
                /*array_push($mappedArray, [
                    'contact_id' => (isset($contacts[$surplus->iduser])) ? $contacts[$surplus->iduser] : null,
                    'animal_id' => $animals[$surplus->idanimal],
                    'quantityM' => $surplus->quantitiesm,
                    'quantityF' => $surplus->quantitiesf,
                    'quantityU' => $surplus->quantitiesu,
                    'country_id' => $country_id,
                    'area_region_id' => $area_region_id,
                    'surplus_status' => $surplusStatus,
                    'origin' => (collect($origin)->contains($surplus->bred)) ? Origin::getIndex($surplus->bred) : null,
                    'age_group' => (collect($ageGroup)->contains($surplus->age)) ? AgeGroup::getIndex($surplus->age) : null,
                    'bornYear' => $surplus->bornYear,
                    'size' => (collect($size)->contains($surplus->size)) ? Size::getIndex($surplus->size) : null,
                    'remarks' => $surplus->remarks,
                    'intern_remarks' => $surplus->internremarks,
                    'special_conditions' => $surplus->special_conditions,
                    'cost_currency' => $surplus->currency,
                    'costPriceM' => $surplus->mprice,
                    'costPriceF' => $surplus->fprice,
                    'costPriceU' => $surplus->uprice,
                    'costPriceP' => $surplus->pprice,
                    'sale_currency' => $surplus->salecurrency,
                    'salePriceM' => $surplus->mpriceown,
                    'salePriceF' => $surplus->fpriceown,
                    'salePriceU' => $surplus->upriceown,
                    'salePriceP' => $surplus->ppriceown,
                    'to_members' => ($surplus->toMembers != null) ? $surplus->toMembers : 0,
                    'to_members_date' => $surplus->toMembersDate,
                    'warning_indication' => ($surplus->warningIndication != null) ? $surplus->warningIndication : 0,
                    'inserted_by' => (!is_null($user)) ? $user->id : null
                ]);*/

                $mappedArray = [
                    'old_id'             => $surplus->idsurplus2,
                    'contact_id'         => (isset($contacts[$surplus->iduser])) ? $contacts[$surplus->iduser] : null,
                    'animal_id'          => $animals[$surplus->idanimal],
                    'quantityM'          => $surplus->quantitiesm,
                    'quantityF'          => $surplus->quantitiesf,
                    'quantityU'          => $surplus->quantitiesu,
                    'country_id'         => $country_id,
                    'area_region_id'     => $area_region_id,
                    'surplus_status'     => $surplusStatus,
                    'origin'             => (collect($origin)->contains($surplus->bred)) ? Origin::getIndex($surplus->bred) : null,
                    'age_group'          => (collect($ageGroup)->contains($surplus->age)) ? AgeGroup::getIndex($surplus->age) : null,
                    'bornYear'           => $surplus->bornYear,
                    'size'               => (collect($size)->contains($surplus->size)) ? Size::getIndex($surplus->size) : null,
                    'remarks'            => (trim($surplus->remarks)            != '') ? $surplus->remarks : null,
                    'intern_remarks'     => (trim($surplus->internremarks)      != '') ? $surplus->internremarks : null,
                    'special_conditions' => (trim($surplus->special_conditions) != '') ? $surplus->special_conditions : null,
                    'cost_currency'      => (trim($surplus->currency)           != '' && $surplus->currency           != 'null') ? $surplus->currency : 'EUR',
                    'costPriceM'         => $surplus->mprice,
                    'costPriceF'         => $surplus->fprice,
                    'costPriceU'         => $surplus->uprice,
                    'costPriceP'         => $surplus->pprice,
                    'sale_currency'      => (trim($surplus->salecurrency) != '' && $surplus->salecurrency != 'null') ? $surplus->salecurrency : 'EUR',
                    'salePriceM'         => $surplus->mpriceown,
                    'salePriceF'         => $surplus->fpriceown,
                    'salePriceU'         => $surplus->upriceown,
                    'salePriceP'         => $surplus->ppriceown,
                    'to_members'         => ($surplus->toMembers != null) ? $surplus->toMembers : 0,
                    'to_members_date'    => $surplus->toMembersDate,
                    'warning_indication' => ($surplus->warningIndication != null) ? $surplus->warningIndication : 0,
                    'inserted_by'        => (!is_null($admin)) ? $admin->id : null,
                    'created_at'         => ($surplus->datecreated_surplus2  != null && DateTimeHelper::validateDate($surplus->datecreated_surplus2)) ? date('Y-m-d H:i:s', strtotime($surplus->datecreated_surplus2)) : null,
                    'updated_at'         => ($surplus->datemodified_surplus2 != null && DateTimeHelper::validateDate($surplus->datemodified_surplus2)) ? date('Y-m-d H:i:s', strtotime($surplus->datemodified_surplus2)) : null,
                ];

                Surplus::create($mappedArray);

                $count_imported_records++;
            }
        }

        //Surplus::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
