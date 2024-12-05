<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Country::create([
        // 	'name' => 'Cuba',
        // 	'country_code' => 'cu',
        // 	'phone_code' => '53',
        // 	'region_id' => Region::where('name', 'Caribbean')->firstOrFail()->id,
        // ]);

        // Country::create([
        //     'name' => 'The Netherlands',
        //     'country_code' => 'nl',
        //     'phone_code' => '31',
        //     'region_id' => Region::where('name', 'Europe (Eur. union + Swit)')->firstOrFail()->id,
        // ]);

        // // Country::create([
        // //     'name' => 'U.S.A.',
        // //     'country_code' => 'us',
        // //     'phone_code' => '1',
        // //     'region_id' => Region::where('name', 'North-America (U.S.A)')->firstOrFail()->id,
        // // ]);

        // Country::create([
        //     'name' => 'France',
        //     'country_code' => 'fr',
        //     'phone_code' => '1',
        //     'region_id' => Region::where('name', 'Europe (Eur. union + Swit)')->firstOrFail()->id,
        // ]);
    }
}
