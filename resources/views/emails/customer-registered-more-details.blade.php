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
            <p>However we would like to receive some more extended information about your institution; where are you exactly located, and what kind of species are you keeping?.</p>
            <p>Do you have any cooperation with any recognized public zoos?</p>
            <p>Looking forward to hear from you,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
