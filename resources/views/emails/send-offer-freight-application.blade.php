<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ ($offer->airfreight_agent) ? $offer->airfreight_agent->letter_name : $offer->supplier->letter_name }},</p>
            <p>
                We currently work on a project that concerns transport of some live animal species to {{$offer->delivery_country->name}}.<br>
                It concerns the following species:
            </p>
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
                    @foreach ($offer->species_ordered as $animal)
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
                            @if ($offer->organisation !== null)
                                {!! $offer->organisation->institution_details !!}
                            @endif
                        </td>
                        <td>
                            @if ($offer->supplier->organisation != null)
                                {!! $offer->supplier->organisation->institution_details !!}
                            @endif
                        </td>
                    </tr>
                </table><br>
            </p>
            <p>It would be deeply appreciated if we could receive your quotation for this transport: from {{ ($offer->supplier->city) ? $offer->supplier->city : 'SUPPLIER CITY' }}, {{ ($offer->supplier->organisation && $offer->supplier->organisation->country) ? $offer->supplier->organisation->country->name : 'SUPPLIER COUNTRY' }} to {{$offer->delivery_airport->name}}, {{$offer->delivery_country->name}}.</p>
            <p>Awaiting your kind communication back.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
