<?php

namespace App\Importing;

use App\Enums\AgeGroup;
use App\Enums\LookingFor;
use App\Enums\Origin;
use App\Models\Animal;
use App\Models\AreaRegion;
use App\Models\OurWanted;
use DateTimeHelper;
use DB;

class ImportOurWanteds
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
        $ourWanteds = DB::connection('mysql_old')->select('select * from stdwanted where deletedstdwanted = 0 order by idstdwanted');

        $animals     = Animal::pluck('id', 'old_id');
        $areaRegions = AreaRegion::pluck('id', 'name');

        $origin     = Origin::get();
        $ageGroup   = AgeGroup::get();
        $lookingFor = LookingFor::get();

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($ourWanteds as $ourWanted) {
            if ($ourWanted->idanimal != null && $ourWanted->idanimal != 0 && isset($animals[$ourWanted->idanimal])) {
                $regions_array = [];
                if ($ourWanted->continents != null && trim($ourWanted->continents) != '') {
                    $regions_name = explode(',', $ourWanted->continents);
                    foreach ($regions_name as $region_name) {
                        if (isset($areaRegions[$region_name])) {
                            array_push($regions_array, $areaRegions[$region_name]);
                        }
                    }
                }

                /*array_push( $mappedArray, [
                    'animal_id' => (isset($animals[$ourWanted->idanimal])) ? $animals[$ourWanted->idanimal] : null,
                    'origin' => (collect($origin)->contains($ourWanted->bred)) ? Origin::getIndex($ourWanted->bred) : null,
                    'age_group' => (collect($ageGroup)->contains($ourWanted->age)) ? AgeGroup::getIndex($ourWanted->age) : null,
                    'looking_for' => (collect($lookingFor)->contains($ourWanted->sex)) ? LookingFor::getIndex($ourWanted->sex) : null,
                    'remarks' => $ourWanted->remarks,
                    'intern_remarks' => $ourWanted->hiddenremarks
                ]);*/

                $mappedArray = [
                    'animal_id'      => $animals[$ourWanted->idanimal],
                    'origin'         => (collect($origin)->contains($ourWanted->bred)) ? Origin::getIndex($ourWanted->bred) : null,
                    'age_group'      => (collect($ageGroup)->contains($ourWanted->age)) ? AgeGroup::getIndex($ourWanted->age) : null,
                    'looking_for'    => (collect($lookingFor)->contains($ourWanted->sex)) ? LookingFor::getIndex($ourWanted->sex) : null,
                    'remarks'        => (trim($ourWanted->remarks)          != '') ? $ourWanted->remarks : null,
                    'intern_remarks' => (trim($ourWanted->hiddenremarks)    != '') ? $ourWanted->hiddenremarks : null,
                    'created_at'     => ($ourWanted->datecreated_stdwanted  != null && DateTimeHelper::validateDate($ourWanted->datecreated_stdwanted)) ? date('Y-m-d H:i:s', strtotime($ourWanted->datecreated_stdwanted)) : null,
                    'updated_at'     => ($ourWanted->datemodified_stdwanted != null && DateTimeHelper::validateDate($ourWanted->datemodified_stdwanted)) ? date('Y-m-d H:i:s', strtotime($ourWanted->datemodified_stdwanted)) : null,
                ];

                $standardWanted = OurWanted::create($mappedArray);
                if (count($regions_array) > 0) {
                    $standardWanted->area_regions()->sync($regions_array);
                }

                $count_imported_records++;
            }
        }

        //OurWanted::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
