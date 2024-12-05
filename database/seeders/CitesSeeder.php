<?php

namespace Database\Seeders;

use App\Models\Cites;
use Illuminate\Database\Seeder;

class CitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cites::create(['key' => 'I', 'type' => 'global']);
        Cites::create(['key' => 'II', 'type' => 'global']);
        Cites::create(['key' => 'III', 'type' => 'global']);

        Cites::create(['key' => 'A', 'type' => 'europe']);
        Cites::create(['key' => 'B', 'type' => 'europe']);
        Cites::create(['key' => 'C', 'type' => 'europe']);
    }
}
