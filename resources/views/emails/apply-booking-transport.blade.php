<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ ($order->airfreight_agent) ? $order->airfreight_agent->letter_name : $order->supplier->letter_name }},</p>
            <p>Please kindly make booking for the ordered species:</p>
            <p>
                M = number of males, F = number of females, U = number of unsexed specimens
                <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                    <tr style="font-weight: bold;">
                        <td style="width: 15px; text-align: center;">M</td>
                        <td style="width: 15px; text-align: center;">F</td>
                        <td style="width: 15px; text-align: center;">U</td>
                        <td style="width: 220px;">Species name</td>
                        <td style="width: 150px;">Crate dimensions</td>
                        <td style="width: 200px;">Remark</td>
                    </tr>
                    @foreach ($order->offer->species_ordered as $animal)
                        <tr style="vertical-align: top;">
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityM }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityF }}
                            </td>
                            <td style="text-align: center;">
                                {{ $animal->offerQuantityU }}
                            </td>
                            <td>
                                {{ $animal->oursurplus->animal->scientific_name}}<br>
                                ({{ $animal->oursurplus->animal->common_name}})
                            </td>
                            <td>
                                ({{$animal->offerQuantityM + $animal->offerQuantityF + $animal->offerQuantityU}}) {{$animal->crateDimensions}}
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                </table>
            </p>
            <p>
                <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                    <tr style="font-weight: bold; text-align: center;">
                        <td style="width: 220px;">Destination)</td>
                        <td style="width: 220px;">Origin</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td>
                            @if ($order->client->organisation != null)
                                {!! $order->client->organisation->institution_details !!}
                            @endif
                        </td>
                        <td>
                            @if ($order->supplier->organisation != null)
                                {!! $order->supplier->organisation->institution_details !!}
                            @endif
                        </td>
                    </tr>
                </table><br>
            </p>
            <p>Please send us copy of AWB, at your earliest convenience, so that client can begin his customs import preparations in advanced.</p>
            <p>Your kind cooperation is much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
