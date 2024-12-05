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
                <b>PROFORMA INVOICE FOR:</b><br>
                @if ($offer->client->organisation != null)
                    {!! $offer->client->organisation->institution_details !!}
                @endif
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <table style="table-layout: unset; margin-right: 5px;">
                    <thead>
                        <tr style="font-weight: bold; text-align: center; background-color: #4f6228; color: #ffffff;">
                            <th colspan="4">Quantity</th>
                            <th colspan="2">Details</th>
                            <th colspan="6">Prices/each</th>
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
                                {{ number_format($animal->offerSalePriceM, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->offerSalePriceF, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->offerSalePriceU, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->offerSalePriceP, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{!empty($animal->oursurplus) && $animal->oursurplus->sale_currency === "EUR" ? number_format(($animal->total_sale_price), 2, '.', '') : number_format(($animal->total_sale_price_usd), 2, '.', '')}}
                            </td>
                        </tr>
                    @endforeach
                        <tr><td colspan="12">&nbsp;</td></tr>
                        <tr style="font-weight: bold; background-color: #c4d79b;">
                            <td colspan="6" style="text-align: right;">TOTAL:</td>
                            <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                            <td colspan="5" style="text-align: center;">
                                {{ $offer->order->cost_currency !== 'USD' ? number_format($offer->offerTotalSpeciesSalePrice, 2, '.', '') : number_format($offer->offerTotalSpeciesSalePriceUSD, 2, '.', '')  }}
                            </td>
                        </tr>
                        <tr style="background-color: #c4d79b;">
                            <td colspan="12" style="text-align: center;">Unless otherwise mentioned, this amount is according our offer.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                @if ($order->bank_account->company_name == 'IZS-BV' && $order->sale_currency = 'EUR')
                    @foreach ($bankAccountsEurBV as $bankAccountEurBV)
                        @if ($loop->index  == 0)
                            <p>
                                <span style="font-weight: bold;">Beneficiary: </span>International Zoo Services/John Rens BV<br>
                                <span style="font-weight: bold;">Beneficiary address: </span><br>
                                {!! $bankAccountEurBV->company_address !!}
                            </p>
                        @endif
                        <p>
                            <span style="font-weight: bold;">Beneficiary bank: </span>{{$bankAccountEurBV->beneficiary_name}}<br>
                            <span style="font-weight: bold;">Beneficiary bank address: </span>{{$bankAccountEurBV->beneficiary_address}}<br>
                            <span style="font-weight: bold;">Beneficiary account number: </span>{{$bankAccountEurBV->beneficiary_account}}<br>
                            <span style="font-weight: bold;">IBAN Code: </span>{{$bankAccountEurBV->iban}}<br>
                            <span style="font-weight: bold;">Swift: </span>{{$bankAccountEurBV->beneficiary_swift}}
                        </p>
                    @endforeach
                @else
                    <p>
                        <span style="font-weight: bold;">Beneficiary: </span>
                        @switch($order->bank_account->company_name)
                            @case('IZS-BV')
                                International Zoo Services/John Rens BV
                                @break
                            @case('IZS-Inc')
                                International Zoo Services/John Rens INC
                                @break
                            @default
                                International Zoo Services/John Rens
                        @endswitch
                        <br>
                        <span style="font-weight: bold;">Beneficiary address: </span><br>
                        {!! $order->bank_account->company_address !!}
                    </p>
                    <p>
                        <span style="font-weight: bold;">Beneficiary bank: </span>{{$order->bank_account->beneficiary_name}}<br>
                        <span style="font-weight: bold;">Beneficiary bank address: </span>{{$order->bank_account->beneficiary_address}}<br>
                        <span style="font-weight: bold;">Beneficiary account number: </span>{{$order->bank_account->beneficiary_account}}<br>
                        <span style="font-weight: bold;">IBAN Code: </span>{{$order->bank_account->iban}}<br>
                        <span style="font-weight: bold;">Swift: </span>{{$order->bank_account->beneficiary_swift}}
                    </p>
                @endif
            </div>
        </div>
    </body>
</html>
