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
            <p>This species is only available on loan for institution open to the public, preferable governmental ones. It concerns a permanent loan from Government to Government.</p>
            <p>A personal visit of the director/manager of the interested zoo to the authorities of this supplying country is necessary. Our management fee for this project is USD 25.000,00, regardless the quantity of specimens that your inquiry concerns. This includes handling correspondence and communications, document arrangements, accompany of the visits to the country of supply, transport consultancy. This will be charged in parts, as the process is divided in several steps.</p>
            <p>From investigation of the possibilities till the transport. More information on request. Besides this, local authorities expect a donation for a wildlife project, which mostly vary between USD 20.000,00 to 30.000,00. We have organized a similar project with these authorities before.</p>
            <p>In case you are interested please contact us for more details.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
