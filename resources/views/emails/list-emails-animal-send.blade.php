<!doctype html>
<html>
@include('emails.email-header')
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td class="container">
            <div class="content">
                <p>{{$email_title}},</p>
                <br>
                <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                    <tr style="vertical-align: top;">
                        <td style="font-weight: bold; margin-bottom: 10px; width: 300px;">
                            Emails
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Name
                        </td>
                    </tr>
                    </tr>
                    @if(!empty($send_emails))
                        @foreach ($send_emails as $row)
                            <tr style="vertical-align: top;">
                                <td>
                                    {{ $row["email"]  ?? "" }}
                                </td>
                                <td>
                                    {{ $row["name"] ?? "" }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
                <br>
                <br>
                @include('emails.email-signature')
            </div>
        </td>
    </tr>
</table>
</body>
</html>
