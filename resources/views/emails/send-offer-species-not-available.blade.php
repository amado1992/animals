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
            <p>Many thanks for your inquiry.</p>
            <p>We are sorry to inform you that the following species have just been reserved and are no longer available:</p>
            <p>
                @foreach ($offer->species_ordered as $animal)
                    -- {{ $animal->oursurplus->animal->scientific_name}} ({{ $animal->oursurplus->animal->common_name}})<br>
                @endforeach
            </p>
            <p>Our sincerely apologizes for the inconvenience.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
