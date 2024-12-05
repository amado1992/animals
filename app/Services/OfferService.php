<?php

namespace App\Services;

use App\Models\CurrencyRate;
use App\Models\Offer;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OfferService
{
    /**
     * Here we calculate the offer totals.
     * In this way we can use this in different controllers.
     *
     * @param int $id
     *
     * @return Offer  $offer
     */
    public static function calculate_offer_totals($id)
    {
        $offer = Offer::select('*', 'offers.id as offerId', 'offers.created_at as created_date')->find($id);

        $general_rate_usd = ($offer->offer_currency != 'USD') ? number_format(
            CurrencyRate::latest()->value($offer->offer_currency . '_USD'),
            2, '.', ''
        ) : 1;

        $offerTotalSpeciesCostPrice        = 0;
        $offerTotalSpeciesSalePrice        = 0;
        $offerTotalSpeciesCostPriceUSD     = 0;
        $offerTotalSpeciesSalePriceUSD     = 0;
        $offerTotalCratesCostPrice         = 0;
        $offerTotalCratesSalePrice         = 0;
        $offerTotalCratesCostPriceUSD      = 0;
        $offerTotalCratesSalePriceUSD      = 0;
        $offerTotalVolKg                   = 0;
        $offerTotalActualWeight            = 0;
        $offerTotalAirfreightsCostPrice    = 0;
        $offerTotalAirfreightsSalePrice    = 0;
        $offerTotalAirfreightsCostPriceUSD = 0;
        $offerTotalAirfreightsSalePriceUSD = 0;
        $offerTotalCostPrice               = 0;
        $offerTotalSalePrice               = 0;
        $offerTotalCostPriceUSD            = 0;
        $offerTotalSalePriceUSD            = 0;

        foreach ($offer->species_ordered as $os) {
            $usdCurrencyValue  = ($os->oursurplus->sale_currency !== 'USD' && $os->oursurplus->sale_currency !== null) ? number_format(
                CurrencyRate::latest()->value($os->oursurplus->sale_currency . '_USD'),
                2, '.', ''
            ) : 1;
            $os->currency_rate = $usdCurrencyValue;

            $currency_rate = ($os->oursurplus->sale_currency !== 'EUR' && $os->oursurplus->sale_currency !== null) ? number_format(
                CurrencyRate::latest()->value($os->oursurplus->sale_currency . '_EUR'),
                2, '.', ''
            ) : 1;

            if ($offer->sale_price_type == 'ExZoo') {
                if ($os->oursurplus->sale_currency != 'USD') {
                    $os->offerSalePriceMPdfUsd = ($os->offerQuantityM > 0 && $os->offerSalePriceM > 0) ? number_format(
                        $os->offerSalePriceM * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePriceFPdfUsd = ($os->offerQuantityF > 0 && $os->offerSalePriceF > 0) ? number_format(
                        $os->offerSalePriceF * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePriceUPdfUsd = ($os->offerQuantityU > 0 && $os->offerSalePriceU > 0) ? number_format(
                        $os->offerSalePriceU * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePricePPdfUsd = ($os->offerQuantityP > 0 && $os->offerSalePriceP > 0) ? number_format(
                        $os->offerSalePriceP * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                }

                $os->offerSalePriceMPdf = ($os->offerQuantityM > 0 && $os->offerSalePriceM > 0) ? number_format(
                    $os->offerSalePriceM,
                    2, '.', ''
                ) : "";
                $os->offerSalePriceFPdf = ($os->offerQuantityF > 0 && $os->offerSalePriceF > 0) ? number_format(
                    $os->offerSalePriceF,
                    2, '.', ''
                ) : "";
                $os->offerSalePriceUPdf = ($os->offerQuantityU > 0 && $os->offerSalePriceU > 0) ? number_format(
                    $os->offerSalePriceU,
                    2, '.', ''
                ) : "";
                $os->offerSalePricePPdf = ($os->offerQuantityP > 0 && $os->offerSalePriceP > 0) ? number_format(
                    $os->offerSalePriceP,
                    2, '.', ''
                ) : "";
            }

            $os->total_cost_price = $os->offerQuantityM * $os->offerCostPriceM * $currency_rate;
            $os->total_cost_price += $os->offerQuantityF * $os->offerCostPriceF * $currency_rate;
            $os->total_cost_price += $os->offerQuantityU * $os->offerCostPriceU * $currency_rate;
            $os->total_cost_price += $os->offerQuantityP * $os->offerCostPriceP * $currency_rate;

            $os->total_cost_price_usd = $os->offerQuantityM * $os->offerCostPriceM * $os->currency_rate;
            $os->total_cost_price_usd += $os->offerQuantityF * $os->offerCostPriceF * $os->currency_rate;
            $os->total_cost_price_usd += $os->offerQuantityU * $os->offerCostPriceU * $os->currency_rate;
            $os->total_cost_price_usd += $os->offerQuantityP * $os->offerCostPriceP * $os->currency_rate;

            $os->total_sale_price = $os->offerQuantityM * $os->offerSalePriceM * $currency_rate;
            $os->total_sale_price += $os->offerQuantityF * $os->offerSalePriceF * $currency_rate;
            $os->total_sale_price += $os->offerQuantityU * $os->offerSalePriceU * $currency_rate;
            $os->total_sale_price += $os->offerQuantityP * $os->offerSalePriceP * $currency_rate;

            $os->total_sale_price_usd = $os->offerQuantityM * $os->offerSalePriceM * $os->currency_rate;
            $os->total_sale_price_usd += $os->offerQuantityF * $os->offerSalePriceF * $os->currency_rate;
            $os->total_sale_price_usd += $os->offerQuantityU * $os->offerSalePriceU * $os->currency_rate;
            $os->total_sale_price_usd += $os->offerQuantityP * $os->offerSalePriceP * $os->currency_rate;

            /*if($os->offerQuantityM > 0 && $os->offerQuantityM == $os->offerQuantityF && $os->offerCostPriceP > 0) {
                $os->total_cost_price += $os->offerQuantityM * $os->offerCostPriceP * $currency_rate;
                $os->total_cost_price_usd += $os->offerQuantityM * $os->offerCostPriceP * $os->currency_rate;
            }

            if($os->offerQuantityM > 0 && $os->offerQuantityM == $os->offerQuantityF && $os->offerSalePriceP > 0) {
                $os->total_sale_price += $os->offerQuantityM * $os->offerSalePriceP * $currency_rate;
                $os->total_sale_price_usd += $os->offerQuantityM * $os->offerSalePriceP * $os->currency_rate;
            }*/

            $offerTotalSpeciesCostPrice    += $os->total_cost_price;
            $offerTotalSpeciesCostPriceUSD += $os->total_cost_price_usd;
            $offerTotalSpeciesSalePrice    += $os->total_sale_price;
            $offerTotalSpeciesSalePriceUSD += $os->total_sale_price_usd;

            $crate_volKg = 0;
            if ($os->species_crate != null && $offer->sale_price_type != 'ExZoo') {
                $crate_volKg = number_format(
                    ($os->species_crate->length * $os->species_crate->wide * $os->species_crate->height) / 6000,
                    2, '.', ''
                );

                $os->species_crate->m_volKg = number_format(
                    $os->species_crate->quantity_males * $crate_volKg, 2, '.',
                    ''
                );
                $os->species_crate->f_volKg = number_format(
                    $os->species_crate->quantity_females * $crate_volKg, 2, '.',
                    ''
                );
                $os->species_crate->u_volKg = number_format(
                    $os->species_crate->quantity_unsexed * $crate_volKg, 2, '.',
                    ''
                );
                $os->species_crate->p_volKg = number_format(
                    $os->species_crate->quantity_pairs * $crate_volKg, 2, '.',
                    ''
                );

                $os->total_volKg        = $os->species_crate->m_volKg + $os->species_crate->f_volKg + $os->species_crate->u_volKg + $os->species_crate->p_volKg;
                $offerTotalVolKg        += $os->total_volKg;
                $offerTotalActualWeight += $os->oursurplus->animal->body_weight;

                $os->species_crate->total_cost_price = $os->species_crate->quantity_males * $os->species_crate->cost_price * $currency_rate;
                $os->species_crate->total_cost_price += $os->species_crate->quantity_females * $os->species_crate->cost_price * $currency_rate;
                $os->species_crate->total_cost_price += $os->species_crate->quantity_unsexed * $os->species_crate->cost_price * $currency_rate;
                $os->species_crate->total_cost_price += $os->species_crate->quantity_pairs * $os->species_crate->cost_price * $currency_rate;

                $offerTotalCratesCostPrice += $os->species_crate->total_cost_price;

                $os->species_crate->total_cost_price_usd = $os->species_crate->quantity_males * $os->species_crate->cost_price * $os->currency_rate;
                $os->species_crate->total_cost_price_usd += $os->species_crate->quantity_females * $os->species_crate->cost_price * $os->currency_rate;
                $os->species_crate->total_cost_price_usd += $os->species_crate->quantity_unsexed * $os->species_crate->cost_price * $os->currency_rate;
                $os->species_crate->total_cost_price_usd += $os->species_crate->quantity_pairs * $os->species_crate->cost_price * $os->currency_rate;

                $offerTotalCratesCostPriceUSD += $os->species_crate->total_cost_price_usd;

                $os->species_crate->total_sale_price = $os->species_crate->quantity_males * $os->species_crate->sale_price * $currency_rate;
                $os->species_crate->total_sale_price += $os->species_crate->quantity_females * $os->species_crate->sale_price * $currency_rate;
                $os->species_crate->total_sale_price += $os->species_crate->quantity_unsexed * $os->species_crate->sale_price * $currency_rate;
                $os->species_crate->total_sale_price += $os->species_crate->quantity_pairs * $os->species_crate->sale_price * $currency_rate;

                $offerTotalCratesSalePrice += $os->species_crate->total_sale_price;

                $os->species_crate->total_sale_price_usd = $os->species_crate->quantity_males * $os->species_crate->sale_price * $os->currency_rate;
                $os->species_crate->total_sale_price_usd += $os->species_crate->quantity_females * $os->species_crate->sale_price * $os->currency_rate;
                $os->species_crate->total_sale_price_usd += $os->species_crate->quantity_unsexed * $os->species_crate->sale_price * $os->currency_rate;
                $os->species_crate->total_sale_price_usd += $os->species_crate->quantity_pairs * $os->species_crate->sale_price * $os->currency_rate;

                $offerTotalCratesSalePriceUSD += $os->species_crate->total_sale_price_usd;
            }

            if ($offer->airfreight_type == 'volKgRates' && $offer->sale_price_type != 'ExZoo') {
                foreach ($os->species_airfreights as $species_airfreight) {
                    $os->total_cost_volKg_value += $species_airfreight->cost_volKg;
                    $os->total_sale_volKg_value += $species_airfreight->sale_volKg;
                }

                $os->total_airfreight_cost_price     = $os->total_volKg * $os->total_cost_volKg_value * $currency_rate;
                $os->total_airfreight_cost_price_usd = $os->total_volKg * $os->total_cost_volKg_value * $os->currency_rate;

                $offerTotalAirfreightsCostPrice    += $os->total_airfreight_cost_price;
                $offerTotalAirfreightsCostPriceUSD += $os->total_airfreight_cost_price_usd;

                $os->total_airfreight_sale_price     = $os->total_volKg * $os->total_sale_volKg_value * $currency_rate;
                $os->total_airfreight_sale_price_usd = $os->total_volKg * $os->total_sale_volKg_value * $os->currency_rate;

                $offerTotalAirfreightsSalePrice    += $os->total_airfreight_sale_price;
                $offerTotalAirfreightsSalePriceUSD += $os->total_airfreight_sale_price_usd;

                if ($os->oursurplus->sale_currency != 'USD') {
                    $os->offerSalePriceMPdfUsd = ($os->offerQuantityM > 0 && $os->offerSalePriceM > 0) ? number_format(
                        ($os->offerSalePriceM + $os->species_crate->sale_price + ($crate_volKg * $os->total_sale_volKg_value)) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePriceFPdfUsd = ($os->offerQuantityF > 0 && $os->offerSalePriceF > 0) ? number_format(
                        ($os->offerSalePriceF + $os->species_crate->sale_price + ($crate_volKg * $os->total_sale_volKg_value)) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePriceUPdfUsd = ($os->offerQuantityU > 0 && $os->offerSalePriceU > 0) ? number_format(
                        ($os->offerSalePriceU + $os->species_crate->sale_price + ($crate_volKg * $os->total_sale_volKg_value)) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePricePPdfUsd = ($os->offerQuantityP > 0 && $os->offerSalePriceP > 0) ? number_format(
                        ($os->offerSalePriceP + ($os->species_crate->sale_price * 2) + ($crate_volKg * $os->total_sale_volKg_value * 2)) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                }

                $os->offerSalePriceMPdf = ($os->offerQuantityM > 0 && $os->offerSalePriceM > 0) ? number_format(
                    $os->offerSalePriceM + $os->species_crate->sale_price + ($crate_volKg * $os->total_sale_volKg_value),
                    2, '.', ''
                ) : "";
                $os->offerSalePriceFPdf = ($os->offerQuantityF > 0 && $os->offerSalePriceF > 0) ? number_format(
                    $os->offerSalePriceF + $os->species_crate->sale_price + ($crate_volKg * $os->total_sale_volKg_value),
                    2, '.', ''
                ) : "";
                $os->offerSalePriceUPdf = ($os->offerQuantityU > 0 && $os->offerSalePriceU > 0) ? number_format(
                    $os->offerSalePriceU + $os->species_crate->sale_price + ($crate_volKg * $os->total_sale_volKg_value),
                    2, '.', ''
                ) : "";
                $os->offerSalePricePPdf = ($os->offerQuantityP > 0 && $os->offerSalePriceP > 0) ? number_format(
                    $os->offerSalePriceP + ($os->species_crate->sale_price * 2) + ($crate_volKg * $os->total_sale_volKg_value * 2),
                    2, '.', ''
                ) : "";
            } elseif ($offer->airfreight_type != "volKgRates" && $offer->sale_price_type != "ExZoo") {
                if ($os->oursurplus->sale_currency != 'USD') {
                    $os->offerSalePriceMPdfUsd = ($os->offerQuantityM > 0 && $os->offerSalePriceM > 0) ? number_format(
                        ($os->offerSalePriceM + $os->species_crate->sale_price) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePriceFPdfUsd = ($os->offerQuantityF > 0 && $os->offerSalePriceF > 0) ? number_format(
                        ($os->offerSalePriceF + $os->species_crate->sale_price) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePriceUPdfUsd = ($os->offerQuantityU > 0 && $os->offerSalePriceU > 0) ? number_format(
                        ($os->offerSalePriceU + $os->species_crate->sale_price) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                    $os->offerSalePricePPdfUsd = ($os->offerQuantityP > 0 && $os->offerSalePriceP > 0) ? number_format(
                        ($os->offerSalePriceP + ($os->species_crate->sale_price * 2)) * $usdCurrencyValue,
                        2, '.', ''
                    ) : "";
                }

                $os->offerSalePriceMPdf = ($os->offerQuantityM > 0 && $os->offerSalePriceM > 0) ? number_format(
                    $os->offerSalePriceM + $os->species_crate->sale_price,
                    2, '.', ''
                ) : "";
                $os->offerSalePriceFPdf = ($os->offerQuantityF > 0 && $os->offerSalePriceF > 0) ? number_format(
                    $os->offerSalePriceF + $os->species_crate->sale_price,
                    2, '.', ''
                ) : "";
                $os->offerSalePriceUPdf = ($os->offerQuantityU > 0 && $os->offerSalePriceU > 0) ? number_format(
                    $os->offerSalePriceU + $os->species_crate->sale_price,
                    2, '.', ''
                ) : "";
                $os->offerSalePricePPdf = ($os->offerQuantityP > 0 && $os->offerSalePriceP > 0) ? number_format(
                    $os->offerSalePriceP + ($os->species_crate->sale_price * 2),
                    2, '.', ''
                ) : "";
            }

            if (trim($os->offerSalePriceMPdf) != '' || trim($os->offerSalePriceFPdf) != '' || trim($os->offerSalePriceUPdf) != '' || trim($os->offerSalePricePPdf) != '') {
                $os->subtotal_sale_price = (trim($os->offerSalePriceMPdf) != '') ? $os->offerSalePriceMPdf * $os->offerQuantityM : 0;
                $os->subtotal_sale_price += (trim($os->offerSalePriceFPdf) != '') ? $os->offerSalePriceFPdf * $os->offerQuantityF : 0;
                $os->subtotal_sale_price += (trim($os->offerSalePriceUPdf) != '') ? $os->offerSalePriceUPdf * $os->offerQuantityU : 0;
                $os->subtotal_sale_price += (trim($os->offerSalePricePPdf) != '') ? $os->offerSalePricePPdf * $os->offerQuantityP : 0;
            } else {
                $os->subtotal_sale_price = 0;
            }
        
            if ($os->oursurplus->sale_currency != 'USD') {
                $os->subtotal_sale_price_usd = number_format($os->subtotal_sale_price * $usdCurrencyValue, 2, '.', '');
            }

            //Surplus details
            if (!empty($os->origin)) {
                $os->surplusDetails = $os->origin;
            } else {
                $os->surplusDetails = "";
            }

            //Located at
            if (!empty($os->region)) {
                $region        = Region::find($os->region_id);
                $os->locatedAt = $region->name;
            } else {
                $os->locatedAt .= $os->oursurplus->location;
            }
        }


        $offer->offerTotalSpeciesCostPrice        = $offerTotalSpeciesCostPrice;
        $offer->offerTotalSpeciesCostPriceUSD     = $offerTotalSpeciesCostPriceUSD;
        $offer->offerTotalSpeciesSalePrice        = $offerTotalSpeciesSalePrice;
        $offer->offerTotalSpeciesSalePriceUSD     = $offerTotalSpeciesSalePriceUSD;
        $offer->offerTotalCratesCostPrice         = $offerTotalCratesCostPrice;
        $offer->offerTotalCratesCostPriceUSD      = $offerTotalCratesCostPriceUSD;
        $offer->offerTotalCratesSalePrice         = $offerTotalCratesSalePrice;
        $offer->offerTotalCratesSalePriceUSD      = $offerTotalCratesSalePriceUSD;
        $offer->offerTotalVolKg                   = $offerTotalVolKg;
        $offer->offerTotalActualWeight            = $offerTotalActualWeight;
        $offer->offerTotalAirfreightsCostPrice    = $offerTotalAirfreightsCostPrice;
        $offer->offerTotalAirfreightsSalePrice    = $offerTotalAirfreightsSalePrice;
        $offer->offerTotalAirfreightsCostPriceUSD = $offerTotalAirfreightsCostPriceUSD;
        $offer->offerTotalAirfreightsSalePriceUSD = $offerTotalAirfreightsSalePriceUSD;

        $offerTotalTransportTruckCostPrice           = ($offer->airfreight_type == 'byTruck' && $offer->transport_truck && $offer->sale_price_type != 'ExZoo') ? $offer->transport_truck->total_km * $offer->transport_truck->cost_rate_per_km : 0;
        $offer->offerTotalTransportTruckCostPrice    = $offerTotalTransportTruckCostPrice;
        $offer->offerTotalTransportTruckCostPriceUSD = ($offerTotalTransportTruckCostPrice * $general_rate_usd);
        $offerTotalTransportTruckSalePrice           = ($offer->airfreight_type == 'byTruck' && $offer->transport_truck && $offer->sale_price_type != 'ExZoo') ? $offer->transport_truck->total_km * $offer->transport_truck->sale_rate_per_km : 0;
        $offer->offerTotalTransportTruckSalePrice    = $offerTotalTransportTruckSalePrice;
        $offer->offerTotalTransportTruckSalePriceUSD = ($offerTotalTransportTruckSalePrice * $general_rate_usd);

        $offerTotalAirfreightPalletCostPrice           = ($offer->airfreight_type == 'pallets' && $offer->airfreight_pallet && $offer->sale_price_type != 'ExZoo') ? $offer->airfreight_pallet->pallet_quantity * $offer->airfreight_pallet->pallet_cost_value : 0;
        $offer->offerTotalAirfreightPalletCostPrice    = $offerTotalAirfreightPalletCostPrice;
        $offer->offerTotalAirfreightPalletCostPriceUSD = ($offerTotalAirfreightPalletCostPrice * $general_rate_usd);
        $offerTotalAirfreightPalletSalePrice           = ($offer->airfreight_type == 'pallets' && $offer->airfreight_pallet && $offer->sale_price_type != 'ExZoo') ? $offer->airfreight_pallet->pallet_quantity * $offer->airfreight_pallet->pallet_sale_value : 0;
        $offer->offerTotalAirfreightPalletSalePrice    = $offerTotalAirfreightPalletSalePrice;
        $offer->offerTotalAirfreightPalletSalePriceUSD = ($offerTotalAirfreightPalletSalePrice * $general_rate_usd);

        $offerTotalCostPrice = $offerTotalSpeciesCostPrice + $offerTotalCratesCostPrice + $offerTotalAirfreightsCostPrice + $offerTotalAirfreightPalletCostPrice + $offerTotalTransportTruckCostPrice;
        $offerTotalSalePrice = $offerTotalSpeciesSalePrice + $offerTotalCratesSalePrice + $offerTotalAirfreightsSalePrice + $offerTotalAirfreightPalletSalePrice + $offerTotalTransportTruckSalePrice;

        $offerTotalCostPriceUSD = $offerTotalSpeciesCostPriceUSD + $offerTotalCratesCostPriceUSD + $offerTotalAirfreightsCostPriceUSD + $offer->offerTotalAirfreightPalletCostPriceUSD + $offer->offerTotalTransportTruckCostPriceUSD;
        $offerTotalSalePriceUSD = $offerTotalSpeciesSalePriceUSD + $offerTotalCratesSalePriceUSD + $offerTotalAirfreightsSalePriceUSD + $offer->offerTotalAirfreightPalletSalePriceUSD + $offer->offerTotalTransportTruckSalePriceUSD;

        $offerAdditionalTests        = $offer->additional_costs()->where('is_test', 1)->get();
        $offer->offerAdditionalTests = $offerAdditionalTests;

        $offerAdditionalTestsTotalCost    = 0;
        $offerAdditionalTestsTotalSale    = 0;
        $offerAdditionalTestsTotalCostUSD = 0;
        $offerAdditionalTestsTotalSaleUSD = 0;
        if ($offer->sale_price_type != 'ExZoo') {
            foreach ($offerAdditionalTests as $additionalTest) {
                $offerAdditionalTestsTotalCost += $additionalTest->quantity * $additionalTest->costPrice;
                $offerAdditionalTestsTotalSale += $additionalTest->quantity * $additionalTest->salePrice;

                $offerAdditionalTestsTotalCostUSD += $additionalTest->quantity * $additionalTest->costPrice * $general_rate_usd;
                $offerAdditionalTestsTotalSaleUSD += $additionalTest->quantity * $additionalTest->salePrice * $general_rate_usd;
            }
        }
        $offer->offerAdditionalTestsTotalCost    = $offerAdditionalTestsTotalCost;
        $offer->offerAdditionalTestsTotalSale    = $offerAdditionalTestsTotalSale;
        $offer->offerAdditionalTestsTotalCostUSD = $offerAdditionalTestsTotalCostUSD;
        $offer->offerAdditionalTestsTotalSaleUSD = $offerAdditionalTestsTotalSaleUSD;

        $offerTotalCostPrice    += $offerAdditionalTestsTotalCost;
        $offerTotalSalePrice    += $offerAdditionalTestsTotalSale;
        $offerTotalCostPriceUSD += $offerAdditionalTestsTotalCostUSD;
        $offerTotalSalePriceUSD += $offerAdditionalTestsTotalSaleUSD;

        $offerAdditionalCosts        = $offer->additional_costs()->where('is_test', 0)->get();
        $offer->offerAdditionalCosts = $offerAdditionalCosts;

        $offerAdditionalCostsTotalCost    = 0;
        $offerAdditionalCostsTotalSale    = 0;
        $offerAdditionalCostsTotalCostUSD = 0;
        $offerAdditionalCostsTotalSaleUSD = 0;
        if ($offer->sale_price_type != 'ExZoo') {
            foreach ($offerAdditionalCosts as $additionalCost) {
                if (Str::contains(Str::lower($additionalCost->name), 'discount')) {
                    $discountSalePrice = abs($additionalCost->salePrice);

                    $offerAdditionalCostsTotalCost -= $additionalCost->quantity * $additionalCost->costPrice;
                    $offerAdditionalCostsTotalSale -= $additionalCost->quantity * $discountSalePrice;

                    $offerAdditionalCostsTotalCostUSD -= $additionalCost->quantity * $additionalCost->costPrice * $general_rate_usd;
                    $offerAdditionalCostsTotalSaleUSD -= $additionalCost->quantity * $discountSalePrice * $general_rate_usd;
                } else {
                    $offerAdditionalCostsTotalCost += $additionalCost->quantity * $additionalCost->costPrice;
                    $offerAdditionalCostsTotalSale += $additionalCost->quantity * $additionalCost->salePrice;

                    $offerAdditionalCostsTotalCostUSD += $additionalCost->quantity * $additionalCost->costPrice * $general_rate_usd;
                    $offerAdditionalCostsTotalSaleUSD += $additionalCost->quantity * $additionalCost->salePrice * $general_rate_usd;
                }
            }
        }
        $offer->offerAdditionalCostsTotalCost    = $offerAdditionalCostsTotalCost;
        $offer->offerAdditionalCostsTotalSale    = $offerAdditionalCostsTotalSale;
        $offer->offerAdditionalCostsTotalCostUSD = $offerAdditionalCostsTotalCostUSD;
        $offer->offerAdditionalCostsTotalSaleUSD = $offerAdditionalCostsTotalSaleUSD;

        $offerTotalCostPrice    += $offerAdditionalCostsTotalCost;
        $offerTotalSalePrice    += $offerAdditionalCostsTotalSale;
        $offerTotalCostPriceUSD += $offerAdditionalCostsTotalCostUSD;
        $offerTotalSalePriceUSD += $offerAdditionalCostsTotalSaleUSD;

        $extraFeeValue    = 0;
        $extraFeeValueUSD = 0;
        if ($offerTotalSalePrice < 5000) {
            $extraFeeValue    = 750;
            $extraFeeValueUSD = ($extraFeeValue * $general_rate_usd);
            if ($offer->extra_fee) {
                $offerTotalSalePrice    = $offerTotalSalePrice + $extraFeeValue;
                $offerTotalSalePriceUSD = $offerTotalSalePriceUSD + $extraFeeValueUSD;
            }
        }

        $offer->extraFeeValue    = $extraFeeValue;
        $offer->extraFeeValueUSD = $extraFeeValueUSD;

        $offer->offerTotalCostPrice    = $offerTotalCostPrice;
        $offer->offerTotalSalePrice    = $offerTotalSalePrice;
        $offer->offerTotalCostPriceUSD = $offerTotalCostPriceUSD;
        $offer->offerTotalSalePriceUSD = $offerTotalSalePriceUSD;
        $offer->currency_rate_eur      = number_format(CurrencyRate::latest()->value('USD_EUR'), 2, '.', '');
        $offer->total_profit           = number_format(($offerTotalSalePrice - $offerTotalCostPrice), 2, '.', '');
        $offer->total_profitUSD           = number_format(($offerTotalSalePriceUSD - $offerTotalCostPriceUSD), 2, '.', '');
        DB::table('offers')
            ->where('id', $offer->id)
            ->update(['total_profit' => $offer->total_profit]);
        $offer->percent_profit = ($offerTotalCostPrice > 0) ? ($offer->total_profit * 100) / $offerTotalCostPrice : 0;
        $offer->percent_profitUSD = ($offerTotalCostPriceUSD > 0) ? ($offer->total_profitUSD * 100) / $offerTotalCostPriceUSD : 0;

        return $offer;
    }

    public function create_offer_pdf($id, $x_quantity = false)
    {
        $offer = $this->calculate_offer_totals($id);

        $dateOfToday = Carbon::now()->format('F j, Y');

        $rates = CurrencyRate::orderBy('date', 'desc')->take(10)->get();

        foreach ($offer->species_ordered as $item) {
            $item->offerQuantityM = ($item->offerQuantityM > 0) ? (($x_quantity) ? 'x' : $item->offerQuantityM) : 0;
            $item->offerQuantityF = ($item->offerQuantityF > 0) ? (($x_quantity) ? 'x' : $item->offerQuantityF) : 0;
            $item->offerQuantityU = ($item->offerQuantityU > 0) ? (($x_quantity) ? 'x' : $item->offerQuantityU) : 0;
        }

        /*$pdf = DOMPDF::loadView('pdf_documents.offer_pdf', compact('offer', 'dateOfToday', 'rates'))->setPaper('a4', 'portrait');

        $fileName = "Offer " . $offer->full_number;

        return $pdf->stream($fileName . '.pdf');*/

        if (!empty($offer->client)) {
            if ($offer->client->organisation != null && $offer->client->organisation->country != null && $offer->client->organisation->country->language === 'ES') {
                return view('pdf_documents.offer_spanish_pdf', compact('offer', 'dateOfToday', 'rates'))->render();
            } else {
                return view('pdf_documents.offer_pdf', compact('offer', 'dateOfToday', 'rates'))->render();
            }
        } else {
            if ($offer->organisation != null && $offer->organisation->country != null && $offer->organisation->country->language === 'ES') {
                return view('pdf_documents.offer_spanish_pdf', compact('offer', 'dateOfToday', 'rates'))->render();
            } else {
                return view('pdf_documents.offer_pdf', compact('offer', 'dateOfToday', 'rates'))->render();
            }
        }
    }
}
