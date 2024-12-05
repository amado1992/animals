<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->supplier->letter_name}},</p>
            <p>
                Regarding the following species:
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
            <p>Please be so kind to apply a Cites export permit on name of the following destination:</p>
            <p>
                @if ($order->client->organisation != null)
                    {!! $order->client->organisation->institution_details !!}
                @else
                    <span style="color: red;">CLIENT WITHOUT ORGANIZATION - NO ADDRESS DETAILS</span>
                @endif
            </p>
            <p>Can you send us a copy as soon as you received this permit? </p>
            <p>Your kind communication back would be much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
