<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WantedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Wanted::class, 5)->create();
    }
}
