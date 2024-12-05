<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\OfferAdditionalCost;
use App\Models\OfferSpecies;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offer = Offer::create([
            'manager_id'          => 1,
            'creator'             => 'IZS',
            'offer_number'        => '1',
            'offer_currency'      => 'EUR',
            'client_id'           => 1,
            'delivery_country_id' => 170,
            'delivery_airport_id' => 194,
            'offer_status'        => 'Ordered',
            'remarks'             => 'Test',
            'sale_price_type'     => 'ExZoo',
            'cost_price_status'   => 'Estimation',
        ]);

        Order::create([
            'offer_id'            => $offer->id,
            'manager_id'          => 1,
            'order_number'        => '1',
            'client_id'           => 1,
            'supplier_id'         => 2,
            'delivery_country_id' => 170,
            'delivery_airport_id' => 194,
            'cost_currency'       => 'EUR',
            'sale_currency'       => 'EUR',
            'company'             => 'IZS_BV',
            'bank_account_id'     => 1,
            'order_status'        => 'Pending',
            'order_remarks'       => 'Test',
            'cost_price_type'     => 'ExZoo',
            'sale_price_type'     => 'ExZoo',
            'cost_price_status'   => 'Estimation',
        ]);

        OfferSpecies::create([
            'offer_id'        => $offer->id,
            'oursurplus_id'   => 2,
            'offerQuantityM'  => 1,
            'offerQuantityF'  => 1,
            'offerQuantityU'  => 1,
            'offerCostPriceM' => 0,
            'offerCostPriceF' => 0,
            'offerCostPriceU' => 0,
            'offerCostPriceP' => 0,
            'offerSalePriceM' => 1000,
            'offerSalePriceF' => 1000,
            'offerSalePriceU' => 1000,
            'offerSalePriceP' => 2000,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Handling-costs',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 300,
            'salePrice' => 0,
            'is_test'   => 0,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Transport to the airport',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 250,
            'salePrice' => 385,
            'is_test'   => 0,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Application documents',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 150,
            'salePrice' => 190,
            'is_test'   => 0,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Airport costs: custom costs, animal hotel, handling, etc',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 335,
            'salePrice' => 380,
            'is_test'   => 0,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Discount',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 0,
            'salePrice' => 0,
            'is_test'   => 0,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Bloodtests',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 0,
            'salePrice' => 0,
            'is_test'   => 1,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'Quarantaine',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 0,
            'salePrice' => 0,
            'is_test'   => 1,
        ]);

        OfferAdditionalCost::create([
            'offer_id'  => $offer->id,
            'name'      => 'DNA sex-determination',
            'quantity'  => 1,
            'currency'  => 'EUR',
            'costPrice' => 0,
            'salePrice' => 0,
            'is_test'   => 1,
        ]);
    }
}
