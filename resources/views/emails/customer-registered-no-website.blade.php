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
            <p>Because there seems to be no website or facebook page, we would request you to inform us more about your breeding farm; what species are you keeping, breeding results, contacts with zoos, etc?.</p>
            <p>Looking forward to hear from you,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
