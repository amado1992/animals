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
                <a href="{{url('/')}}/contacts/resetListEmailNewContact">Reset List</a>
                <table style="font-size: 13px;" border="0">
                    <tr>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Name
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Institution
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Type
                        </td>
                        <td style="font-weight: bold; margin-bottom: 20px; width: 300px;">
                            City
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Country
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Url
                        </td>
                    </tr>
                    @php($date = "")
                    @foreach ($contacts as $contact)
                        <tr>
                            <td>
                                {{ $contact->full_name }}
                            </td>
                            <td>
                                @if($contact->organisation) {{ $contact->organisation->name }} @else - @endif
                            </td>
                            <td>
                                @if($contact->organisation && $contact->organisation->type)
                                    <span class="self-cursor" title="{{ $contact->organisation->type->label }}">{{ $contact->organisation->type->key }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $contact->city }}
                            </td>
                            <td>
                                @if($contact->country) {{ $contact->country->name }} @else - @endif
                            </td>
                            <td>
                                <a href="{{env("APP_URL")}}/contacts/{{$contact->id}}">{{env("APP_URL")}}/contacts/{{$contact->id}}</a>
                            </td>
                        </tr>
                    @endforeach
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
