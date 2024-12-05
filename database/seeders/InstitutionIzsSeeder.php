<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Organisation;
use Illuminate\Database\Seeder;

class InstitutionIzsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $organisation                    = new Organisation();
        $organisation->name              = 'International Zoo Services';
        $organisation->domain_name       = 'zoo-services.com';
        $organisation->organisation_type = 'Z';
        $organisation->email             = 'izs@zoo-services.com';
        $organisation->phone             = '+31854011610';
        $organisation->level             = 'A';
        $organisation->save();

        $contact                  = new Contact();
        $contact->first_name      = 'International Zoo Services';
        $contact->domain_name     = 'zoo-services.com';
        $contact->email           = 'fake@zoo-services.com';
        $contact->mobile_phone    = '+31854011610';
        $contact->organisation_id = $organisation->id;
        $contact->save();
    }
}
