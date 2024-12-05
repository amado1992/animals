<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ ($order->airfreight_agent) ? $order->airfreight_agent->letter_name : $order->supplier->letter_name}},</p>
            <p>In order to make preparations for transport of the ordered species:</p>
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
            <p>Hereyby <strong>external dimensions of crates</strong> (length*width*height in cm) for transporting said species.</p>
            <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold; text-align: center;">
                    <th rowspan="2">qty</th>
                    <th colspan="3">external crate dimensions (CM)</th>
                    <th rowspan="2">qty of species inside</th>
                </tr>
                <tr style="text-align: center;">
                    <td style="width: 30px;">length</td>
                    <td style="width: 30px;">width</td>
                    <td style="width: 30px;">height</td>
                </tr>
                <tr>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                </tr>
                <tr>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                </tr>
            </table>
            <br>
            <p>
                Please kindly let us know how long it may take for construction and corresponding costs.<br>
                Awaiting your kind communication back.
            </p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
