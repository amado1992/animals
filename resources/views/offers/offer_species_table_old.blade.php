<table id="offerSpeciesTable" class="table table-sm" style="table-layout: unset;" width="100%" cellspacing="0">
    <thead>
        <tr class="table-success text-center">
            <th colspan="3" style="width: 310px;">INFO</th>
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
            <td class="text-center" style="width: 10px;">Sex</td>
            <td style="width: 250px;">&nbsp;</td>
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
            <td><input type="text" class="input-group input-group-sm" name="offerQuantityM" value="{{ $species->offerQuantityM }}" oldValue="{{ $species->offerQuantityM }}"></td>
            <td class="text-center">M</td>
            <td>&nbsp;</td>
            <td>{{ $species->oursurplus->sale_currency }}</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td><input type="text" class="input-group input-group-sm" name="offerCostPriceM" value="{{ number_format($species->offerCostPriceM, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceM, 2, '.', '') }}"></td>
                <td>{{ number_format($species->offerQuantityM * $species->offerCostPriceM, 2, '.', '') }}</td>
                <td>{{ number_format($species->offerQuantityM * $species->offerCostPriceM * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td><input type="text" class="input-group input-group-sm" name="offerSalePriceM" value="{{ number_format($species->offerSalePriceM, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceM, 2, '.', '') }}"></td>
                <td>{{ number_format($species->offerQuantityM * $species->offerSalePriceM, 2, '.', '') }}</td>
                <td>{{ number_format($species->offerQuantityM * $species->offerSalePriceM * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center">{{ number_format($species->offerQuantityM * ($species->offerSalePriceM - $species->offerCostPriceM) * $species->currency_rate, 2, '.', '') }}</td>
            @endif
        </tr>
        <tr>
            <td><input type="text" class="input-group input-group-sm" name="offerQuantityF" value="{{ $species->offerQuantityF }}" oldValue="{{ $species->offerQuantityF }}"></td>
            <td class="text-center">F</td>
            <td>&nbsp;</td>
            <td>{{ $species->oursurplus->sale_currency }}</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td><input type="text" class="input-group input-group-sm" name="offerCostPriceF" value="{{ number_format($species->offerCostPriceF, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceF, 2, '.', '') }}"></td>
                <td>{{ number_format($species->offerQuantityF * $species->offerCostPriceF, 2, '.', '') }}</td>
                <td>{{ number_format($species->offerQuantityF * $species->offerCostPriceF * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td><input type="text" class="input-group input-group-sm" name="offerSalePriceF" value="{{ number_format($species->offerSalePriceF, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceF, 2, '.', '') }}"></td>
                <td>{{ number_format($species->offerQuantityF * $species->offerSalePriceF, 2, '.', '') }}</td>
                <td>{{ number_format($species->offerQuantityF * $species->offerSalePriceF * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center">{{ number_format($species->offerQuantityF * ($species->offerSalePriceF - $species->offerCostPriceF) * $species->currency_rate, 2, '.', '') }}</td>
            @endif
        </tr>
        <tr>
            <td><input type="text" class="input-group input-group-sm" name="offerQuantityU" value="{{ $species->offerQuantityU }}" oldValue="{{ $species->offerQuantityU }}"></td>
            <td class="text-center">U</td>
            <td>&nbsp;</td>
            <td>{{ $species->oursurplus->sale_currency }}</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td><input type="text" class="input-group input-group-sm" name="offerCostPriceU" value="{{ number_format($species->offerCostPriceU, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceU, 2, '.', '') }}"></td>
                <td>{{ number_format($species->offerQuantityU * $species->offerCostPriceU, 2, '.', '') }}</td>
                <td>{{ number_format($species->offerQuantityU * $species->offerCostPriceU * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td><input type="text" class="input-group input-group-sm" name="offerSalePriceU" value="{{ number_format($species->offerSalePriceU, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceU, 2, '.', '') }}"></td>
                <td>{{ number_format($species->offerQuantityU * $species->offerSalePriceU, 2, '.', '') }}</td>
                <td>{{ number_format($species->offerQuantityU * $species->offerSalePriceU * $species->currency_rate, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td class="text-center">{{ number_format($species->offerQuantityU * ($species->offerSalePriceU - $species->offerCostPriceU) * $species->currency_rate, 2, '.', '') }}</td>
            @endif
        </tr>
        @if ($species->offerQuantityM > 0 && $species->offerQuantityM == $species->offerQuantityF && $species->offerSalePriceP > 0)
            <tr>
                <td>
                    <input type="text" class="input-group input-group-sm" name="offerQuantityP" value="{{ $species->offerQuantityM }}" oldValue="{{ $species->offerQuantityM }}">
                </td>
                <td class="text-center">P</td>
                <td>&nbsp;</td>
                <td>{{ $species->oursurplus->sale_currency }}</td>
                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                    <td><input type="text" class="input-group input-group-sm" name="offerCostPriceP" value="{{ number_format($species->offerCostPriceP, 2, '.', '') }}" oldValue="{{ number_format($species->offerCostPriceP, 2, '.', '') }}"></td>
                    <td>{{ number_format($species->offerQuantityM * $species->offerCostPriceP , 2, '.', '') }}</td>
                    <td>{{ number_format($species->offerQuantityM * $species->offerCostPriceP * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                    <td><input type="text" class="input-group input-group-sm" name="offerSalePriceP" value="{{ number_format($species->offerSalePriceP, 2, '.', '') }}" oldValue="{{ number_format($species->offerSalePriceP, 2, '.', '') }}"></td>
                    <td>{{ number_format($species->offerQuantityM * $species->offerSalePriceP, 2, '.', '') }}</td>
                    <td>{{ number_format($species->offerQuantityM * $species->offerSalePriceP * $species->currency_rate, 2, '.', '') }}</td>
                @endif
                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                    <td class="text-center">{{ number_format($species->offerQuantityM * ($species->offerSalePriceP - $species->offerCostPriceP) * $species->currency_rate, 2, '.', '') }}</td>
                @endif
            </tr>
        @endif
        <tr class="table-active">
            <td></td>
            <td colspan="3" class="font-weight-bold">TOTAL:</td>
            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                <td></td>
                <td></td>
                <td>{{ number_format($species->total_cost_price_usd, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                <td></td>
                <td></td>
                <td>{{ number_format($species->total_sale_price_usd, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                <td>{{ number_format(($species->total_sale_price_usd- $species->total_cost_price_usd), 2, '.', '') }}</td>
            @endif
        </tr>
    </tbody>
</table>
