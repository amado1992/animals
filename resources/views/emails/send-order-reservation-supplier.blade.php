<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->supplier->letter_name}},</p>
            <p>Enclosed you can find final Reservation for:</p>
            <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 220px; text-align: center;">Species</td>
                </tr>
                @foreach ($order->offer->species_ordered as $species)
                    <tr style="vertical-align: top;">
                        <td style="white-space: nowrap;">
                            {{ $species->oursurplus->animal->scientific_name}} ({{ $species->oursurplus->animal->common_name}})
                        </td>
                    </tr>
                @endforeach
            </table>
            <br>
            <p>Please kindly confirm receipt of this document and corresponding reservation for the species.</p>
            <p>Further details for document application will be sent shortly.</p>
            <p>Your kind communication back would be much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
