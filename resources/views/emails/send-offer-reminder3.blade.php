<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$offer->client->letter_name}},</p>
            <p>Unfortunately, we have not heard back from you on our offer for:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 10px; text-align: center;">M</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">F</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">U</td>
                    <td style="width: 150px;"></td>
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
                    </tr>
                @endforeach
            </table>
            <p>Therefore we assume you are no longer interested and will cancel your offer.</p>
            <p>Please let us know in case we have misunderstood and you need more time.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
