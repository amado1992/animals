<?php

namespace Database\Seeders;

use App\Models\AreaRegion;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AreaRegion::create(['name' => 'Africa', 'short_cut' => 'A (Afr)']);
        AreaRegion::create(['name' => 'Asia+Middle-East', 'short_cut' => 'B (As+Mid-east)']);
        AreaRegion::create(['name' => 'USA+Canada+Caribbean', 'short_cut' => 'C (US+Can+Car)']);
        AreaRegion::create(['name' => 'Central-Am+South-Am', 'short_cut' => 'D (Centr.+Sou-Am)']);
        AreaRegion::create(['name' => 'Europe(EU)+Europe(non EU)', 'short_cut' => 'E (Europ, all)']);
        AreaRegion::create(['name' => 'Australia', 'short_cut' => 'F (Aus)']);
    }
}
