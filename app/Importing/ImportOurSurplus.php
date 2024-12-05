<?php

namespace App\Importing;

use App\Enums\AgeGroup;
use App\Enums\Origin;
use App\Enums\Size;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\Country;
use App\Models\OurSurplus;
use App\Models\Region;
use DateTimeHelper;
use DB;

class ImportOurSurplus
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
        $standardSurpluses = DB::connection('mysql_old')->select('select * from stdprice where deletedstdprice = 0 order by idstdprice');

        $countries   = Country::pluck('id', 'old_id');
        $regions     = Region::pluck('id', 'name');
        $areaRegions = AreaRegion::pluck('id', 'name');
        $animals     = Animal::pluck('id', 'old_id');

        $origin   = Origin::get();
        $ageGroup = AgeGroup::get();
        $size     = Size::get();

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($standardSurpluses as $ourSurplus) {
            if ($ourSurplus->idanimal != null && $ourSurplus->idanimal != 0 && isset($animals[$ourSurplus->idanimal])) {
                $regions_array = [];
                if ($ourSurplus->continents != null && trim($ourSurplus->continents) != '') {
                    $regions_name = explode(',', $ourSurplus->continents);
                    foreach ($regions_name as $region_name) {
                        if (isset($areaRegions[$region_name])) {
                            array_push($regions_array, $areaRegions[$region_name]);
                        }
                    }
                }

                $area_region_id = null;
                if (trim($ourSurplus->stdPriceAreaContinent) != '') {
                    $area_region_id = (isset($areaRegions[$ourSurplus->stdPriceAreaContinent])) ? $areaRegions[$ourSurplus->stdPriceAreaContinent] : null;
                }

                $region_id = null;
                if (trim($ourSurplus->stdPriceContinent) != '') {
                    $region_id = (isset($regions[$ourSurplus->stdPriceContinent])) ? $regions[$ourSurplus->stdPriceContinent] : null;
                }

                if ($region_id == null) {
                    if ($ourSurplus->idcountry > 0) {
                        $country = Country::where('old_id', $ourSurplus->idcountry)->first();

                        if ($country != null && $country->region != null) {
                            $region_id      = $country->region->id;
                            $area_region_id = $country->region->area_region_id;
                        }
                    }
                }

                /*array_push( $mappedArray, [
                    'animal_id' => (isset($animals[$ourSurplus->idanimal])) ? $animals[$ourSurplus->idanimal] : null,
                    'quantityM' => $ourSurplus->quantitiesm,
                    'quantityF' => $ourSurplus->quantitiesf,
                    'quantityU' => $ourSurplus->quantitiesu,
                    'country_id' => (isset($countries[$ourSurplus->idcountry])) ? $countries[$ourSurplus->idcountry] : null,
                    'origin' => (collect($origin)->contains($ourSurplus->bred)) ? Origin::getIndex($ourSurplus->bred) : null,
                    'age_group' => (collect($ageGroup)->contains($ourSurplus->age)) ? AgeGroup::getIndex($ourSurplus->age) : null,
                    'bornYear' => $ourSurplus->bornYear,
                    'size' => (collect($size)->contains($ourSurplus->size)) ? Size::getIndex($ourSurplus->size) : null,
                    'remarks' => $ourSurplus->remarks,
                    'intern_remarks' => $ourSurplus->internremarks,
                    'special_conditions' => $ourSurplus->special_conditions,
                    'sale_currency' => $ourSurplus->currency,
                    'salePriceM' => $ourSurplus->pricerealm,
                    'salePriceF' => $ourSurplus->pricerealf,
                    'salePriceU' => $ourSurplus->pricerealu,
                    'salePriceP' => $ourSurplus->pricerealp,
                    'is_public' => ($ourSurplus->public != null) ? $ourSurplus->public : 0
                ]);*/

                $mappedArray = [
                    'old_id'             => $ourSurplus->idstdprice,
                    'animal_id'          => $animals[$ourSurplus->idanimal],
                    'quantityM'          => $ourSurplus->quantitiesm,
                    'quantityF'          => $ourSurplus->quantitiesf,
                    'quantityU'          => $ourSurplus->quantitiesu,
                    'region_id'          => $region_id,
                    'area_region_id'     => $area_region_id,
                    'origin'             => (collect($origin)->contains($ourSurplus->bred)) ? Origin::getIndex($ourSurplus->bred) : null,
                    'age_group'          => (collect($ageGroup)->contains($ourSurplus->age)) ? AgeGroup::getIndex($ourSurplus->age) : null,
                    'bornYear'           => $ourSurplus->bornYear,
                    'size'               => (collect($size)->contains($ourSurplus->size)) ? Size::getIndex($ourSurplus->size) : null,
                    'remarks'            => (trim($ourSurplus->remarks)            != '') ? $ourSurplus->remarks : null,
                    'intern_remarks'     => (trim($ourSurplus->internremarks)      != '') ? $ourSurplus->internremarks : null,
                    'special_conditions' => (trim($ourSurplus->special_conditions) != '') ? $ourSurplus->special_conditions : null,
                    'sale_currency'      => (trim($ourSurplus->currency)           != '' && $ourSurplus->currency           != 'null') ? $ourSurplus->currency : 'EUR',
                    'salePriceM'         => $ourSurplus->pricerealm,
                    'salePriceF'         => $ourSurplus->pricerealf,
                    'salePriceU'         => $ourSurplus->pricerealu,
                    'salePriceP'         => $ourSurplus->pricerealp,
                    'is_public'          => ($ourSurplus->public                != null) ? $ourSurplus->public : 0,
                    'created_at'         => ($ourSurplus->datecreated_stdprice  != null && DateTimeHelper::validateDate($ourSurplus->datecreated_stdprice)) ? date('Y-m-d H:i:s', strtotime($ourSurplus->datecreated_stdprice)) : null,
                    'updated_at'         => ($ourSurplus->datemodified_stdprice != null && DateTimeHelper::validateDate($ourSurplus->datemodified_stdprice)) ? date('Y-m-d H:i:s', strtotime($ourSurplus->datemodified_stdprice)) : null,
                ];

                $standardSurplus = OurSurplus::create($mappedArray);
                if (count($regions_array) > 0) {
                    $standardSurplus->area_regions()->sync($regions_array);
                }

                $count_imported_records++;
            }
        }

        //OurSurplus::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
