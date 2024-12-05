<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear contact_name,</p>
            <p>Many thanks for your offer of your surplus specimens; could you please be so kind to inform us all details, like: age, relationship, c.b. or w.c., and your price proposal?</p>
            <p>Thank you very much for your cooperation.</p>
            <br>
            <br>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
