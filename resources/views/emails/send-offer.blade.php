<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            @if (count($in_surplus) > 0)
                <p style='color: red; font-size: 18px;'>
                    The client has the following species on his surplus:<br>
                    @foreach ($in_surplus as $common_name)
                        - {{ $common_name }}<br>
                    @endforeach
                </p>
            @endif
            <p>
                @if ($offer->client)
                    <strong>Client:</strong> {{ $offer->client->full_name }}<br>
                    {{ $offer->client->email }}<br>
                    {{ $offer->client->mobile_phone }}<br>
                    {!! $offer->client->organisation->institution_details !!}
                @else
                    @if ($offer->organisation != null)
                        <strong>Client:</strong> {{ $offer->organisation->name }}<br>
                        {{ $offer->organisation->email }}<br>
                        {{ $offer->organisation->phone ?? "" }}<br>
                        {{ $offer->organisation->address }} {{ $offer->organisation->city }}<br>
                        {{ $organization_country->name ?? '' }}<br>
                        {{-- {{ $offer->client->organisation->email }}<br>
                        {{ $offer->client->organisation->phone }}<br> --}}
                        {{-- {!! $offer->client->organisation->institution_details !!} --}}
                    @endif
                @endif
            </p>
            <p>
                <strong>Request made on:</strong> {{ date('F j, Y', strtotime($offer->created_at)) }}
            </p>
            <p style="font-size: 12px; font-weight: bold;">
                Offer {{ $offer->offer_type }} {{ $offer->full_number }} [link_system]
            </p>
            <p style="font-size: 12px;">
                Explanation:<br>
                M: Males, F: Females, U: Specimen(s) of which sex is unknown, Pr: Pairs<br>
                x-x-x: large quantities available.<br>
                c.b.: captive bred, w.c.: wild caught, c.b./w.c.: mixed group of captive bred and wild-caught specimens.
            </p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 10px; text-align: center;">M</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">F</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">U</td>
                    <td style="width: 150px;"></td>
                    <td style="width: 20px; text-align: center;"></td>
                    <td style="width: 30px; text-align: center;">M</td>
                    <td style="width: 30px; text-align: center;">F</td>
                    <td style="width: 30px; text-align: center;">U</td>
                    <td style="width: 30px; text-align: center;">Pr</td>
                    <td style="width: 150px;">Prices are:</td>
                </tr>
                @foreach ($offer->species_ordered as $animal)
                    <tr style="vertical-align: top;">
                        <td style="text-align: center;">
                            {{ ($offer->quantity_x && $animal->offerQuantityM != 0) ? 'x' : $animal->offerQuantityM }}
                        </td>
                        <td></td>
                        <td style="text-align: center;">
                            {{ ($offer->quantity_x && $animal->offerQuantityF != 0) ? 'x' : $animal->offerQuantityF }}
                        </td>
                        <td></td>
                        <td style="text-align: center;">
                            {{ ($offer->quantity_x && $animal->offerQuantityU != 0) ? 'x' : $animal->offerQuantityU }}
                        </td>
                        <td style="white-space: nowrap;">
                            {{ $animal->oursurplus->animal->scientific_name}}<br>
                            ({{ $animal->oursurplus->animal->common_name}})
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
                        <td>
                            {{ $offer->sale_remark }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan='12'>{{ $animal->surplusDetails }}</td>
                    </tr>
                    <tr>
                        <td colspan='12'>shipped from: {{ $animal->locatedAt }}</td>
                    </tr>
                    <tr><td colspan='12'>&nbsp;</td></tr>
                @endforeach
            </table>
            <p style="font-size: 12px;">
                Excluding costs for tests, quarantining and veterinary inspection.<br>
                For orders less than Euro 5000,00; a fee of Euro 750,00 will be charged as administration costs.
            </p>
            <p style="font-size: 12px;">
                Attached you will find a more specified offer.
            </p>
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
