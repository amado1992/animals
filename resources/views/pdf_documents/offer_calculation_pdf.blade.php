<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Zoo Services">

        <style>
            @page {
                margin: 10px;
            }

            .pdf-container {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 10px;
            }
        </style>
    </head>
    <body>
        <div class="pdf-container">

            <div class="row">
                <table style="width: 100%;" cellspacing="0" border="0">
                    <tbody>
                        <tr style="font-weight: bold;">
                            <td>@if ($parent_view === 'order_details') Order number: {{ $offer->order->full_number }} @else Offer number: {{ $offer->full_number }} @endif</td>
                            <td>Date: {{ $dateOfToday }}</td>
                        </tr>
                        <tr>
                            @if ($offer->client)
                            <td>
								<b>Client info</b><br>
                                <b>Client:</b> {{ $offer->client->full_name }}<br>
								<b>E-mail: </b>{{ $offer->client->email }}<br>
								<b>Phone: </b>{{ ($offer->client->organisation) ? $offer->client->organisation->phone : '' }}<br>
								<b>Country: </b>{{ ($offer->client->country) ? $offer->client->country->name : '' }}<br>
								<b>Level: </b>{{ ($offer->client->organisation) ? $offer->client->organisation->level : '' }}
                            </td>
                            @endif
							<td>
								<b>Supplier info</b><br>
							@if ($offer->supplier)
								<b>Contact: </b>{{ $offer->supplier->full_name }}<br>
								<b>E-mail: </b>{{ $offer->supplier->email }}<br>
								<b>Phone: </b>{{ ($offer->supplier->organisation) ? $offer->supplier->organisation->phone : '' }}<br>
								<b>Country: </b>{{ ($offer->supplier->country) ? $offer->supplier->country->name : '' }}<br>
								<b>Level: </b>{{ ($offer->supplier->organisation) ? $offer->supplier->organisation->level : '' }}
							@else
								<b>Contact: </b><br>
								<b>E-mail: </b>izs@zoo-services.com<br>
								<b>Phone: </b><br>
								<b>Country: </b>The Netherlands
							@endif
							</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <table style="width: 100%;" cellpadding="0" cellspacing="2">
                    <thead>
                        <tr style="background-color: #4f6228; color: #ffffff;">
                            <th colspan="4">SPECIES</th>
                            <th></th>
                            <th colspan="3">COST PRICES</th>
                            <th colspan="3">SALE PRICES</th>
                            <th>PROFIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                            <td style="width: 20px;">Qty</td>
                            <td style="width: 25px;">Sex</td>
                            <td style="width: 150px;">Scientific name</td>
                            <td style="width: 145px;">Supplier</td>
                            <td style="width: 25px;"></td>
                            <td style="width: 55px;">Price each</td>
                            <td style="width: 55px;">Price total</td>
                            <td style="width: 55px;">Price USD</td>
                            <td style="width: 55px;">Price each</td>
                            <td style="width: 55px;">Price total</td>
                            <td style="width: 55px;">Price USD</td>
                            <td style="width: 55px;">In USD</td>
                        </tr>

                        @foreach ($offer->species_ordered as $species)
                            @if ($species->offerQuantityM > 0)
                            <tr style="background-color: #ebf1e8; vertical-align: top;">
                                <td style="text-align: center;">{{ $species->offerQuantityM }}</td>
                                <td style="text-align: center;">M</td>
                                <td>
                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                        {{ $species->oursurplus->animal->common_name }} ({{ $species->oursurplus->animal->scientific_name }})
                                    @else
                                        ERROR - NO STANDARD SURPLUS
                                    @endif
                                </td>
                                <td>IZS</td>
                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerCostPriceM, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityM * $species->offerCostPriceM, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityM * $species->offerCostPriceM * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerSalePriceM, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityM * $species->offerSalePriceM, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityM * $species->offerSalePriceM * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityM * ($species->offerSalePriceM - $species->offerCostPriceM) * $species->currency_rate, 2, '.', '') }}</td>
                            </tr>
                            @endif
                            @if ($species->offerQuantityF > 0)
                            <tr style="background-color: #ebf1e8; vertical-align: top;">
                                <td style="text-align: center;">{{ $species->offerQuantityF }}</td>
                                <td style="text-align: center;">F</td>
                                <td>
                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                        {{ $species->oursurplus->animal->common_name }} ({{ $species->oursurplus->animal->scientific_name }})
                                    @else
                                        ERROR - NO STANDARD SURPLUS
                                    @endif
                                </td>
                                <td>IZS</td>
                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerCostPriceF, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityF * $species->offerCostPriceF, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityF * $species->offerCostPriceF * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerSalePriceF, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityF * $species->offerSalePriceF, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityF * $species->offerSalePriceF * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityF * ($species->offerSalePriceF - $species->offerCostPriceF) * $species->currency_rate, 2, '.', '') }}</td>
                            </tr>
                            @endif
                            @if ($species->offerQuantityU > 0)
                            <tr style="background-color: #ebf1e8; vertical-align: top;">
                                <td style="text-align: center;">{{ $species->offerQuantityU }}</td>
                                <td style="text-align: center;">U</td>
                                <td>
                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                        {{ $species->oursurplus->animal->common_name }} ({{ $species->oursurplus->animal->scientific_name }})
                                    @else
                                        ERROR - NO STANDARD SURPLUS
                                    @endif
                                </td>
                                <td>IZS</td>
                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerCostPriceU, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityU * $species->offerCostPriceU, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityU * $species->offerCostPriceU * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerSalePriceU, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityU * $species->offerSalePriceU, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityU * $species->offerSalePriceU * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityU * ($species->offerSalePriceU - $species->offerCostPriceU) * $species->currency_rate, 2, '.', '') }}</td>
                            </tr>
                            @endif
                            @if ($species->offerQuantityP > 0)
                            <tr style="background-color: #ebf1e8; vertical-align: top;">
                                <td style="text-align: center;">{{ $species->offerQuantityP }}</td>
                                <td style="text-align: center;">P</td>
                                <td>
                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                        {{ $species->oursurplus->animal->common_name }} ({{ $species->oursurplus->animal->scientific_name }})
                                    @else
                                        ERROR - NO STANDARD SURPLUS
                                    @endif
                                </td>
                                <td>IZS</td>
                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerCostPriceP, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityP * $species->offerCostPriceP , 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityP * $species->offerCostPriceP * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerSalePriceP, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityP * $species->offerSalePriceP, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityP * $species->offerSalePriceP * $species->currency_rate, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format($species->offerQuantityP * ($species->offerSalePriceP - $species->offerCostPriceP) * $species->currency_rate, 2, '.', '') }}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr style="background-color: #ebf1e8;">
                            <td></td>
                            <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">{{ number_format($offer->offerTotalSpeciesCostPriceUSD, 2, '.', '') }}</td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">{{ number_format($offer->offerTotalSpeciesSalePriceUSD, 2, '.', '') }}</td>
                            <td style="text-align: right;">{{ number_format(($offer->offerTotalSpeciesSalePriceUSD - $offer->offerTotalSpeciesCostPriceUSD), 2, '.', '') }}</td>
                        </tr>
                        <tr><td colspan="12"></td></tr>
                        @if ($offer->sale_price_type != "ExZoo")
                            <tr style="background-color: #4f6228; color: #ffffff;">
                                <th colspan="4">CRATES</th>
                                <th></th>
                                <th colspan="3">COST PRICES</th>
                                <th colspan="3">SALE PRICES</th>
                                <th>PROFIT</th>
                            </tr>
                            <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                <td style="width: 20px;">Qty</td>
                                <td colspan="2" style="width: 175px;">Dimensions</td>
                                <td style="width: 145px;">Vol. weight (Kg)</td>
                                <td style="width: 25px;"></td>
                                <td style="width: 55px;">Price each</td>
                                <td style="width: 55px;">Price total</td>
                                <td style="width: 55px;">Price USD</td>
                                <td style="width: 55px;">Price each</td>
                                <td style="width: 55px;">Price total</td>
                                <td style="width: 55px;">Price USD</td>
                                <td style="width: 55px;">In USD</td>
                            </tr>
                            @foreach ($offer->species_ordered as $species)
                                @if ($species->species_crate->quantity_males > 0)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">{{ $species->species_crate->quantity_males }}</td>
                                        <td colspan="2" style="text-align: center;">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
                                        <td style="text-align: center;">{{ $species->species_crate->m_volKg }}</td>
                                        <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_males * $species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_males * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_males * $species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_males * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_males * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                                @if ($species->species_crate->quantity_females > 0)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">{{ $species->species_crate->quantity_females }}</td>
                                        <td colspan="2" style="text-align: center;">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
                                        <td style="text-align: center;">{{ $species->species_crate->f_volKg }}</td>
                                        <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_females * $species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_females * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_females * $species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_females * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_females * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                                @if ($species->species_crate->quantity_unsexed > 0)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">{{ $species->species_crate->quantity_unsexed }}</td>
                                        <td colspan="2" style="text-align: center;">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
                                        <td style="text-align: center;">{{ $species->species_crate->u_volKg }}</td>
                                        <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_unsexed * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                                @if ($species->species_crate->quantity_pairs > 0)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">{{ $species->species_crate->quantity_pairs }}</td>
                                        <td colspan="2" style="text-align: center;">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
                                        <td style="text-align: center;">{{ $species->species_crate->p_volKg }}</td>
                                        <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->cost_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->sale_price, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($species->species_crate->quantity_pairs * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="background-color: #ebf1e8;">
                                <td></td>
                                <td colspan="2" style="font-weight: bold;">TOTAL:</td>
                                <td style="text-align: center;">{{ number_format($offer->offerTotalVolKg, 2, '.', '') }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">{{ number_format($offer->offerTotalCratesCostPriceUSD, 2, '.', '') }}</td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">{{ number_format($offer->offerTotalCratesSalePriceUSD, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format(($offer->offerTotalCratesSalePriceUSD - $offer->offerTotalCratesCostPriceUSD), 2, '.', '') }}</td>
                            </tr>
                            <tr><td colspan="12"></td></tr>
                            @if ($offer->airfreight_type == "volKgRates")
                                <tr style="background-color: #4f6228; color: #ffffff;">
                                    <th colspan="4">AIRFREIGHTS</th>
                                    <th></th>
                                    <th colspan="3">COST PRICES</th>
                                    <th colspan="3">SALE PRICES</th>
                                    <th>PROFIT</th>
                                </tr>
                                <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                    <td style="width: 20px;">Qty</td>
                                    <td colspan="2" style="width: 175px;">From - To</td>
                                    <td style="width: 145px;">Vol. weight (Kg)</td>
                                    <td style="width: 25px;"></td>
                                    <td style="width: 55px;">Price each</td>
                                    <td style="width: 55px;">Price total</td>
                                    <td style="width: 55px;">Price USD</td>
                                    <td style="width: 55px;">Price each</td>
                                    <td style="width: 55px;">Price total</td>
                                    <td style="width: 55px;">Price USD</td>
                                    <td style="width: 55px;">In USD</td>
                                </tr>
                                @foreach ($offer->species_ordered as $species)
                                    @foreach ($species->species_airfreights as $specie_airfreight)
                                        @if ($species->offerQuantityM > 0)
                                            <tr style="background-color: #ebf1e8;">
                                                <td style="text-align: center;">{{ $species->offerQuantityM }}</td>
                                                <td colspan="2" style="text-align: center;">@if ($specie_airfreight->airfreight) {{ $specie_airfreight->airfreight->from_continent->name }} - {{ $specie_airfreight->airfreight->to_continent->name }} @else - @endif</td>
                                                <td style="text-align: center;">{{ number_format($species->species_crate->m_volKg, 2, '.', '') }}</td>
                                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->m_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->m_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->m_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->m_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->m_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                        @if ($species->offerQuantityF > 0)
                                            <tr style="background-color: #ebf1e8;">
                                                <td style="text-align: center;">{{ $species->offerQuantityF }}</td>
                                                <td colspan="2" style="text-align: center;">@if ($specie_airfreight->airfreight) {{ $specie_airfreight->airfreight->from_continent->name }} - {{ $specie_airfreight->airfreight->to_continent->name }} @else - @endif</td>
                                                <td style="text-align: center;">{{ number_format($species->species_crate->f_volKg, 2, '.', '') }}</td>
                                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->f_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->f_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->f_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->f_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->f_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                        @if ($species->offerQuantityU > 0)
                                            <tr style="background-color: #ebf1e8;">
                                                <td style="text-align: center;">{{ $species->offerQuantityU }}</td>
                                                <td colspan="2" style="text-align: center;">@if ($specie_airfreight->airfreight) {{ $specie_airfreight->airfreight->from_continent->name }} - {{ $specie_airfreight->airfreight->to_continent->name }} @else - @endif</td>
                                                <td style="text-align: center;">{{ number_format($species->species_crate->u_volKg, 2, '.', '') }}</td>
                                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->u_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->u_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->u_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->u_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->u_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                        @if ($species->offerQuantityP > 0)
                                            <tr style="background-color: #ebf1e8;">
                                                <td style="text-align: center;">{{ $species->offerQuantityP }}</td>
                                                <td colspan="2" style="text-align: center;">@if ($specie_airfreight->airfreight) {{ $specie_airfreight->airfreight->from_continent->name }} - {{ $specie_airfreight->airfreight->to_continent->name }} @else - @endif</td>
                                                <td style="text-align: center;">{{ number_format($species->species_crate->p_volKg, 2, '.', '') }}</td>
                                                <td style="text-align: center;">{{ $species->oursurplus->sale_currency }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->p_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->p_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->p_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->p_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                                                <td style="text-align: right;">{{ number_format($species->species_crate->p_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                <tr style="background-color: #ebf1e8;">
                                    <td></td>
                                    <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightsCostPriceUSD, 2, '.', '') }}</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightsSalePriceUSD, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format(($offer->offerTotalAirfreightsSalePriceUSD - $offer->offerTotalAirfreightsCostPriceUSD), 2, '.', '') }}</td>
                                </tr>
                                <tr><td colspan="12"></td></tr>
                            @elseif ($offer->airfreight_type == "byTruck")
                                <tr style="background-color: #4f6228; color: #ffffff;">
                                    <th colspan="4">TRANSPORT BY TRUCK</th>
                                    <th></th>
                                    <th colspan="3">COST PRICES</th>
                                    <th colspan="3">SALE PRICES</th>
                                    <th>PROFIT</th>
                                </tr>
                                <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                    <td style="width: 20px;">Qty</td>
                                    <td colspan="2" style="width: 175px;">From - To</td>
                                    <td style="width: 145px;">Total km</td>
                                    <td style="width: 25px;"></td>
                                    <td style="width: 55px;">Price per km</td>
                                    <td style="width: 55px;">Price total</td>
                                    <td style="width: 55px;">Price USD</td>
                                    <td style="width: 55px;">Price per km</td>
                                    <td style="width: 55px;">Price total</td>
                                    <td style="width: 55px;">Price USD</td>
                                    <td style="width: 55px;">In USD</td>
                                </tr>
                                @if ($offer->transport_truck)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">1</td>
                                        <td colspan="2" style="text-align: center;">{{$offer->transport_truck->origin_country->name}} - {{$offer->transport_truck->delivery_country->name}}</td>
                                        <td style="text-align: center;">{{ number_format($offer->transport_truck->total_km, 2, '.', '') }}</td>
                                        <td style="text-align: center;">{{$offer->offer_currency}}</td>
                                        <td style="text-align: right;">{{ number_format($offer->transport_truck->cost_rate_per_km, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckCostPrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckCostPrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->transport_truck->sale_rate_per_km, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckSalePrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckSalePrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format(($offer->offerTotalTransportTruckSalePrice - $offer->offerTotalTransportTruckCostPrice) * $general_rate_usd, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                                <tr style="background-color: #ebf1e8;">
                                    <td></td>
                                    <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckCostPrice * $general_rate_usd, 2, '.', '') }}</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckSalePrice * $general_rate_usd, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format(($offer->offerTotalTransportTruckSalePrice - $offer->offerTotalTransportTruckCostPrice) * $general_rate_usd, 2, '.', '') }}</td>
                                </tr>
                                <tr><td colspan="12"></td></tr>
                            @elseif ($offer->airfreight_type == "pallets")
                                <tr style="background-color: #4f6228; color: #ffffff;">
                                    <th colspan="4">AIRFREIGHT BY PALLETS</th>
                                    <th></th>
                                    <th colspan="3">COST PRICES</th>
                                    <th colspan="3">SALE PRICES</th>
                                    <th>PROFIT</th>
                                </tr>
                                <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                    <td style="width: 20px;">Qty</td>
                                    <td colspan="2" style="width: 175px;">From - To</td>
                                    <td style="width: 145px;">Vol. weight</td>
                                    <td style="width: 25px;"></td>
                                    <td style="width: 55px;">Price per pallet</td>
                                    <td style="width: 55px;">Price total</td>
                                    <td style="width: 55px;">Price USD</td>
                                    <td style="width: 55px;">Price per pallet</td>
                                    <td style="width: 55px;">Price total</td>
                                    <td style="width: 55px;">Price USD</td>
                                    <td style="width: 55px;">In USD</td>
                                </tr>
                                @if ($offer->airfreight_pallet)
                                    <tr style="background-color: #ebf1e8;">
                                        <td>{{$offer->airfreight_pallet->pallet_quantity}}</td>
                                        <td colspan="2" style="text-align: center;">{{$offer->airfreight_pallet->from_continent->name}} - {{$offer->airfreight_pallet->to_continent->name}}</td>
                                        <td style="text-align: center;">{{ number_format($offer->offerTotalVolKg, 2, '.', '') }}</td>
                                        <td style="text-align: center;">{{$offer->offer_currency}}</td>
                                        <td style="text-align: right;">{{ number_format($offer->airfreight_pallet->pallet_cost_value, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightPalletCostPrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightPalletCostPrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->airfreight_pallet->pallet_sale_value, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalTransportTruckSalePrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightPalletSalePrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format(($offer->offerTotalAirfreightPalletSalePrice - $offer->offerTotalAirfreightPalletCostPrice) * $general_rate_usd, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                                <tr style="background-color: #ebf1e8;">
                                    <td></td>
                                    <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightPalletCostPrice * $general_rate_usd, 2, '.', '') }}</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">{{ number_format($offer->offerTotalAirfreightPalletSalePrice * $general_rate_usd, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format(($offer->offerTotalAirfreightPalletSalePrice - $offer->offerTotalAirfreightPalletCostPrice) * $general_rate_usd, 2, '.', '') }}</td>
                                </tr>
                                <tr><td colspan="12"></td></tr>
                            @endif
                            <tr style="background-color: #4f6228; color: #ffffff;">
                                <th colspan="4">TESTS & QUARANTINE</th>
                                <th></th>
                                <th colspan="3">COST PRICES</th>
                                <th colspan="3">SALE PRICES</th>
                                <th>PROFIT</th>
                            </tr>
                            <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                <td style="width: 20px;">Qty</td>
                                <td colspan="3" style="width: 320px;">Type</td>
                                <td style="width: 25px;"></td>
                                <td style="width: 55px;">Price each</td>
                                <td style="width: 55px;">Price total</td>
                                <td style="width: 55px;">Price USD</td>
                                <td style="width: 55px;">Price each</td>
                                <td style="width: 55px;">Price total</td>
                                <td style="width: 55px;">Price USD</td>
                                <td style="width: 55px;">In USD</td>
                            </tr>
                            @foreach($offer->offerAdditionalTests as $at)
                                @if ($at->costPrice != 0 || $at->salePrice != 0)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">{{ $at->quantity }}</td>
                                        <td colspan="3">{{ $at->name }}</td>
                                        <td style="text-align: center;">{{ $at->currency}}</td>
                                        <td style="text-align: right;">{{ number_format($at->costPrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($at->quantity * $at->costPrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($at->quantity * $at->costPrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($at->salePrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($at->quantity * $at->salePrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($at->quantity * $at->salePrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($at->quantity * ($at->salePrice - $at->costPrice) * $general_rate_usd, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="background-color: #ebf1e8;">
                                <td></td>
                                <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">{{ number_format($offer->offerAdditionalTestsTotalCostUSD, 2, '.', '') }}</td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">{{ number_format($offer->offerAdditionalTestsTotalSaleUSD, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ number_format(($offer->offerAdditionalTestsTotalSaleUSD - $offer->offerAdditionalTestsTotalCostUSD), 2, '.', '') }}</td>
                            </tr>
                            <tr><td colspan="12"></td></tr>
                            <tr style="background-color: #4f6228; color: #ffffff;">
                                <th colspan="4">BASIC COSTS</th>
                                <th></th>
                                <th colspan="3">COST PRICES</th>
                                <th colspan="3">SALE PRICES</th>
                                <th>PROFIT</th>
                            </tr>
                            <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                <td style="width: 20px;">Qty</td>
                                <td colspan="3" style="width: 320px;">Type</td>
                                <td style="width: 25px;"></td>
                                <td style="width: 55px;">Price each</td>
                                <td style="width: 55px;">Price total</td>
                                <td style="width: 55px;">Price USD</td>
                                <td style="width: 55px;">Price each</td>
                                <td style="width: 55px;">Price total</td>
                                <td style="width: 55px;">Price USD</td>
                                <td style="width: 55px;">In USD</td>
                            </tr>
                            @foreach($offer->offerAdditionalCosts as $ac)
                                @if ($ac->costPrice != 0 || $ac->salePrice != 0)
                                    <tr style="background-color: #ebf1e8;">
                                        <td style="text-align: center;">{{ $ac->quantity }}</td>
                                        <td colspan="3">{{ $ac->name }}</td>
                                        <td style="text-align: center;">{{ $ac->currency}}</td>
                                        <td style="text-align: right;">{{ number_format($ac->costPrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($ac->quantity * $ac->costPrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($ac->quantity * $ac->costPrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($ac->salePrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($ac->quantity * $ac->salePrice, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($ac->quantity * $ac->salePrice * $general_rate_usd, 2, '.', '') }}</td>
                                        <td style="text-align: right;">{{ number_format($ac->quantity * ($ac->salePrice - $ac->costPrice) * $general_rate_usd, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($offer->extra_fee == true && $offer->extraFeeValue != 0)
                                <tr style="background-color: #ebf1e8;">
                                    <td style="text-align: center;">1</td>
                                    <td colspan="3">Extra fee for orders less than Euro 5000,00.</td>
                                    <td style="text-align: center;">{{ $offer->offer_currency}}</td>
                                    <td style="text-align: right;">{{ number_format(0, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format(0, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format(0, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format($offer->extraFeeValue, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format($offer->extraFeeValue, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format($offer->extraFeeValue * $general_rate_usd, 2, '.', '') }}</td>
                                    <td style="text-align: right;">{{ number_format($offer->extraFeeValue * $general_rate_usd, 2, '.', '') }}</td>
                                </tr>
                            @endif
                            <tr style="background-color: #ebf1e8;">
                                <td></td>
                                <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">{{ number_format($offer->offerAdditionalCostsTotalCostUSD, 2, '.', '') }}</td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">{{ ($offer->extra_fee == true && $offer->extraFeeValue != 0) ? number_format($offer->offerAdditionalCostsTotalSaleUSD + ($offer->extraFeeValue * $general_rate_usd), 2, '.', '') : number_format($offer->offerAdditionalCostsTotalSaleUSD, 2, '.', '') }}</td>
                                <td style="text-align: right;">{{ ($offer->extra_fee == true && $offer->extraFeeValue != 0) ? number_format(($offer->offerAdditionalCostsTotalSaleUSD + ($offer->extraFeeValue * $general_rate_usd) - $offer->offerAdditionalCostsTotalCostUSD), 2, '.', '') : number_format(($offer->offerAdditionalCostsTotalSaleUSD - $offer->offerAdditionalCostsTotalCostUSD), 2, '.', '') }}</td>
                            </tr>
                            <tr><td colspan="12"></td></tr>
                        @endif
                        <tr style="background-color: #ebf1e8;">
                            <td></td>
                            <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">{{ number_format($offer->offerTotalCostPriceUSD, 2, '.', '') }}</td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">{{ number_format($offer->offerTotalSalePriceUSD, 2, '.', '') }}</td>
                            <td style="text-align: right;">{{ number_format(($offer->offerTotalSalePriceUSD - $offer->offerTotalCostPriceUSD), 2, '.', '') }}</td>
                        </tr>
                        <tr style="background-color: #ebf1e8;">
                            <td></td>
                            <td colspan="10" style="text-align: right; font-weight: bold;">Percent of the profit based on the total cost price:</td>
                            <td style="text-align: right;">{{ number_format($offer->percent_profit, 2, '.', '') }} %</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </body>
</html>
