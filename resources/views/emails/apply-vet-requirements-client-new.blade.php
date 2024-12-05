<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$offer->client->letter_name}},</p>
            <p>Please be so kind to send us the veterinary import conditions for the animal(s), mentioned below.</p>
            <p>The country of export is: {{ !empty($offer->delivery_country) ? $offer->delivery_country->name : '' }}</p>
            <p>Many thanks for your cooperation.</p>
            <br>
            <br>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
