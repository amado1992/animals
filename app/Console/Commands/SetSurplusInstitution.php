<?php

namespace App\Console\Commands;

use App\Models\Surplus;
use Illuminate\Console\Command;

class SetSurplusInstitution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:set-surplus-institution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set surplus institution based on contact.';

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
        $surpluses = Surplus::whereHas('contact', function ($query) {
            $query->whereNotNull('organisation_id');
        })->get();

        foreach ($surpluses as $surplus) {
            $surplus->update(['organisation_id' => $surplus->contact->organisation_id], ['timestamps' => false]);
        }
    }
}
