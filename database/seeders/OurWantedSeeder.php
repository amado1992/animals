<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OurWantedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\OurWanted::class, 5)->create();
    }
}
