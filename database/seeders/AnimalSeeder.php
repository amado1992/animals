<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Classification;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $crax = Classification::where('common_name', 'Crax')->firstOrFail();

        Animal::create([
            'code_number'      => '1314002003002000',
            'common_name'      => 'Blue knobbed curassow',
            'scientific_name'  => 'Crax alberti',
            'cites_global_key' => 'II',
            'cites_europe_key' => 'A',
            'genus_id'         => $crax->id,
            'iata_code'        => 16,
            'body_weight'      => 10.0,
        ]);

        $saguinus = Classification::where('common_name', 'Saguinus')->firstOrFail();

        Animal::create([
            'code_number'      => '1406007004008000',
            'common_name'      => 'Emperor tamarin',
            'scientific_name'  => 'Saguinus imperator',
            'cites_global_key' => 'I',
            'cites_europe_key' => 'B',
            'genus_id'         => $saguinus->id,
            'iata_code'        => 31,
            'body_weight'      => 10.0,
        ]);
    }
}
