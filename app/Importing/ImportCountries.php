<?php

namespace App\Importing;

use App\Models\Country;
use App\Models\Region;
use Carbon\Carbon;
use DB;

class ImportCountries
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
        $regions = Region::pluck('id', 'name');

        $countries = DB::connection('mysql_old')->select('select * from country order by name');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();
        $now                    = Carbon::now();

        foreach ($countries as $country) {
            // Region cannot be null
            if (!isset($regions[$country->continent])) {
                $count_skipped_records++;
                $skipped_records->push('country; ' . $country->name . '; no region defined');
                continue;
            }

            // Skip countries without a proper code
            if (!isset($country->code) || $country->code === '' || !$country->code) {
                $count_skipped_records++;
                $skipped_records->push('country; ' . $country->name . '; no country code defined');
                continue;
            }

            array_push($mappedArray, [
                'old_id'       => $country->idcountry,
                'name'         => $country->name,
                'country_code' => strtoupper($country->code),
                'phone_code'   => $country->phonecode,
                'region_id'    => $regions[$country->continent],
                'language'     => $country->language,
                'created_at'   => $now,
            ]);

            $count_imported_records++;
        }

        Country::insert($mappedArray);

        if (count($skipped_records) > 0) {
            \Storage::disk('local')->put('import_skip_countries.txt', $skipped_records->toArray());
        }

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
