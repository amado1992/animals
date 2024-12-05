<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OurSurplusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\OurSurplus::class, 5)->create();
    }
}
