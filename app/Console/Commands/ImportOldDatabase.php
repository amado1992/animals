<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportOldDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:import {--l|level=} {--i|index=} {--b|batch=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old database';

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
     * @return mixed
     */
    public function handle()
    {
        $importClasses = collect([
            ['name' => 'ImportCountries', 'level' => 1],
            ['name' => 'ImportAirports', 'level' => 1],
            ['name' => 'ImportClassificationClasses', 'level' => 1],
            ['name' => 'ImportClassificationOrders', 'level' => 1],
            ['name' => 'ImportClassificationFamilies', 'level' => 1],
            ['name' => 'ImportClassificationGenera', 'level' => 1],
            ['name' => 'ImportCrates', 'level' => 1],
            ['name' => 'ImportZooAssociations', 'level' => 1],
            ['name' => 'ImportGuidelines', 'level' => 1],
            ['name' => 'ImportBankAccounts', 'level' => 1],
            ['name' => 'ImportCodes', 'level' => 1],
            ['name' => 'ImportInterestingWebsites', 'level' => 1], // check codes / interesting websites
            ['name' => 'ImportStdTexts', 'level' => 1],
            ['name' => 'ImportOrganisations', 'level' => 2],
            ['name' => 'ImportContacts', 'level' => 3],
            ['name' => 'ImportAnimals', 'level' => 4],
            ['name' => 'ImportClassificationCodes', 'level' => 4],
            ['name' => 'ImportWanteds', 'level' => 5],
            ['name' => 'ImportOurWanteds', 'level' => 5],
            ['name' => 'ImportSurplus', 'level' => 6],
            ['name' => 'ImportOurSurplus', 'level' => 6],
            ['name' => 'ImportAirfreights', 'level' => 7],
            ['name' => 'ImportAnimalsPictures', 'level' => 8],
            ['name' => 'ImportOurSurplusPictures', 'level' => 9],
            ['name' => 'ImportSurplusPictures', 'level' => 9],
        ]);

        // Take only importers with a level equal or lower than the given start level
        if ($this->option('level')) {
            $importClasses = $importClasses->where('level', $this->option('level'));
        }
        // Take only importers from the given index
        elseif ($this->option('index')) {
            $importClasses = $importClasses->skip($this->option('index'));
        }

        // Take only importers from the given index
        $batch = $this->option('batch');

        $this->info($importClasses->count() . ' importers are found based on the input.');

        foreach ($importClasses as $index => $class) {
            $start_time = microtime(true);

            $className = 'App\Importing\\' . $class['name'];

            $this->info('Importing: ' . $class['name']);

            $import = new $className();
            if ($batch) {
                $import->batch = $batch;
            }
            $result = $import->handle();

            $end_time = microtime(true);
            $duration = $end_time - $start_time;

            $this->info('Imported:  ' . $class['name'] . ' (' . $result['count_imported_records'] . ' records, skipped ' . $result['count_skipped_records'] . ' in ' . round($duration, 3) . ' seconds)');
        }
    }
}
