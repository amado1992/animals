<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->client->letter_name}},</p>
            <p>Reservation sheet, for the species that you have ordered, can be found enclosed. If possible, please kindly confirm receipt of this document.</p>
            <p>Also enclosed Checklist form, where you can indicate Consignee details for each document required for the importation/transportation of the concerning species into your country. Application of documents will be done based on the information provided by you. Please be so kind to fill out attached form and send it by return, at your earliest convenience.</p>
            <p>Your kind communication back would be much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
