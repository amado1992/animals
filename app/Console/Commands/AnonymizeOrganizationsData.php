<?php

namespace App\Console\Commands;

use App\Models\InterestingWebsite;
use App\Models\Organisation;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AnonymizeOrganizationsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:anonymize-organizations';

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

        $total_organisations = Organisation::whereNotNull('email')->where('email', 'not like', 'a.%')->get();
        $this->info($total_organisations->count() . ' organisations to be anonimized');
        foreach ($total_organisations as $organisation) {
            if ($organisation->name) {
                $organisation->name = $faker->company;
            }

            if ($organisation->email) {
                $organisation->email = 'a.' . Str::camel(Str::random(12)) . $faker->unique()->safeEmail;
            }

            if ($organisation->domain_name) {
                $organisation->domain_name = $faker->domainName;
            }

            if ($organisation->website) {
                $organisation->website = $faker->domainName;
            }

            if ($organisation->facebook_page) {
                $organisation->facebook_page = $faker->domainName;
            }

            if ($organisation->phone) {
                $organisation->phone = $faker->phoneNumber;
            }

            if ($organisation->address) {
                $organisation->address = $faker->streetAddress;
            }

            $organisation->save();
        }

        InterestingWebsite::truncate();
        $this->info('Login codes removed');
    }
}
