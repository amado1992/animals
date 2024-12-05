<?php

namespace Database\Seeders;

use App\Models\AdditionalCost;
use Illuminate\Database\Seeder;

class AdditionalCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdditionalCost::create([
            'name'         => 'Handling-costs',
            'usdCostPrice' => 330,
            'usdSalePrice' => 0,
            'eurCostPrice' => 300,
            'eurSalePrice' => 0,
            'is_test'      => 0,
        ]);

        AdditionalCost::create([
            'name'         => 'Transport to the airport',
            'usdCostPrice' => 350,
            'usdSalePrice' => 445,
            'eurCostPrice' => 250,
            'eurSalePrice' => 385,
            'is_test'      => 0,
        ]);

        AdditionalCost::create([
            'name'         => 'Application documents',
            'usdCostPrice' => 190,
            'usdSalePrice' => 220,
            'eurCostPrice' => 150,
            'eurSalePrice' => 190,
            'is_test'      => 0,
        ]);

        AdditionalCost::create([
            'name'         => 'Airport costs: custom costs, animal hotel, handling, etc',
            'usdCostPrice' => 335,
            'usdSalePrice' => 435,
            'eurCostPrice' => 280,
            'eurSalePrice' => 380,
            'is_test'      => 0,
        ]);

        AdditionalCost::create([
            'name'         => 'Discount',
            'usdCostPrice' => 0,
            'usdSalePrice' => 0,
            'eurCostPrice' => 0,
            'eurSalePrice' => 0,
            'is_test'      => 0,
        ]);

        AdditionalCost::create([
            'name'         => 'Bloodtests',
            'usdCostPrice' => 0,
            'usdSalePrice' => 0,
            'eurCostPrice' => 0,
            'eurSalePrice' => 0,
            'is_test'      => 1,
        ]);

        AdditionalCost::create([
            'name'         => 'Quarantaine',
            'usdCostPrice' => 0,
            'usdSalePrice' => 0,
            'eurCostPrice' => 0,
            'eurSalePrice' => 0,
            'is_test'      => 1,
        ]);

        AdditionalCost::create([
            'name'         => 'DNA sex-determination',
            'usdCostPrice' => 0,
            'usdSalePrice' => 0,
            'eurCostPrice' => 0,
            'eurSalePrice' => 0,
            'is_test'      => 1,
        ]);
    }
}
