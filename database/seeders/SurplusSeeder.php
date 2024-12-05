<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SurplusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Surplus::class, 5)->create();
    }
}
