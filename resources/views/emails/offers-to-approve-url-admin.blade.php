<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>There are offers to approve, see below:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 20px; text-align: center;">Req. No</td>
                    <td style="width: 200px; text-align: left;">URL</td>
                </tr>
                @foreach ($offersToApprove as $offer)
                    <tr style="vertical-align: top;">
                        <td style="text-align: center;">
                            {{ $offer->full_number }}
                        </td>
                        <td>
						<a href="{{ url('offers', $offer->id) }}">{{ url('offers', $offer->id)}}</a>
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
			<br>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
