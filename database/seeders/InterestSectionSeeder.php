<?php

namespace Database\Seeders;

use App\Models\InterestSection;
use Illuminate\Database\Seeder;

class InterestSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Z'   => 'Zoo animals',
            'B'   => 'Birds',
            'AA'  => 'Aquatic species',
            'PR'  => 'Primates',
            'SM'  => 'Small mammals',
            'RAF' => 'Reptiles',
            'C'   => 'Carnivores',
        ];

        foreach ($types as $key => $value) {
            InterestSection::create([
                'key'   => $key,
                'label' => $value,
            ]);
        }
    }
}
