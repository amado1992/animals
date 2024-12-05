<?php

namespace App\Importing;

use App\Models\Airport;
use App\Models\Country;
use Carbon\Carbon;
use DB;

class ImportAirports
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
        $countries = Country::pluck('id', 'country_code');

        $cities = DB::connection('mysql_old')->select('
            SELECT city.*, country.code as country_code
            FROM city
            JOIN country ON (city.countryidcountry = country.idcountry)
            WHERE country.code <> ""
        ');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $now                    = Carbon::now();

        foreach ($cities as $city) {
            // Country has to be found
            if (!isset($countries[strtoupper($city->country_code)])) {
                exit;
            }

            array_push($mappedArray, [
                'old_id'     => $city->idcity,
                'name'       => $city->namecity,
                'city'       => $city->namecity,
                'country_id' => $countries[strtoupper($city->country_code)],
                'created_at' => $now,
            ]);

            $count_imported_records++;
        }

        Airport::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
