@if (count($species->species_airfreights) > 0)
    <table id="offerSpeciesAirfreightTable" class="table table-sm" style="table-layout: unset;" width="100%" cellspacing="0">
        <thead>
            <tr class="table-success text-center">
                <th colspan="3" style="width: 325px;">AIRFREIGHTS PER VOL KG</th>
                <th style="width: 15px;"></th>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <th colspan="3" style="width: 255px;">COST PRICES</th>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <th colspan="3" style="width: 255px;">SALE PRICES</th>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <th>PROFIT</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr class="table-active">
                <td class="text-center" style="width: 50px;">Qty</td>
                <td class="text-center" style="width: 190px;">From-To</td>
                <td class="text-center" style="width: 85px;">Vol. weight</td>
                <td style="width: 15px;"></td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td style="width: 85px;">Price vol.kg</td>
                    <td style="width: 85px;">Price total</td>
                    <td style="width: 85px;">Price USD</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td style="width: 85px;">Price vol.kg</td>
                    <td style="width: 85px;">Price total</td>
                    <td style="width: 85px;">Price USD</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td>In USD</td>
                @endif
            </tr>
            @foreach ($species->species_airfreights as $specie_airfreight)
            <tr>
                <td></td>
                <td class="text-center">{{$specie_airfreight->airfreight->origin_country->name}} - {{$specie_airfreight->airfreight->delivery_country->name}}</td>
                <td></td>
                <td></td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td><input type="text" class="input-group input-group-sm" name="cost_volKg" specieAirfreightId="{{$specie_airfreight->id}}" value="{{ number_format($specie_airfreight->cost_volKg, 2, '.', '') }}" oldValue="{{ number_format($specie_airfreight->cost_volKg, 2, '.', '') }}"></td>
                    <td></td>
                    <td></td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td><input type="text" class="input-group input-group-sm" name="sale_volKg" specieAirfreightId="{{$specie_airfreight->id}}" value="{{ number_format($specie_airfreight->sale_volKg, 2, '.', '') }}" oldValue="{{ number_format($specie_airfreight->sale_volKg, 2, '.', '') }}"></td>
                    <td></td>
                    <td></td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td class="text-center"></td>
                @endif
            </tr>
            @endforeach
            <tr>
                <td class="text-center">{{ $species->offerQuantityM }}</td>
                <td></td>
                <td class="text-center">{{ number_format($species->species_crate->m_volKg, 2, '.', '') }}</td>
                <td>{{ $species->oursurplus->sale_currency }}</td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td>{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->m_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->m_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td>{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->m_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->m_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td class="text-center">{{ number_format($species->species_crate->m_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                @endif
            </tr>
            <tr>
                <td class="text-center">{{ $species->offerQuantityF }}</td>
                <td></td>
                <td class="text-center">{{ number_format($species->species_crate->f_volKg, 2, '.', '') }}</td>
                <td>{{ $species->oursurplus->sale_currency }}</td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td>{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->f_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->f_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td>{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->f_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->f_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td class="text-center">{{ number_format($species->species_crate->f_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                @endif
            </tr>
            <tr>
                <td class="text-center">{{ $species->offerQuantityU }}</td>
                <td></td>
                <td class="text-center">{{ number_format($species->species_crate->u_volKg, 2, '.', '') }}</td>
                <td>{{ $species->oursurplus->sale_currency }}</td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td>{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->u_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->u_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td>{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->u_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->u_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td class="text-center">{{ number_format($species->species_crate->u_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                @endif
            </tr>
            @if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerSalePriceP > 0)
                <tr>
                    <td class="text-center">{{ $species->offerQuantityM }}</td>
                    <td></td>
                    <td class="text-center">{{ number_format($species->species_crate->p_volKg, 2, '.', '') }}</td>
                    <td>{{ $species->oursurplus->sale_currency }}</td>
                    @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                        <td>{{ number_format($species->total_cost_volKg_value, 2, '.', '') }}</td>
                        <td>{{ number_format($species->species_crate->p_volKg * $species->total_cost_volKg_value, 2, '.', '') }}</td>
                        <td>{{ number_format($species->species_crate->p_volKg * $species->total_cost_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                    @endif
                    @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                        <td>{{ number_format($species->total_sale_volKg_value, 2, '.', '') }}</td>
                        <td>{{ number_format($species->species_crate->p_volKg * $species->total_sale_volKg_value, 2, '.', '') }}</td>
                        <td>{{ number_format($species->species_crate->p_volKg * $species->total_sale_volKg_value * $species->currency_rate, 2, '.', '') }}</td>
                    @endif
                    @if (Auth::user()->hasPermission('offers.see-profit-value'))
                        <td class="text-center">{{ number_format($species->species_crate->p_volKg * ($species->total_sale_volKg_value - $species->total_cost_volKg_value) * $species->currency_rate, 2, '.', '') }}</td>
                    @endif
                </tr>
            @endif
            <tr class="table-active">
                <td></td>
                <td class="font-weight-bold">TOTAL:</td>
                <td class="text-center">{{ number_format($species->total_volKg, 2, '.', '') }}</td>
                <td></td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td></td>
                    <td></td>
                    <td>{{ number_format($species->total_airfreight_cost_price_usd, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td></td>
                    <td></td>
                    <td>{{ number_format($species->total_airfreight_sale_price_usd, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td>{{ number_format($species->total_airfreight_sale_price_usd - $species->total_airfreight_cost_price_usd, 2, '.', '') }}</td>
                @endif
            </tr>
        </tbody>
    </table>
@endif
