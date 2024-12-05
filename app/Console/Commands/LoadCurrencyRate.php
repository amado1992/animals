<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class LoadCurrencyRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load currency exchanges rates from http://exchangeratesapi.io/';

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
        $currencyService = new CurrencyService();
        $result          = $currencyService->loadLatestRates();
        $currencyService->saveRatesToDatabase($result['date'], $result['rates']);

        $this->info('rates are loaded and saved');
    }
}
