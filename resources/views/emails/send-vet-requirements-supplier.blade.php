<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->supplier->letter_name}},</p>
            <p>Find enclosed official veterinary import condition for the ordered species:</p>
            <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 10px; text-align: center;">M</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">F</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">U</td>
                    <td style="width: 220px; text-align: center;">Species</td>
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
            <p>Kindly let us know if these can be declared on Health Certificate, and if any additional costs for testing would be required, should we be able to inform client accordingly.</p>
            <p>Awaiting your kind communication back.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
