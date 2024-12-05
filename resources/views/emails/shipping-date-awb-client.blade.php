<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ $order->client->letter_name }},</p>
            <p>Enclosed you can find Draft AWB for the ordered species, so you can begin customs preparations on your side.</p>
            <p>
                M = number of males, F = number of females, U = number of unsexed specimens
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
                </table><br>
            </p>
            <p>
                Please let us know by return  your confirmation, so that final shipping arrangements can be done.<br>
                Awaiting your kind communication back.
            </p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
