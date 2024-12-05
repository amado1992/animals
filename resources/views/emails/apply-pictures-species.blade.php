<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ ($offer->supplier) ? $offer->supplier->letter_name : 'Mr./Mrs.'}},</p>
            <p>Many thanks for your offer.</p>
            <p>The interested zoo likes to know more about the specimen(s); can you please be so kind to send us all details like age? And one or more pictures would be very welcome.</p>
            <p>Many thanks in advance for your collaboration.</p>
            <p>Awaiting your kind communication back.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
