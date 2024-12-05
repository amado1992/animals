<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Zoo Services">

        <style>
            @page {
                margin: 20px;
            }

            .pdf-container {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        <div class="pdf-container">
            @include('pdf_documents.header_pdf')

            <div class="row">&nbsp;</div>

            <div class="row">
                <table width="100%" cellspacing="0">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;">Offer number: {{ $offer->full_number }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $dateOfToday }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right; font-weight: bold;">1 EUR = {{ $rates->first() ? number_format($rates->first()->EUR_USD, 2, '.', '') : 'No rate' }} USD / 1 USD = {{ $rates->first() ? number_format($rates->first()->USD_EUR, 2, '.', '') : 'No rate' }} EUR</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                @if ($offer->client)
                    {!! $offer->client->organisation->institution_details !!}
                    {{ $offer->client->email }}
                @else
                    @if ($offer->organisation !== null)
                        {!! $offer->organisation->institution_details !!}
                        {{ $offer->organisation->email }}
                    @endif
                @endif
            </div>

            <div class="row">&nbsp;</div>

            @if ($offer->client)
            <div class="row">
                Dear {{ $offer->client->letter_name }},<br><br>
                We have pleasure offering you the following species:
            </div>
            @endif

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
                                {{ $animal->surplusDetails }}<br>
                                {{ $animal->locatedAt }}
                            </td>
                            <td style="text-align: center;">
                                <label>{{ $animal->oursurplus->sale_currency }}</label>
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePriceMPdf }}
                                @if (trim($animal->offerSalePriceMPdf) != '')
                                    @if ($animal->oursurplus->sale_currency != 'USD')
                                        <br>(+/- USD {{ $animal->offerSalePriceMPdfUsd }})
                                    @else
                                        <br>(+/- EUR {{ number_format($animal->offerSalePriceMPdf * $rates->first()->USD_EUR, 2, '.', '') }})
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePriceFPdf }}
                                @if (trim($animal->offerSalePriceFPdf) != '')
                                    @if ($animal->oursurplus->sale_currency != 'USD')
                                        <br>(+/- USD {{ $animal->offerSalePriceFPdfUsd }})
                                    @else
                                        <br>(+/- EUR {{ number_format($animal->offerSalePriceFPdf * $rates->first()->USD_EUR, 2, '.', '') }})
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePriceUPdf }}
                                @if (trim($animal->offerSalePriceUPdf) != '')
                                    @if ($animal->oursurplus->sale_currency != 'USD')
                                        <br>(+/- USD {{ $animal->offerSalePriceUPdfUsd }})
                                    @else
                                        <br>(+/- EUR {{ number_format($animal->offerSalePriceUPdf * $rates->first()->USD_EUR, 2, '.', '') }})
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerSalePricePPdf }}
                                @if (trim($animal->offerSalePricePPdf) != '')
                                    @if ($animal->oursurplus->sale_currency != 'USD')
                                        <br>(+/- USD {{ $animal->offerSalePricePPdfUsd }})
                                    @else
                                        <br>(+/- EUR {{ number_format($animal->offerSalePricePPdf * $rates->first()->USD_EUR, 2, '.', '') }})
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @switch($offer->sale_price_type)
                                    @case("ExZoo")
                                        Ex zoological institution
                                        @break
                                    @case("CF")
                                        C+F {{$offer->delivery_airport->city}}, {{$offer->delivery_country->name}}
                                        @break
                                    @default
                                        {{$offer->sale_price_type}} {{$offer->delivery_airport->city}}, {{$offer->delivery_country->name}}
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                        @if ($offer->sale_price_type != "ExZoo")
                            <tr style="text-align: center; font-weight: bold; background-color: #76933c; color: #ffffff;">
                                <td colspan="6">
                                @if ((float) $offer->offerAdditionalCostsTotalSale > 0)
                                    *Additional basic costs for each shipment (regardless quantity of animals)
                                @endif
                                </td>
                                <td></td>
                                <td colspan="5">
                                    @if ((float) $offer->offerAdditionalCostsTotalSale > 0)
                                        Price
                                    @endif
                                </td>
                            </tr>
                            @if($offer->airfreight_type == "pallets")
                                <tr style="background-color: #ebf1e8;">
                                    <td colspan="6">Transport by pallet:</td>
                                    <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                                    <td colspan="5" style="text-align: center;">{{ number_format($offer->offerTotalAirfreightPalletSalePrice, 2, '.', '') }}</td>
                                </tr>
                            @elseif($offer->airfreight_type == "byTruck")
                                <tr style="background-color: #ebf1e8;">
                                    <td colspan="6">Transport by truck:</td>
                                    <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                                    <td colspan="5" style="text-align: center;">{{ number_format($offer->offerTotalTransportTruckSalePrice, 2, '.', '') }}</td>
                                </tr>
                            @endif
                            @if ($offer->additional_costs()->where('is_test', 0)->where('quantity', '<>', 0)->where('salePrice', '<>', 0)->get()->count() > 0 || $offer->additional_costs()->where('is_test', 1)->where('quantity', '<>', 0)->where('salePrice', '<>', 0)->get()->count() > 0)
                                @foreach ($offer->additional_costs()->where('is_test', 0)->get() as $additionalCost)
                                    @if ($additionalCost->salePrice != 0 && $additionalCost->quantity !== 0)
                                        <tr style="background-color: #ebf1e8;">
                                            <td colspan="6">{{ $additionalCost->name }}:</td>
                                            <td style="text-align: center;">{{ $additionalCost->currency }}</td>
                                            <td colspan="5" style="text-align: center;">{{ number_format(abs($additionalCost->quantity * $additionalCost->salePrice), 2, '.', '') }}</td>
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
                                            <td colspan="5" style="text-align: center;">{{ number_format(abs($additionalTest->quantity * $additionalTest->salePrice), 2, '.', '') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endif

                        <tr style="background-color: #c4d79b;">
                            <td colspan="12">
                                {{$tests_with_nonzero_qty ?? 0 > 0 ? 'Including' : 'Excluding'}} costs for tests,
                                quarantining and veterinary inspection.<br>
                                For orders less than Euro 5000,00; a fee of Euro 750,00 will be charged as administration costs.<br>
                                We can consider supplying animals in exchange for your surplus animals; Details of the future destination
                                can be provided on request.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <p>
                    <b>Explanation:</b>
                    <ul>
                        <li>M: Males, F: Females, U: Specimen(s) of which sex is unknown.</li>
                        <li>c.b.: captive bred, w.c.: wild caught, c.b./w.c.: mixed group of captive bred and wild-caught specimens.</li>
                        <li>Young adult: the specimens have various ages, between young and just adult. Please indicate what exact age that you prefer.</li>
                        <li>Ex continent: the continent where the specimens are located and from where they will be shipped.</li>
                        <li>Ex Institution: price for just the specimen(s), without costs of crate and any transport.</li>
                        <li>FOB: Price of the specimen(s) including costs of crate(s) and transport to the airport of departure.</li>
                        <li>C+F : Price of the specimen(s) including costs of crate(s) and transport to the airport of destination.</li>
                        <li>Basic costs: costs for documents, handling, custom costs, and veterinarian inspection. These costs are for each shipment regardless of the quantity of specimens.</li>
                        <li>Young-adult: this means there are several specimens available of different ages, all not old. You can advise us which age that you prefer.</li>
                    </ul>

                    <b>Important information:</b>
                    <ul>
                        <li>For large orders, discount can be considered. For orders less that Euro 5000,00 ex institution, a fee of Euro 750,00 will be charged as administration costs. For large orders a discount can be discussed.</li>
                        <li>We have no animals in stock, but manage a database with available specimens of zoological institutions worldwide; details can be applied by us to supplying institutions.</li>
                        <li>This offer is valid for 14 days; the offer is subject to availability which will be checked after confirmation of your interest.</li>
                        <li>For institutions located in the European Union: Only zoos open to the public are allowed to import birds from outside the EU; private collectors are not authorized.</li>
                        <li>For some shipments a minimum quantity of specimens to be ordered might be required by the supplier.</li>
                        <li>Information and pictures about the future destination of the specimen(s) might be requested by the supplier-institution, for final approval of the transaction.</li>
                        <li>Delivery time of the specimen(s) depends on the granting of the permits by the authorities on both sides.</li>
                        <li>Due to Covid-19, pandemics, political situations and other, changes in logistics processes can be expected. Delays and cancellation of flights/vessels/trucks might occur. Rates can fluctuate on an hourly basis. If the flight cannot proceed for any reason, the costs for the quarantine/stabling, the testing and other costs are for the applicant. IZS is not responsible for any costs due to previously mentioned reasons.</li>
                    </ul>

                    <b>Terms of payment:</b>
                    <ul>
                        <li>Species that needed no special documents and can be shipped immediately full payment at the moment of reservation.</li>
                        <li>Species that needs a permit application: 30% the moment of reservation; the balance before shipment.</li>
                    </ul>

                    Looking forward to hearing from you.<br><br>
                    Kindest regards,<br><br>
                    INTERNATIONAL ZOO SERVICES<br>
                    <a href="https://www.zoo-services.com">www.zoo-services.com</a>
                </p>
            </div>
        </div>
    </body>
</html>
