<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Classification::create([
            'common_name'     => 'Scorpions',
            'scientific_name' => 'Scorpionidae',
            'rank'            => 'family',
        ]);

        $callitrichidae = Classification::create([
            'common_name'     => 'Marmosets and tamarins',
            'scientific_name' => 'Callitrichidae',
            'rank'            => 'family',
        ]);

        $mammals = Classification::create([
            'common_name'     => 'Mammals',
            'scientific_name' => 'Mammalia',
            'rank'            => 'class',
            'belongs_to'      => $callitrichidae->id,
        ]);

        $primates = Classification::create([
            'common_name'     => 'Primates',
            'scientific_name' => 'Primates',
            'rank'            => 'order',
            'belongs_to'      => $mammals->id,
        ]);

        $saguinus = Classification::create([
            'common_name'     => 'Saguinus',
            'scientific_name' => 'Saguinus',
            'rank'            => 'genus',
            'belongs_to'      => $primates->id,
        ]);

        Classification::create([
            'common_name'     => 'Caracaras, falcons, and alies',
            'scientific_name' => 'Falconidae',
            'rank'            => 'family',
        ]);

        Classification::create([
            'common_name'     => 'Megapodes',
            'scientific_name' => 'Megapodidae',
            'rank'            => 'family',
        ]);

        $cracidea = Classification::create([
            'common_name'     => 'Chachalacas, guans and curassows',
            'scientific_name' => 'Cracidae',
            'rank'            => 'family',
        ]);

        $birds = Classification::create([
            'common_name'     => 'Birds',
            'scientific_name' => 'Aves',
            'rank'            => 'class',
            'belongs_to'      => $cracidea->id,
        ]);

        $galliformes = Classification::create([
            'common_name'     => 'GALLINACEOUS BIRDS',
            'scientific_name' => 'GALLIFORMES',
            'rank'            => 'order',
            'belongs_to'      => $birds->id,
        ]);

        $aburria = Classification::create([
            'common_name'     => 'Aburria',
            'scientific_name' => 'Aburria',
            'rank'            => 'genus',
            'belongs_to'      => $galliformes->id,
        ]);

        $crax = Classification::create([
            'common_name'     => 'Crax',
            'scientific_name' => 'Crax',
            'rank'            => 'genus',
            'belongs_to'      => $galliformes->id,
        ]);
    }
}
