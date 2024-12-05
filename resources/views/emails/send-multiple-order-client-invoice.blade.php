<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$client->letter_name}},</p>
            <p>Find enclosed invoice for:</p>
            <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 10px; text-align: center;">M</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">F</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">U</td>
                    <td style="width: 150px; text-align: center;">Species</td>
                </tr>
                @foreach ($orders as $order)
                    @foreach ($order["offer"]["species_ordered"] as $species)
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
                @endforeach
            </table>
            <br>
            <p>
                Please send us ttcopy of payment when available.<br>
                Final reservation and document application can only be started once this payment is confirmed on the account.
            </p>
            <p>Your kind cooperation is much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
