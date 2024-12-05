<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(OrganisationTypeSeeder::class);
        $this->call(InterestSectionSeeder::class);
        $this->call(AssociationSeeder::class);
        $this->call(CitesSeeder::class);
        $this->call(AdditionalCostSeeder::class);
        $this->call(GenericDomainSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(ActionSeeder::class);

        // $this->call(ContactSeeder::class);
        // $this->call(ClassificationSeeder::class);
        // $this->call(CrateSeeder::class);
        // $this->call(TaskSeeder::class);
        // $this->call(AnimalSeeder::class);
        // $this->call(OurSurplusSeeder::class);
        // $this->call(OurWantedSeeder::class);
        // $this->call(SurplusSeeder::class);
        // $this->call(WantedSeeder::class);
        // $this->call(OfferSeeder::class);
        // $this->call(OrderSeeder::class);
    }
}
