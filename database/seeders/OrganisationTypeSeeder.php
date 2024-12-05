<?php

namespace Database\Seeders;

use App\Models\OrganisationType;
use Illuminate\Database\Seeder;

class OrganisationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'ACON'  => 'Animal consult & Vet.',
            'AEQ'   => 'Animal equipment',
            'AFOO'  => 'Animal Food',
            'AMG'   => 'Animal Magazine',
            'AS'    => 'Animal supplier',
            'AR'    => 'Appartment Rent',
            'AU'    => 'Authorities',
            'C'     => 'Circus and animal-training',
            'CO'    => 'Company',
            'EDMAT' => 'Educational Material',
            'HT'    => 'Hotel-private',
            'HORES' => 'Hotel-resorts',
            'NPR'   => 'National Park and reserves',
            'PS'    => 'Press',
            'PR'    => 'Private',
            'PBF'   => 'Private breeding farm',
            'RL'    => 'Research and laboratories',
            'SOU'   => 'Souvenirs',
            'TR'    => 'Transport',
            'Z'     => 'Zoo and public facilities',
            'ARCH'  => 'Zoo architecture and design',
            //
            'CONS' => 'Construction',
            'AE'   => 'Aquarium Equipment',
            'ZCON' => 'Zoo - Consultancy',
            'EVM'  => 'Event management',
            'VST'  => 'Visitor Services &amp; Ticketing',
            'uk'   => 'Unknown',
        ];

        foreach ($types as $key => $value) {
            OrganisationType::create([
                'key'   => $key,
                'label' => $value,
            ]);
        }
    }
}
