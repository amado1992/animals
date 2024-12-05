<table>
    <thead>
        <tr>
            <th>Deal No.</th>
            <th>Status</th>
            <th>Quantity</th>
            <th>Species</th>
            <th>Client</th>
            <th>Curr</th>
            <th>
                Estimated<br>
                profit USD
            </th>
            <th>Internal remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($offers as $offersByMonth)
            @foreach ($offersByMonth as $offer)
                @foreach ($offer as $sub_offer)
                    @php
                        $sub_offer = App\Services\OfferService::calculate_offer_totals($sub_offer->id);
                    @endphp
                    <tr>
                        <td style="text-align: center;">
                            {{ $sub_offer->full_number }}<br>
                            {{ date('j/M', strtotime($sub_offer->created_at)) }}
                        </td>
                        <td>
                            {{ $sub_offer->offer_status }}
                        </td>
                        <td style="text-align: center;">
                            @foreach ($sub_offer->species_ordered as $species)
                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($sub_offer->species_ordered as $species)
                                @if ($species->oursurplus && $species->oursurplus->animal)
                                    {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                @else
                                    ERROR - NO STANDARD SURPLUS
                                @endif
                            @endforeach
                        </td>
                        <td>
                            {{ $sub_offer->client->full_name }}<br>
                            ({{ $sub_offer->client->email }})
                        </td>
                        <td style="text-align: center;">
                            {{ $sub_offer->offer_currency }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($sub_offer->offerTotalSalePriceUSD - $sub_offer->offerTotalCostPriceUSD, 2, '.', '') }}
                        </td>
                        <td>
                            {{ $sub_offer->remarks }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="8" style="font-weight: bold; background-color: lightblue;">{{ date('M/Y', strtotime($sub_offer->created_at)) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
