<?php

namespace Database\Seeders;

use App\Models\Crate;
use Illuminate\Database\Seeder;

class CrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Crate::create([
            'name'      => 'Deer antelope medium',
            'iata_code' => 73,
            'length'    => 125,
            'height'    => 135,
            'width'     => 45,
            'weight'    => 150,
            'price_eur' => 125,
        ]);

        Crate::create([
            'name'      => 'Spider monkey',
            'iata_code' => 31,
            'length'    => 50,
            'height'    => 80,
            'width'     => 40,
            'weight'    => 15,
            'price_eur' => 160,
        ]);

        Crate::create([
            'name'      => 'Goose small',
            'iata_code' => 17,
            'length'    => 45,
            'height'    => 40,
            'width'     => 30,
            'weight'    => 5,
            'price_eur' => 35,
        ]);
    }
}
