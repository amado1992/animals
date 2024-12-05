<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$offer->client->letter_name}},</p>
            <p>Recently we have sent to you the offer below.</p>
            <p>We just were informed by the supplying institution that they are prepared to adjust the price; can you please inform us what price you find acceptable?</p>
            <p>In case you prefer an exchange, this can also be considered. The destination of your surplus animals is always subject to your approval.</p>
            <p>
                Looking forward to hear from you.<br>
                .........................
            </p>
            <p style="font-size: 12px; font-weight: bold;">
                OFFER {{ $offer->offer_type }} {{ $offer->full_number }}
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
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
