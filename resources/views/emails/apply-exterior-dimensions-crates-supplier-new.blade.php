<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$offer->supplier->letter_name}},</p>
            <p>Please be so kind to send us your suggestion for the interior measurements of the crates, for the transport of the animal(s) below mentioned.</p>
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
