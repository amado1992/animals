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
                <a href="{{url('/')}}/organisations/resetListEmailNewOrganisation">Reset List</a>
                <table style="font-size: 13px;" border="0">
                    <tr>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Institution
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Canonical Name
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
                    @foreach ($organisations as $organisation)
                        <tr>
                            <td>
                                {{ $organisation->name }}
                            </td>
                            <td>
                                {{ $organisation->canonical_name ?? "--" }}
                            </td>
                            <td>
                                @if($organisation->type)
                                    <span class="self-cursor" title="{{ $organisation->type->label }}">{{ $organisation->type->key }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $organisation->city }}
                            </td>
                            <td>
                                {{ ($organisation->country) ? $organisation->country->name : '' }}
                            </td>
                            <td>
                                <a href="{{url('/')}}/organisations/{{$organisation->id}}">{{url('/')}}/organisations/{{$organisation->id}}</a>
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
