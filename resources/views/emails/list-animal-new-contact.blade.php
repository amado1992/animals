<!doctype html>
<html>
@include('emails.email-header')
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td class="container">
            <div class="content">
                <p>{{$email_title}},</p>
                <p>{{$email_initially}}</p>
                <br>
                <table style="font-size: 13px; border: 0;" border="0">
                    <tr>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            M
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            F
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            U
                        </td>
                        <td style="font-weight: bold; margin-bottom: 20px; width: 500px;">
                            Name and Common Name
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Origin
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Birth year / Age
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Continent
                        </td>
                    </tr>
                    @php($class = "")
                    @foreach ($surplus as $row)
                        @if(!empty($row->animal->code_number) && $class != substr($row->animal->code_number, 0, 2))
                            @php($class = substr($row->animal->code_number, 0, 2) ?? '')
                            <tr>
                                <td colspan="7">
                                    @if($class === "12")
                                        <b>Reptiles</b>
                                    @elseif ($class === "13")
                                        <b>Birds</b>
                                    @elseif ($class === "14")
                                        <b>Mammals</b>
                                    @elseif ($class === "10")
                                        <b>Fish</b>
                                    @elseif ($class === "11")
                                        <b>Amphibians</b>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                {{ $row->quantityM ?? "" }}
                            </td>
                            <td>
                                {{ $row->quantityF ?? "" }}
                            </td>
                            <td>
                                {{ $row->quantityU ?? "" }}
                            </td>
                            <td>
                                {{ $row->animal->scientific_name ?? "" }} ({{ $row->animal->common_name ?? "" }})<br>
                                <i>{{ $row->remarks }}</i>
                            </td>
                            <td>
                                {{ $row->origin_field ?? ""}}
                            </td>
                            <td>
                                @if(empty($row->bornYear))
                                    @if(!empty($row->age_field))
                                        @if($row->age_group === "less_1_year")
                                            {{ date("Y", strtotime("now"))  }},
                                        @elseif($row->age_group === "between_1_2_years")
                                            {{ date("Y", strtotime("now - 1 year"))  }} - {{ date("Y", strtotime("now - 2 year"))  }},
                                        @elseif($row->age_group === "between_1_3_years")
                                            {{ date("Y", strtotime("now - 1 year"))  }} - {{ date("Y", strtotime("now - 3 year"))  }},
                                        @elseif($row->age_group === "between_2_3_years")
                                            {{ date("Y", strtotime("now - 2 year"))  }} - {{ date("Y", strtotime("now - 3 year"))  }},
                                        @endif
                                    @endif
                                @else
                                    {{ $row->bornYear . "," }}
                                @endif
                                {{ $row->age_field ?? "" }}
                            </td>
                            <td>
                                {{ ($row->country != null) ? $row->country->region->name : '' }}
                            </td>
                        </tr>
                    @endforeach
                </table>
                <br>
                <br>
                <p>{{$email_footer}}</p>
                @include('emails.email-signature')
            </div>
        </td>
    </tr>
</table>
</body>
</html>
