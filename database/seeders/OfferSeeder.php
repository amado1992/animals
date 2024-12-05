<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\OfferAdditionalCost;
use App\Models\OfferSpecies;
use App\Models\OfferSpeciesCrate;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
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
            'offer_status'        => 'Inquiry',
            'remarks'             => 'Test',
            'sale_price_type'     => 'ExZoo',
            'cost_price_status'   => 'Estimation',
        ]);

        $offerSpecies = OfferSpecies::create([
            'offer_id'        => $offer->id,
            'oursurplus_id'   => 1,
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

        OfferSpeciesCrate::create([
            'offer_species_id' => $offerSpecies->id,
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
