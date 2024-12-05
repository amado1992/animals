<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->client->letter_name}},</p>
            <p>After checking your veterinary import conditions for the ordered species:</p>
            <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; text-align: center;">
                    <td style="width: 10px;">M</td>
                    <td style="width: 1px;">-</td>
                    <td style="width: 10px;">F</td>
                    <td style="width: 1px;">-</td>
                    <td style="width: 10px;">U</td>
                    <td style="width: 220px;">Species</td>
                </tr>
                @foreach ($order->offer->species_ordered as $species)
                    <tr style="vertical-align: top;">
                        <td style="text-align: center;">
                            {{ ($species->offerQuantityM < 0) ? 'x' : $species->offerQuantityM }}
                        </td>
                        <td></td>
                        <td style="text-align: center;">
                            {{ ($species->offerQuantityF < 0) ? 'x' : $species->offerQuantityF }}
                        </td>
                        <td></td>
                        <td style="text-align: center;">
                            {{ ($species->offerQuantityU < 0) ? 'x' : $species->offerQuantityU }}
                        </td>
                        <td style="white-space: nowrap;">
                            {{ $species->oursurplus->animal->scientific_name}} ({{ $species->oursurplus->animal->common_name}})
                        </td>
                    </tr>
                @endforeach
            </table>
            <br>
            <p>Supplying institution has confirned they can declare them on official Health Certificate. Based on your authorities requirements it would imply extra costs for:</p>
            <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; text-align: center;">
                    <td style="width: 80px;"></td>
                    <td style="width: 30px;">Curr</td>
                    <td style="width: 50px;">Subtotal</td>
                </tr>
                @foreach ($order->offer->additional_costs()->where('is_test', 1)->get() as $additionalTest)
                    <tr>
                        <td>{{ $additionalTest->name }}:</td>
                        <td style="text-align: center;">{{ $additionalTest->currency }}</td>
                        <td style="text-align: center;">{{ number_format(abs($additionalTest->quantity * $additionalTest->salePrice), 2, '.', '') }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; text-align: center;">
                    <td></td>
                    <td>TOTAL:</td>
                    <td>{{ number_format($order->offer->offerAdditionalTestsTotalSale, 2, '.', '') }}</td>
                </tr>
            </table>
            <br>
            <p>Awaiting your kind communication back.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
