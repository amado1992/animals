<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Zoo Services">

        <!-- Scripts -->
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

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
        <div class="container pdf-container">
            @include('pdf_documents.header_pdf')

            <div class="row">&nbsp;</div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table" width="100%" cellspacing="0">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold;">N&uacute;mero de oferta: {{ $offer->full_number }}</td>
                                <td style="text-align: right; font-weight: bold;">{{ $dateOfToday }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right; font-weight: bold;">1 EUR = {{ $rates->first() ? number_format($rates->first()->EUR_USD, 2, '.', '') : 'No rate' }} USD / 1 USD = {{ $rates->first() ? number_format($rates->first()->USD_EUR, 2, '.', '') : 'No rate' }} EUR</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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

            <div class="row">
                <div class="col-md-12">&nbsp;</div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    Estimado(a)
                    @if ($offer->client)
                        {{ $offer->client->letter_name }}
                    @else
                        {{ $offer->organisation->name }}
                    @endif
                    ,<br><br>
                    De acuerdo con su reciente pedido, nos complace ofrecerle las siguientes especies:
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">&nbsp;</div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table" style="table-layout: unset; margin-right: 5px;">
                        <thead>
                            <tr style="font-weight: bold; text-align: center; background-color: #4f6228; color: #ffffff;">
                                <th colspan="3">Cantidad</th>
                                <th colspan="2">Detalles</th>
                                <th colspan="6">Precios c/u</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr style="font-weight: bold; text-align: center; background-color: #76933c; color: #ffffff;">
                                <td style="width: 18px;">M</td>
                                <td style="width: 18px;">F</td>
                                <td style="width: 18px;">U</td>
                                <td style="width: 140px;">Nombre cient&iacute;fico</td>
                                <td style="width: 140px;">Obervaciones</td>
                                <td style="width: 20px;">&nbsp;</td>
                                <td style="width: 65px;">M</td>
                                <td style="width: 65px;">F</td>
                                <td style="width: 65px;">U</td>
                                <td style="width: 65px;">Pareja</td>
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
                                <td style="white-space: nowrap;">
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
                                    <td colspan="5">*Costos b&aacute;sicos adicionales por cada env&iacute;o (sea cual sea la cantidad de animales)</td>
                                    <td></td>
                                    <td colspan="5">Precio</td>
                                </tr>
                                @if($offer->airfreight_type == "pallets")
                                    <tr style="background-color: #ebf1e8;">
                                        <td colspan="5">Transporte por contenedores:</td>
                                        <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                                        <td colspan="5" style="text-align: center;">{{ number_format($offer->offerTotalAirfreightPalletSalePrice, 2, '.', '') }}</td>
                                    </tr>
                                @elseif($offer->airfreight_type == "byTruck")
                                    <tr style="background-color: #ebf1e8;">
                                        <td colspan="5">Transporte por carretera:</td>
                                        <td style="text-align: center;">{{ $offer->offer_currency }}</td>
                                        <td colspan="5" style="text-align: center;">{{ number_format($offer->offerTotalTransportTruckSalePrice, 2, '.', '') }}</td>
                                    </tr>
                                @endif
                                @if ($offer->additional_costs()->where('is_test', 0)->where('quantity', '<>', 0)->where('salePrice', '<>', 0)->get()->count() > 0 || $offer->additional_costs()->where('is_test', 1)->where('quantity', '<>', 0)->where('salePrice', '<>', 0)->get()->count() > 0)
                                    @foreach ($offer->additional_costs()->where('is_test', 0)->get() as $additionalCost)
                                        @if ($additionalCost->salePrice != 0)
                                            <tr style="background-color: #ebf1e8;">
                                                <td colspan="5">{{ $additionalCost->name }}</td>
                                                <td style="text-align: center;">{{ $additionalCost->currency }}</td>
                                                <td colspan="5" style="text-align: center;">{{ number_format(abs($additionalCost->quantity * $additionalCost->salePrice), 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @foreach ($offer->additional_costs()->where('is_test', 1)->get() as $additionalTest)
                                        @if ($additionalTest->salePrice != 0)
                                            <tr style="background-color: #ebf1e8;">
                                                <td colspan="5">{{ $additionalTest->name }}</td>
                                                <td style="text-align: center;">{{ $additionalTest->currency }}</td>
                                                <td colspan="5" style="text-align: center;">{{ number_format(abs($additionalTest->quantity * $additionalTest->salePrice), 2, '.', '') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <tr style="background-color: #c4d79b;">
                                    <td colspan="11">
                                        Los precios no incluyen pruebas ni cuarentena, solo en caso de que se requiera por el pa&iacute;s importador.
                                    </td>
                                </tr>
                            @endif

                            <tr style="background-color: #c4d79b;">
                                <td colspan="11">
                                    Para &oacute;rdenes menores de EUR o USD 5000.00, se cobrar&aacute; una cuota de 750.00 por Gastos Administrativos.<br>
                                    Podemos considerar el suministro de animales a cambio de sus animales excedentes; Los detalles del destino futuro se pueden proporcionar a su solicitud.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <p class="text-justify">
                        <b>Explicaci&oacute;n:</b>
                        <ul>
                            <li>M: macho, F: hembra, U: sexo desconocido</li>
                            <li>c.b.: criado en cautiverio, w.c.: capturado salvaje, c.b./w.c.: grupo mixto de especímenes: criados en cautiverio y atrapados salvaje.</li>
                            <li>Adulto joven: esp&eacute;cimen de edad joven y/o adulto joven. Puede seleccionar la edad de su peferencia.</li>
                            <li>Ex continent: continente de origen de los espec&iacute;menes y desde donde ser&aacute;n exportados.</li>
                            <li>Ex Institution: Precio solamente del espec&iacute;men sin incluir costos de transportaci&oacute;n ni de embalaje.</li>
                            <li>FOB: Precio del esp&eacute;cimen incluyendo costos de transportaci&oacute;n y de embalaje hasta el aeropuerto de salida.</li>
                            <li>C+F: Precio del esp&eacute;cimen incluyendo costos de transportaci&oacute;n y de embalaje hasta el aeropuerto de destino.</li>
                            <li>Costos b&aacute;sicos: costos de documentos, manejo de la carga, aduana, inspecci&oacute;n veterinaria. Estos costos son para cada exportaci&oacute;n, sin importar la cantidad de animales.</li>
                            <li>Young-adult: esto significa que hay varias especies disponibles de diferentes edades, no todas son mayores. Usted puede indicarnos cual edad prefiere.</li>
                        </ul>

                        <b>Informaci&oacute;n importante:</b>
                        <ul>
                            <li>Puede considerarse un descuento para largas &oacute;rdenes. Para &oacute;rdenes menores de EUR o USD 5000.00 valor Ex Instituci&oacute;n, se cobrar&aacute; una cuota de 750.00 por Gastos Administrativos.</li>
                            <li>IZS no tiene animales en existencia, pero mantiene una base de datos de surplus de zool&oacute;gicos de todo el mundo. </li>
                            <li>Esta oferta es v&aacute;lida por 14 d&iacute;as; est&aacute; sujeta a cancelaci&oacute;n de no obtenerse los permisos requeridos por ambas partes.</li>
                            <li>Para Instituciones situadas en la Uni&oacute;n Europea: Solo zool&oacute;gicos abiertos al p&uacute;blico est&aacute;n autorizados a importar aves provenientes de pa&iacute;ses no pertenecientes a la EU; coleccionistas privados no est&aacute;n autorizados.</li>
                            <li>Para algunos embarques, el proveedor requiere un m&iacute;nimo de especies a embarcar.</li>
                            <li>Fotos y otros detalles sobre el destino final de los espec&iacute;menes pueden ser requeridas por parte de la instituci&oacute;n proveedora.</li>
                            <li>El plazo para la exportaci&oacute;n depender&aacute; de los permisos emitidos por las autoridades en ambas partes.</li>
                            <li>Debido a covid-19, se pueden esperar cambios en los procesos log&iacute;sticos. Pueden ocurrir demoras y cancelaciones de vuelos/embarcaciones/camiones. Las tarifas pueden fluctuar por hora. Si el vuelo no puede continuar por alg&uacute;n motivo, los costos de cuarentena, las pruebas y otros costos son para el solicitante. IZS no es responsable de ning&uacute;n costo debido a covid-19.</li>
                        </ul>

                        <b>T&eacute;rminos de pago:</b>
                        <ul>
                            <li>Las especies que no requieran documentos especiales, podr&aacute;n ser transportadas con inmediatez: pago del monto  total al momento de reservar.</li>
                            <li>Las especies que necesitan permisos especiales: 30% al momento de reservar, el resto antes del embarque.</li>
                        </ul>

                        A la esperta de saber de usted.<br><br>
                        Cordialmente,<br><br>
                        INTERNATIONAL ZOO SERVICES<br>
                        <a href="https://www.zoo-services.com">www.zoo-services.com</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
