<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            @if ($offer->client)
            <p>Dear {{$offer->client->letter_name}},</p>
           @else
            <p>Dear {{$offer->organisation->name}},</p>
           @endif
            <p>Regarding the following species:</p>
            <p>
                @foreach ($offer->species_ordered as $animal)
                    -- {{ $animal->oursurplus->animal->scientific_name}} ({{ $animal->oursurplus->animal->common_name}})<br>
                @endforeach
            </p>
            <p>We will send you an offer ex zoo; the costs for transport and crates, we estimate on:</p>
            <p>XXXXXXXXXXXXXXXXXXXXXXXX</p>
            <p>In case you are interested and you want to receive an offer with the exact costs till your airport, please let me know.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
