<?php

namespace App\Console\Commands;

use App\Models\Organisation;
use Illuminate\Console\Command;

class ContactCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:contact-country';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set contact country based on related institution.';

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
        $institutions = Organisation::whereNotNull('country_id')->get();

        foreach ($institutions as $institution) {
            foreach ($institution->contacts as $contact) {
                $contact->update(['country_id' => $institution->country_id]);
            }
        }

        $this->info('Task done successfully.');
    }
}
