<?php

namespace App\Console\Commands;

use App\Models\Wanted;
use Illuminate\Console\Command;

class SetWantedInstitution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:set-wanted-institution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set wanted institution based on contact.';

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
        $wanteds = Wanted::whereHas('client', function ($query) {
            $query->whereNotNull('organisation_id');
        })->get();

        foreach ($wanteds as $wanted) {
            $wanted->update(['organisation_id' => $wanted->client->organisation_id], ['timestamps' => false]);
        }
    }
}
