<table>
    <thead>
      <tr>
          <th>Order No.</th>
          <th>Quantities</th>
          <th>Species</th>
          <th>Provider</th>
          <th>Client</th>
          <th>Curr</th>
          <th>Bank account</th>
          <th>Order status</th>
          <th>Total SP amount</th>
          <th>Total SP in USD</th>
          <th>Total CP amount</th>
          <th>Total CP in USD</th>
          <th>Profit in USD</th>
          <th>Profit in EUR</th>
          <th>Internal remarks</th>
      </tr>
    </thead>
    <tbody>
        @php
            $totalProfitUsdByMonth = 0;
            $offerTotalCostPriceUsdAll = 0;
            $offerTotalSalePriceUsdAll = 0;
            $totalProfitUsdAll = 0;
        @endphp
        @foreach($orders as $ordersByMonth)
            @foreach ($ordersByMonth as $order)
                @foreach ($order as $sub_order)
                    @php
                        $sub_order->offer = App\Services\OfferService::calculate_offer_totals($sub_order->offer->id);
                    @endphp
                    <tr>
                        <td style="text-align: center;">
                            {{ $sub_order->full_number }}<br>
                            {{ date('j/M', strtotime($sub_order->created_at)) }}
                        </td>
                        <td style="text-align: center;">
                            @foreach ($sub_order->offer->species_ordered as $species)
                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($sub_order->offer->species_ordered as $species)
                                @if ($species->oursurplus && $species->oursurplus->animal)
                                    {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                @else
                                    ERROR - NO STANDARD SURPLUS
                                @endif
                            @endforeach
                        </td>
                        <td>
                            {{ $sub_order->supplier->full_name }}<br>
                            ({{ $sub_order->supplier->email }})
                        </td>
                        <td>
                            {{ $sub_order->client->full_name }}<br>
                            ({{ $sub_order->client->email }})
                        </td>
                        <td style="text-align: center;">{{ $sub_order->sale_currency }}</td>
                        <td>{{ $sub_order->bank_account->company_name }} {{ $sub_order->bank_account->name }}</td>
                        <td>{{ $sub_order->order_status }}</td>
                        <td style="text-align: right;">{{ number_format($sub_order->offer->offerTotalSalePrice, 2, '.', '') }}</td>
                        <td style="text-align: right;">
                            {{ number_format($sub_order->offer->offerTotalSalePriceUSD, 2, '.', '') }}
                            @php
                                $offerTotalSalePriceUsdAll += $sub_order->offer->offerTotalSalePriceUSD;
                            @endphp
                        </td>
                        <td style="text-align: right;">{{ number_format($sub_order->offer->offerTotalCostPrice, 2, '.', '') }}</td>
                        <td style="text-align: right;">
                            {{ number_format($sub_order->offer->offerTotalCostPriceUSD, 2, '.', '') }}
                            @php
                                $offerTotalCostPriceUsdAll += $sub_order->offer->offerTotalCostPriceUSD;
                            @endphp
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($sub_order->offer->offerTotalSalePriceUSD - $sub_order->offer->offerTotalCostPriceUSD, 2, '.', '') }}
                            @php
                                $totalProfitUsdByMonth += ($sub_order->offer->offerTotalSalePriceUSD - $sub_order->offer->offerTotalCostPriceUSD);
                            @endphp
                        </td>
                        <td style="text-align: right;">&nbsp;</td>
                        <td>{{ $sub_order->order_remarks }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: lightblue;">{{ date('M/Y', strtotime($sub_order->created_at)) }}</td>
                    <td colspan="11" style="background-color: lightblue;">&nbsp;</td>
                    <td style="text-align: right; font-weight: bold; background-color: skyblue;">
                        {{ number_format($totalProfitUsdByMonth, 2, '.', '') }}
                        @php
                            $totalProfitUsdAll += $totalProfitUsdByMonth;
                        @endphp
                    </td>
                    <td style="background-color: lightblue;">&nbsp;</td>
                    <td style="background-color: lightblue;">&nbsp;</td>
                </tr>
            @endforeach
        @endforeach
        <tr><td colspan="15">&nbsp;</td></tr>
        <tr>
            <td style="text-align: center; font-weight: bold;">TOTALS:</td>
            <td colspan="8">&nbsp;</td>
            <td style="text-align: right; font-weight: bold;">
                {{ number_format($offerTotalSalePriceUsdAll, 2, '.', '') }}
            </td>
            <td>&nbsp;</td>
            <td style="text-align: right; font-weight: bold;">
                {{ number_format($offerTotalCostPriceUsdAll, 2, '.', '') }}
            </td>
            <td style="text-align: right; font-weight: bold;">
                {{ number_format($totalProfitUsdAll, 2, '.', '') }}
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
</table>
