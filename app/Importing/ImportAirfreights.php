<?php

namespace App\Importing;

use App\Models\Airfreight;
use App\Models\Airport;
use App\Models\Contact;
use App\Models\Country;
use DateTimeHelper;
use DB;

class ImportAirfreights
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
        $freights = DB::connection('mysql_old')->select('select * from airfreight where deletedairfreight = 0 order by idAirfreight');

        $contacts = Contact::pluck('id', 'old_id');

        $countries = Country::pluck('old_id', 'name');
        $airports  = Airport::pluck('old_id', 'city');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($freights as $freight) {
            if (!empty($freight->fromcountry) && $freight->fromcountry != '0' && !empty($freight->tocountry) && $freight->tocountry != '0') {
                array_push($mappedArray, [
                    'source'              => $freight->af_type,
                    'type'                => $freight->af_options,
                    'departure_continent' => ($countries->has([$freight->fromcountry])) ? Country::where('old_id', $countries[$freight->fromcountry])->first()->region->id : 170,
                    'arrival_continent'   => (!empty($freight->fromairport) && $airports->has([$freight->fromairport])) ? Airport::where('old_id', $airports[$freight->fromairport])->first()->region->id : null,
                    'currency'            => $freight->currency,
                    'volKg_weight_value'  => $freight->volweight,
                    'volKg_weight_cost'   => $freight->costvolairfreighttotal,
                    'lowerdeck_value'     => $freight->lowerdeck_value,
                    'lowerdeck_cost'      => $freight->costLowerdeck,
                    'maindeck_value'      => $freight->maindeck_value,
                    'maindeck_cost'       => $freight->costMaindeck,
                    'offered_date'        => $freight->offeredDate,
                    'transport_agent'     => (isset($contacts[$freight->iduser])) ? $contacts[$freight->iduser] : null,
                    'remarks'             => $freight->remarkText,
                    'updated_at'          => ($freight->datemodified_airfreight != null && DateTimeHelper::validateDate($freight->datemodified_airfreight)) ? date('Y-m-d H:i:s', strtotime($freight->datemodified_airfreight)) : null,
                ]);

                $count_imported_records++;
            }
        }

        Airfreight::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
