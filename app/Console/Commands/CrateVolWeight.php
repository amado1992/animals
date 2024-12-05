<?php

namespace App\Console\Commands;

use App\Models\Crate;
use Illuminate\Console\Command;

class CrateVolWeight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:crate-volweight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate vol. weight for all crates based on crate dimensions.';

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
        Crate::query()->update(['weight' => 0]);

        $crates = Crate::where('length', '<>', 0)
            ->where('wide', '<>', 0)
            ->where('height', '<>', 0)
            ->get();

        foreach ($crates as $crate) {
            $volWeight = ($crate->length * $crate->wide * $crate->height) / 6000;
            $crate->update(['weight' => round($volWeight, 2)]);
        }
    }
}
