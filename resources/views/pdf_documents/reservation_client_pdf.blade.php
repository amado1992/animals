<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Zoo Services">

        <style>
            @page {
                margin: 15px;
            }

            .pdf-container {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        <div class="pdf-container">
            <div class="row">
                <div class="col-md-12">
                    <table class="table" cellspacing="0">
                        <tbody>
                            <tr>
                                <td style="vertical-align: top;">
                                    <img src="{{asset('img/logo.jpg')}}" alt="" style="width: 70px;">
                                </td>
                                <td style="vertical-align: top;">
                                    @switch($order->bank_account->company_name)
                                        @case('IZS-BV')
                                            <b>INTERNATIONAL ZOO SERVICES BV</b><br>
                                            @break
                                        @case('IZS-Inc')
                                            <b>INTERNATIONAL ZOO SERVICES INC</b><br>
                                            @break
                                        @default
                                            <b>INTERNATIONAL ZOO SERVICES</b><br>
                                    @endswitch
                                    {{Str::of($order->bank_account->company_address)->replace('<br>', ', ')}}<br>
                                    {{$order->bank_account->beneficiary_address}}<br>
                                    @if ($order->bank_account->company_name == 'IZS-BV')
                                        VAT: NL800799227B02
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <table width="100%" cellspacing="0">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;">Order number: {{ $order->full_number }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ \Carbon\Carbon::now()->format('F j, Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <b>RESERVATION FOR:</b><br>
                @if ($offer->client->organisation != null)
                    {!! $offer->client->organisation->institution_details !!}
                @endif
                {{ $order->client->full_name }},<br>
                {{ $order->client->email }}
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                Dear {{ $order->client->letter_name }},<br><br>
                Hereby we have pleasure in confirming the reservation of the following specimen(s):
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <table style="table-layout: unset; margin-right: 5px;">
                    <thead>
                        <tr style="font-weight: bold; text-align: center; background-color: #4f6228; color: #ffffff;">
                            <th colspan="4">Quantity</th>
                            <th colspan="2">Details</th>
                            <th colspan="7">Prices/each</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr style="font-weight: bold; text-align: center; background-color: #76933c; color: #ffffff;">
                            <td style="width: 18px;">M</td>
                            <td style="width: 18px;">F</td>
                            <td style="width: 18px;">U</td>
                            <td style="width: 18px;">P</td>
                            <td style="width: 130px;">Scientific name</td>
                            <td style="width: 130px;">Remarks</td>
                            <td style="width: 20px;">&nbsp;</td>
                            <td style="width: 60px;">M</td>
                            <td style="width: 60px;">F</td>
                            <td style="width: 60px;">U</td>
                            <td style="width: 60px;">Pair</td>
                            <td style="width: 60px;">Subtotal</td>
                            <td style="width: 75px;">&nbsp;</td>
                        </tr>

                    @foreach ($offer->species_ordered as $animal)
                        @if ($loop->index % 2 == 0)
                            @php $bkgColor = "#ebf1e8"; @endphp
                        @else
                            @php $bkgColor = "#c4d79b"; @endphp
                        @endif
                        <tr style="vertical-align: top; background-color: {{ $bkgColor }};">
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityM }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityF }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityU }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityP }}
                            </td>
                            <td style="word-wrap: break-word; min-width: 130px;max-width: 130px; white-space:normal;">
                                @if ($animal->oursurplus && $animal->oursurplus->animal)
                                    {{ $animal->oursurplus->animal->scientific_name}}<br>
                                    ({{ $animal->oursurplus->animal->common_name}})
                                @else
                                    ERROR - NO STANDARD SURPLUS
                                @endif
                            </td>
                            <td>
                                {{ $animal->surplusDetails }}
                            </td>
                            <td style="text-align: center;">
                                <label>{{ $animal->oursurplus->sale_currency }}</label>
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePriceMPdf }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePriceFPdf }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePriceUPdf }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePricePPdf }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->subtotal_sale_price, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                @switch($order->sale_price_type)
                                    @case("ExZoo")
                                        Ex zoological institution
                                        @break
                                    @case("CF")
                                        C+F {{$order->delivery_airport->city}}, {{$order->delivery_country->name}}
                                        @break
                                    @default
                                        {{$order->sale_price_type}} {{$order->delivery_airport->city}}, {{$order->delivery_country->name}}
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                        @if ($order->sale_price_type != "ExZoo")
                            <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                <td colspan="6">
                                    @if ((float) $offer->offerAdditionalCostsTotalSale > 0)
                                        *Additional basic costs for each shipment (regardless quantity of animals)
                                    @endif
                                </td>
                                <td></td>
                                <td colspan="6">
                                    @if ((float) $offer->offerAdditionalCostsTotalSale > 0)
                                        Price
                                    @endif
                                </td>
                            </tr>
                            @if($offer->airfreight_type == "pallets")
                                <tr style="background-color: #ebf1e8;">
                                    <td colspan="6">Transport by pallet:</td>
                                    <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                                    <td colspan="6" style="text-align: center;">{{ number_format($offer->offerTotalAirfreightPalletSalePrice, 2, '.', '') }}</td>
                                </tr>
                            @elseif($offer->airfreight_type == "byTruck")
                                <tr style="background-color: #ebf1e8;">
                                    <td colspan="6">Transport by truck:</td>
                                    <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                                    <td colspan="6" style="text-align: center;">{{ number_format($offer->offerTotalTransportTruckSalePrice, 2, '.', '') }}</td>
                                </tr>
                            @endif
                            @if ($offer->additional_costs()->where('is_test', 0)->where('quantity', '<>', 0)->where('salePrice', '<>', 0)->get()->count() > 0 || $offer->additional_costs()->where('is_test', 1)->where('quantity', '<>', 0)->where('salePrice', '<>', 0)->get()->count() > 0)
                                @foreach ($offer->additional_costs()->where('is_test', 0)->where('quantity', '<>', 0)->get() as $additionalCost)
                                    @if ($additionalCost->salePrice != 0)
                                        <tr style="background-color: #ebf1e8;">
                                            <td colspan="6">{{ $additionalCost->name }}:</td>
                                            <td style="text-align: center;">{{ $additionalCost->currency }}</td>
                                            <td colspan="6" style="text-align: center;">{{ number_format($additionalCost->quantity * $additionalCost->salePrice, 2, '.', '') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @php
                                $tests_with_nonzero_qty = 0;
                                @endphp
                                @foreach ($offer->additional_costs()->where('is_test', 1)->get() as $additionalTest)
                                    @php
                                        if ($additionalTest->quantity > 0) {
                                            $tests_with_nonzero_qty++;
                                        }
                                    @endphp
                                    @if ($additionalTest->salePrice != 0)
                                        <tr style="background-color: #ebf1e8;">
                                            <td colspan="6">{{ $additionalTest->name }}:</td>
                                            <td style="text-align: center;">{{ $additionalTest->currency }}</td>
                                            <td colspan="6" style="text-align: center;">{{ number_format($additionalTest->quantity * $additionalTest->salePrice, 2, '.', '') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            <tr style="background-color: #c4d79b;">
                                <td colspan="13">
                                {{$tests_with_nonzero_qty ?? 0 > 0 ? 'Including' : 'Excluding'}} costs for tests,
                                quarantining and veterinary inspection.<br>
                                </td>
                            </tr>
                        @endif
                        @if ($offer->extra_fee == true && $offer->extraFeeValue != 0)
                            <tr style="background-color: #c4d79b;">
                                <td colspan="13">
                                    For orders less than Euro 5000,00; a fee of Euro 750,00 will be charged as administration costs.<br>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="row">
                @if ($reservationClientText)
                    {{$reservationClientText->english_text}}
                @endif
            </div>

            <div class="row">
                <p>
                    Looking forward to hearing from you.<br><br>
                    Kindest regards,<br><br>
                    INTERNATIONAL ZOO SERVICES<br>
                    <a href="https://www.zoo-services.com">www.zoo-services.com</a>
                </p>
            </div>
        </div>
    </body>
</html>
