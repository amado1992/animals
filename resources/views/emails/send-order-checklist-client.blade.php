<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->client->letter_name}},</p>
            <p>On checklist enclosed, you can indicate all Consignee details for each document so we can forward accordingly to shipper.</p>
            <p>Please be so kind to fill out attached form and return it.</p>
            <p>Your kind communication back would be much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
