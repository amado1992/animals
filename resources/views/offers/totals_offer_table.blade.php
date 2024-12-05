<table id="orderTotals" class="table table-striped table-sm mb-0">
    <thead>
        <tr>
            <th></th>
            <th>Cost prices made in {{ $offer->order->cost_currency }}</th>
            @if ($offer->order->cost_currency !== 'USD')
                <th>Converted cost prices USD</th>
            @endif
            <th>Sales prices made in {{ $offer->order->sale_currency }}</th>
            @if ($offer->order->sale_currency !== 'USD')
                <th>Converted sales prices USD</th>
            @endif
            <th>Expected profit USD</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Species:</td>
            <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalSpeciesCostPrice, 2, '.', '') : number_format($offer->offerTotalSpeciesCostPriceUSD, 2, '.', '') }}</td>
            @if ($offer->order->cost_currency !== 'USD')
                <td>{{ number_format($offer->offerTotalSpeciesCostPriceUSD, 2, '.', '') }}</td>
            @endif
            <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerTotalSpeciesSalePrice, 2, '.', '') : number_format($offer->offerTotalSpeciesSalePriceUSD, 2, '.', '') }}</td>
            @if ($offer->order->sale_currency !== 'USD')
                <td>{{ number_format($offer->offerTotalSpeciesSalePriceUSD, 2, '.', '') }}</td>
            @endif
            <td>{{ number_format($offer->offerTotalSpeciesSalePriceUSD - $offer->offerTotalSpeciesCostPriceUSD, 2, '.', '') }}</td>
        </tr>
        @if ($offer->order->sale_price_type != "ExZoo")
            <tr>
                <td>Crates:</td>
                <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalCratesCostPrice, 2, '.', '') : number_format($offer->offerTotalCratesCostPriceUSD, 2, '.', '') }}</td>
                @if ($offer->order->cost_currency !== 'USD')
                    <td>{{ number_format($offer->offerTotalCratesCostPriceUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerTotalCratesSalePrice, 2, '.', '') : number_format($offer->offerTotalCratesSalePriceUSD, 2, '.', '') }}</td>
                @if ($offer->order->sale_currency !== 'USD')
                    <td>{{ number_format($offer->offerTotalCratesSalePriceUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ number_format($offer->offerTotalCratesSalePriceUSD - $offer->offerTotalCratesCostPriceUSD, 2, '.', '') }}</td>
            </tr>
            <tr>
                @if ($offer->airfreight_type == "pallets")
                    <td>Airfreights by pallets:</td>
                    <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalAirfreightPalletCostPrice, 2, '.', '') : number_format($offer->offerTotalAirfreightPalletCostPriceUSD, 2, '.', '') }}</td>
                    @if ($offer->order->cost_currency !== 'USD')
                        <td>{{ number_format($offer->offerTotalAirfreightPalletCostPriceUSD, 2, '.', '') }}</td>
                    @endif
                    <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerTotalAirfreightPalletSalePrice, 2, '.', '') : number_format($offer->offerTotalAirfreightPalletSalePriceUSD, 2, '.', '') }}</td>
                    @if ($offer->order->sale_currency !== 'USD')
                        <td>{{ number_format($offer->offerTotalAirfreightPalletSalePriceUSD, 2, '.', '') }}</td>
                    @endif
                    <td>{{ number_format($offer->offerTotalAirfreightPalletSalePriceUSD - $offer->offerTotalAirfreightPalletCostPriceUSD, 2, '.', '') }}</td>
                @elseif ($offer->airfreight_type == "byTruck")
                    <td>Transport by truck:</td>
                    <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalTransportTruckCostPrice, 2, '.', '') : number_format($offer->offerTotalTransportTruckCostPriceUSD, 2, '.', '') }}</td>
                    @if ($offer->order->cost_currency !== 'USD')
                        <td>{{ number_format($offer->offerTotalTransportTruckCostPriceUSD, 2, '.', '') }}</td>
                    @endif
                    <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerTotalAirfreightPalletSalePrice, 2, '.', '') : number_format($offer->offerTotalAirfreightPalletSalePriceUSD, 2, '.', '') }}</td>
                    @if ($offer->order->sale_currency !== 'USD')
                        <td>{{ number_format($offer->offerTotalAirfreightPalletSalePriceUSD, 2, '.', '') }}</td>
                    @endif
                    <td>{{ number_format($offer->offerTotalAirfreightPalletSalePriceUSD - $offer->offerTotalTransportTruckCostPriceUSD, 2, '.', '') }}</td>
                @else
                    <td>Airfreights:</td>
                    <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalAirfreightsCostPrice, 2, '.', '') : number_format($offer->offerTotalAirfreightsCostPriceUSD, 2, '.', '') }}</td>
                    @if ($offer->order->cost_currency !== 'USD')
                        <td>{{ number_format($offer->offerTotalAirfreightsCostPriceUSD, 2, '.', '') }}</td>
                    @endif
                    <td>{{ $offer->order->sale_currency !== 'USD' ?  number_format($offer->offerTotalAirfreightsSalePrice, 2, '.', '') : number_format($offer->offerTotalAirfreightsSalePriceUSD, 2, '.', '') }}</td>
                    @if ($offer->order->sale_currency !== 'USD')
                        <td>{{ number_format($offer->offerTotalAirfreightsSalePriceUSD, 2, '.', '') }}</td>
                    @endif
                    <td>{{ number_format($offer->offerTotalAirfreightsSalePriceUSD - $offer->offerTotalAirfreightsCostPriceUSD, 2, '.', '') }}</td>
                @endif
            </tr>
            <tr>
                <td>Tests & Quarantine:</td>
                <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerAdditionalTestsTotalCost, 2, '.', '') : number_format($offer->offerAdditionalTestsTotalCostUSD, 2, '.', '') }}</td>
                @if ($offer->order->cost_currency !== 'USD')
                    <td>{{ number_format($offer->offerAdditionalTestsTotalCostUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerAdditionalTestsTotalSale, 2, '.', '') : number_format($offer->offerAdditionalTestsTotalSaleUSD, 2, '.', '') }}</td>
                @if ($offer->order->sale_currency !== 'USD')
                    <td>{{ number_format($offer->offerAdditionalTestsTotalSaleUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ number_format($offer->offerAdditionalTestsTotalSaleUSD - $offer->offerAdditionalTestsTotalCostUSD, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td>Basic costs:</td>
                <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerAdditionalCostsTotalCost, 2, '.', '') : number_format($offer->offerAdditionalCostsTotalCostUSD, 2, '.', '') }}</td>
                @if ($offer->order->cost_currency !== 'USD')
                    <td>{{ number_format($offer->offerAdditionalCostsTotalCostUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerAdditionalCostsTotalSale, 2, '.', '') : number_format($offer->offerAdditionalCostsTotalSaleUSD, 2, '.', '') }}</td>
                @if ($offer->order->sale_currency !== 'USD')
                    <td>{{ number_format($offer->offerAdditionalCostsTotalSaleUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ number_format($offer->offerAdditionalCostsTotalSaleUSD - $offer->offerAdditionalCostsTotalCostUSD, 2, '.', '') }}</td>
            </tr>
        @endif
        @if ($offer->extra_fee)
            <tr>
                <td>Extra fee:</td>
                <td>{{ number_format(0, 2, '.', '') }}</td>
                @if ($offer->order->cost_currency !== 'USD')
                    <td>{{ number_format(0, 2, '.', '') }}</td>
                @endif
                <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->extraFeeValue, 2, '.', '') : number_format($offer->extraFeeValueUSD, 2, '.', '') }}</td>
                @if ($offer->order->sale_currency !== 'USD')
                    <td>{{ number_format($offer->extraFeeValueUSD, 2, '.', '') }}</td>
                @endif
                <td>{{ number_format($offer->extraFeeValueUSD, 2, '.', '') }}</td>
            </tr>
        @endif
        <tr class="font-weight-bold">
            <td>TOTAL:</td>
            <td>{{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalCostPrice, 2, '.', '') : number_format($offer->offerTotalCostPriceUSD, 2, '.', '') }}</td>
            @if ($offer->order->cost_currency !== 'USD')
                <td>{{ number_format($offer->offerTotalCostPriceUSD, 2, '.', '') }}</td>
            @endif
            <td>{{ $offer->order->sale_currency !== 'USD' ? number_format($offer->offerTotalSalePrice, 2, '.', '') : number_format($offer->offerTotalSalePriceUSD, 2, '.', '') }}</td>
            @if ($offer->order->sale_currency !== 'USD')
                <td>{{ number_format($offer->offerTotalSalePriceUSD, 2, '.', '') }}</td>
            @endif
            <td>{{ number_format($offer->offerTotalSalePriceUSD - $offer->offerTotalCostPriceUSD, 2, '.', '') }}</td>
        </tr>
    </tbody>
</table>
