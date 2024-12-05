<table id="offerSpeciesCrateTable" class="table table-sm" style="table-layout: unset;" width="100%" cellspacing="0">
    <thead>
        <tr class="table-success text-center">
            <th colspan="3" style="width: 325px;">CRATES</th>
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
            <td class="text-center" style="width: 165px;">Dimensions</td>
            <td class="text-center" style="width: 110px;">Vol. weight</td>
            <td style="width: 15px;"></td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td style="width: 85px;">Price each</td>
                <td style="width: 85px;">Price total</td>
                <td style="width: 85px;">Price USD</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td style="width: 85px;">Price each</td>
                <td style="width: 85px;">Price total</td>
                <td style="width: 85px;">Price USD</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td>In USD</td>
            @endif
        </tr>
        <tr>
            <td colspan="4">
                @if (Auth::user()->hasPermission('offers.update'))
                    <select style="width: 190px;" name="crateSelection" id="crateSelection">
                        <option @if(!isset($species->species_crate->crate_id)) selected @endif value="0">--Select crate--</option>
                        @foreach ($species->crates as $crate)
                            <option value="{{ $crate->id }}" @if(isset($species->species_crate->crate_id) && $crate->id == $species->species_crate->crate_id) selected @endif>{{ $crate->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="input-group input-group-sm d-inline-block" style="width: 35px;" name="length" value="{{ $species->species_crate->length }}" oldValue="{{ $species->species_crate->length }}"> x
                    <input type="text" class="input-group input-group-sm d-inline-block" style="width: 35px;" name="wide" value="{{ $species->species_crate->wide }}" oldValue="{{ $species->species_crate->wide }}"> x
                    <input type="text" class="input-group input-group-sm d-inline-block"  style="width: 35px;" name="height" value="{{ $species->species_crate->height }}" oldValue="{{ $species->species_crate->height }}"> (cm)
                @endif
            </td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td><input type="text" class="input-group input-group-sm" name="cost_price" value="{{ number_format($species->species_crate->cost_price, 2, '.', '') }}" oldValue="{{ number_format($species->species_crate->cost_price, 2, '.', '') }}"></td>
                <td></td>
                <td></td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td><input type="text" class="input-group input-group-sm" name="sale_price" value="{{ number_format($species->species_crate->sale_price, 2, '.', '') }}" oldValue="{{ number_format($species->species_crate->sale_price, 2, '.', '') }}"></td>
                <td></td>
                <td></td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center"></td>
            @endif
        </tr>
        <tr>
            <td><input type="text" class="input-group input-group-sm" name="quantity_males" value="{{ $species->species_crate->quantity_males }}" oldValue="{{ $species->species_crate->quantity_males }}"></td>
            <td class="text-center">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
            <td>{{ $species->species_crate->m_volKg }}</td>
            <td>{{ $species->oursurplus->sale_currency }}</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td>{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_males * $species->species_crate->cost_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_males * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td>{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_males * $species->species_crate->sale_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_males * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center">{{ number_format($species->species_crate->quantity_males * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
            @endif
        </tr>
        <tr>
            <td><input type="text" class="input-group input-group-sm" name="quantity_females" value="{{ $species->species_crate->quantity_females }}" oldValue="{{ $species->species_crate->quantity_females }}"></td>
            <td class="text-center">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
            <td>{{ $species->species_crate->f_volKg }}</td>
            <td>{{ $species->oursurplus->sale_currency }}</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td>{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_females * $species->species_crate->cost_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_females * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td>{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_females * $species->species_crate->sale_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_females * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center">{{ number_format($species->species_crate->quantity_females * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
            @endif
        </tr>
        <tr>
            <td><input type="text" class="input-group input-group-sm" name="quantity_unsexed" value="{{ $species->species_crate->quantity_unsexed }}" oldValue="{{ $species->species_crate->quantity_unsexed }}"></td>
            <td class="text-center">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
            <td>{{ $species->species_crate->u_volKg }}</td>
            <td>{{ $species->oursurplus->sale_currency }}</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td>{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->cost_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td>{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->sale_price, 2, '.', '') }}</td>
                <td>{{ number_format($species->species_crate->quantity_unsexed * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center">{{ number_format($species->species_crate->quantity_unsexed * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
            @endif
        </tr>
        @if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerSalePriceP > 0)
            <tr>
                <td><input type="text" class="input-group input-group-sm" name="quantity_pairs" value="{{ $species->species_crate->quantity_pairs }}" oldValue="{{ $species->species_crate->quantity_pairs }}"></td>
                <td class="text-center">{{ $species->species_crate->length }} x {{ $species->species_crate->wide }} x {{ $species->species_crate->height }} (cm)</td>
                <td>{{ $species->species_crate->p_volKg }}</td>
                <td>{{ $species->oursurplus->sale_currency }}</td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td>{{ number_format($species->species_crate->cost_price, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->cost_price, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->cost_price * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td>{{ number_format($species->species_crate->sale_price, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->sale_price, 2, '.', '') }}</td>
                    <td>{{ number_format($species->species_crate->quantity_pairs * $species->species_crate->sale_price * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td class="text-center">{{ number_format($species->species_crate->quantity_pairs * ($species->species_crate->sale_price - $species->species_crate->cost_price) * $species->currency_rate, 2, '.', '') }}</td>
                @endif
            </tr>
        @endif
        <tr class="table-active">
            <td></td>
            <td class="font-weight-bold">TOTAL:</td>
            <td>{{ number_format($species->total_volKg, 2, '.', '') }}</td>
            <td></td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td></td>
                <td></td>
                <td>{{ number_format($species->species_crate->total_cost_price_usd, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td></td>
                <td></td>
                <td>{{ number_format($species->species_crate->total_sale_price_usd, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td>{{ number_format(($species->species_crate->total_sale_price_usd - $species->species_crate->total_cost_price_usd), 2, '.', '') }}</td>
            @endif
        </tr>
    </tbody>
</table>
