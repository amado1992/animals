<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ ($offer->supplier) ? $offer->supplier->letter_name : 'Mr./Mrs.'}},</p>
            <p>Please be so kind to send us some pictures/videos of the animal(s) below mentioned.</p>
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
