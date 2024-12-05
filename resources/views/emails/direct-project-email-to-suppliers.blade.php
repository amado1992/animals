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
            <p>Can you inform us if you can supply this species? Also breeding loan can be considered.<br></p>
            <p style="font-weight: bold;">Please note that we will bring you directly in contact with the interested zoo to discuss the details; so the transaction is directly between zoos.</p>
            <p>Looking forward hearing from you,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
