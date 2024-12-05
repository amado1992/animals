<?php

namespace App\Console\Commands;

use App\Models\Crate;
use Illuminate\Console\Command;

class CrateNamesUppercase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:crate-uppercase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all crate names uppercase';

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
        $crates = Crate::get();

        foreach ($crates as $crate) {
            $crate->update(['name' => ucfirst($crate->name)]);
        }
    }
}
