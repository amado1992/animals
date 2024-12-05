<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$offer->client->letter_name}},</p>
            <p>Many thanks for your inquiry.</p>
            <p>The supplier-zoo likes to be informed about the future destination; can you please be so kind to send us a picture of the enclosure?</p>
            <p>Many thanks in advance for your collaboration.</p>
            <p>Awaiting your kind communication back.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
