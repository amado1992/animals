<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AnonymizeContactsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:anonymize-contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymize data for testing purposes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $faker = \Faker\Factory::create('nl_NL');

        $total_contacts = Contact::whereNotNull('email')->where('email', 'not like', 'a.%')->get();
        $this->info($total_contacts->count() . ' contacts to be anonimized');
        foreach ($total_contacts as $contact) {
            if ($contact->first_name) {
                $contact->first_name = $faker->firstName;
            }

            if ($contact->last_name) {
                $contact->last_name = $faker->lastName;
            }

            if ($contact->email) {
                $contact->email = 'a.' . Str::camel(Str::random(12)) . $faker->unique()->safeEmail;
            }

            if ($contact->domain_name) {
                $contact->domain_name = $faker->domainName;
            }

            if ($contact->mobile_phone) {
                $contact->mobile_phone = $faker->phoneNumber;
            }

            $contact->save();
        }
    }
}
