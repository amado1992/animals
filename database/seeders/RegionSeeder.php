<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::create(['name' => 'Asia', 'short_cut' => 'As', 'area_region_id' => 2]);
        Region::create(['name' => 'Africa', 'short_cut' => 'Afr', 'area_region_id' => 1]);
        Region::create(['name' => 'Middle-East', 'short_cut' => 'M-East', 'area_region_id' => 2]);
        Region::create(['name' => 'Europe (Non Eur. union)', 'short_cut' => 'Eur (neu)', 'area_region_id' => 5]);
        Region::create(['name' => 'Europe (Eur. union + Swit + UK)', 'short_cut' => 'Eur (eu)', 'area_region_id' => 5]);
        Region::create(['name' => 'Australia', 'short_cut' => 'Aus', 'area_region_id' => 6]);
        Region::create(['name' => 'Central-America', 'short_cut' => 'C-Am', 'area_region_id' => 4]);
        Region::create(['name' => 'South-America', 'short_cut' => 'S-Am', 'area_region_id' => 4]);
        Region::create(['name' => 'North-America (Can)', 'short_cut' => 'Can', 'area_region_id' => 3]);
        Region::create(['name' => 'North-America (U.S.A)', 'short_cut' => 'Usa', 'area_region_id' => 3]);
        Region::create(['name' => 'Caribbean', 'short_cut' => 'Car', 'area_region_id' => 3]);
    }
}
