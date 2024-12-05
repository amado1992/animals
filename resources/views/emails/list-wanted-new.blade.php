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
                <a href="{{url('/')}}/wanted/resetListEmailNewWanted">Reset List</a>
                <table style="font-size: 13px;" border="0">
                    <tr>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Species
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Client institution
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Looking for
                        </td>
                        <td style="font-weight: bold; margin-bottom: 20px; width: 300px;">
                            Origin
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Age
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Url
                        </td>
                    </tr>
                    @php($date = "")
                    @foreach ($wanteds as $wanted)
                        <tr>
                            <td>
                                <span class="card-title mb-0">{{ ($wanted->animal != null) ? $wanted->animal->common_name : '' }}</span>
                                <span><em>({{ ($wanted->animal != null) ? $wanted->animal->scientific_name : '' }})</em></span>
                            </td>
                            <td>
                                @if ($wanted->organisation != null)
                                    <span class="card-title mb-0">{{ $wanted->organisation->name }} <em>({{ $wanted->organisation->email }})</em></span>
                                @else
                                    <span class="card-title mb-0 text-danger">INSTITUTION NOT DEFINED</span>
                                @endif
                            </td>
                            <td>
                                {{ $wanted->looking_field }}
                            </td>
                            <td>
                                {{ $wanted->origin_field }}
                            </td>
                            <td>
                                {{ $wanted->age_field }}
                            </td>
                            <td>
                                <a href="{{env("APP_URL")}}/wanted/{{$wanted->id}}">{{env("APP_URL")}}/wanted/{{$wanted->id}}</a>
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
