<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$contact->full_name}},</p>
            <p>We thank you very much for your application.</p>
            <p>However we are trying to visit your website, but we cannot open it.</p>
            <p>Please let us know where we can find more information about your institution.</p>
            <p>Looking forward to hear from you,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
