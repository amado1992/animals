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
                @if ($order->supplier)
                    @if ($offer->supplier->organisation != null)
                        {!! $offer->supplier->organisation->institution_details !!}
                    @endif
                    {{ $order->supplier->full_name }},<br>
                    {{ $order->supplier->email }}
                @else
                    International Zoo Services,<br>
                    izs@zoo-services.com
                @endif
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                Dear {{ ($order->supplier) ? $order->supplier->letter_name : 'Mr./Mrs.' }},<br><br>
                Hereby we have pleasure in confirming the reservation of the following specimen(s):
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
                            <td style="width: 140px;">Scientific name</td>
                            <td style="width: 140px;">Remarks</td>
                            <td style="width: 20px;">&nbsp;</td>
                            <td style="width: 65px;">M</td>
                            <td style="width: 65px;">F</td>
                            <td style="width: 65px;">U</td>
                            <td style="width: 65px;">Pair</td>
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
                            <td style="word-wrap: break-word; min-width: 140px;max-width: 140px; white-space:normal;">
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
                                {{ number_format($animal->offerCostPriceM, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->offerCostPriceF, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->offerCostPriceU, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                {{ number_format($animal->offerCostPriceP, 2, '.', '') }}
                            </td>
                            <td style="text-align: center;">
                                Ex zoological institution
                            </td>
                        </tr>
                    @endforeach
                        <tr style="background-color: #c4d79b;">
                            <td colspan="12">
                                Excluding costs for tests, quarantining and veterinary inspection.<br>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                @if ($reservationSupplierText)
                    {{$reservationSupplierText->english_text}}
                @endif
            </div>

            <div class="row">
                <p>
                    Your cooperation is much appreciated,<br><br>
                    Kindest regards,<br><br>
                    INTERNATIONAL ZOO SERVICES<br>
                    <a href="https://www.zoo-services.com">www.zoo-services.com</a>
                </p>
            </div>
        </div>
    </body>
</html>
