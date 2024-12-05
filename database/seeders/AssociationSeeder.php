<?php

namespace Database\Seeders;

use App\Models\Association;
use Illuminate\Database\Seeder;

class AssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'AZA'    => 'AZA',
            'EAZA'   => 'EAZA',
            'PAZAP'  => 'PAZAP',
            'APLZA'  => 'APLZA',
            'Others' => 'Others',
        ];

        foreach ($types as $key => $value) {
            Association::create([
                'key'   => $key,
                'label' => $value,
            ]);
        }
    }
}
