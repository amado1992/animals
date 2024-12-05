<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->client->letter_name}},</p>
            <p>Statement IZS, for the species that you have ordered, can be found enclosed. If possible, please kindly confirm receipt of this document.</p>
            <p>Your kind communication back would be much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
