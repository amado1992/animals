<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear contact_name,</p>
            <p>
                One of our relations, a recognized zoological institution, is looking for <strong>{{$animal->common_name}} ({{$animal->scientific_name}})</strong>
                @if ($wanted != null)
                    @if ($wanted->looking_field != ''){{', ' . $wanted->looking_field}} @endif
                    @if ($wanted->remarks){{', ' . $wanted->remarks}} @endif
                    .
                @endif
            </p>
            <p>
                Can you inform us if you can help us supplying this species? Also breeding loan can be considered.<br>
                Please note that the destination is subject to your approval. All information can be provided on request.
            </p>
            <p>Looking forward hearing from you,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
