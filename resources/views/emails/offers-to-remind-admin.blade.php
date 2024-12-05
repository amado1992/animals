<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            @if ($offersToRemind->times_reminded == 0)
                <p>There are offers to remind, see below:</p>
            @else
                <p>These offers where already reminded, please check them in order to change the status to Prospect or Cancelled. See below:</p>
            @endif
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 20px; text-align: center;">Req. No</td>
                    <td style="width: 200px; text-align: center;">Quant. & Species</td>
                    <td style="width: 100px; text-align: center;">Client</td>
                </tr>
                @foreach ($offersToRemind as $offer)
                    <tr style="vertical-align: top;">
                        <td style="text-align: center;">
                            {{ $offer->full_number }}
                        </td>
                        <td>
                            @if ($offer->offer_species->count() == 0)
                                <span style="color: red;">No species added yet</span>
                            @elseif ($offer->offer_species->count() > 3)
                                    <span>Several species</span>
                            @else
                                @foreach ($offer->species_ordered as $species)
                                    {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <strong>Client:</strong> {{ $offer->client->full_name }}<br>
                            ({{ $offer->client->email }})<br>
                            @if ($offer->client->organisation != null)
                                <strong>Institution:</strong> {{ $offer->client->organisation->name }}<br>
                                <strong>Type:</strong> {{ ($offer->client->organisation->type) ? $offer->client->organisation->type->label : '' }}<br>
                            @endif
                            <strong>Country:</strong> {{ ($offer->client->country) ? $offer->client->country->name : '' }}
                        </td>
                    </tr>
                @endforeach
            </table>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
