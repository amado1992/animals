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
                <a href="{{url('/')}}/surplus/resetListEmailNewSurplu">Reset List</a>
                <table style="font-size: 13px;" border="0">
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
                        <td style="font-weight: bold; margin-bottom: 20px; width: 200px;">
                            Name and Common Name
                        </td>
                        <td style="font-weight: bold; margin-bottom: 20px; width: 100px;">
                            Supplier
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Continent / Origin / Age
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Supplier price
                        </td>
                        <td style="font-weight: bold; margin-bottom: 10px;">
                            Profit
                        </td>
                    </tr>
                    @php($date = "")
                    @foreach ($surplus as $row)
                        @if($date != date("d-m-Y", strtotime($row->created_at)))
                            @php($date = date("d-m-Y", strtotime($row->created_at)))
                            <tr style="vertical-align: top;">
                                <td colspan="7">
                                </td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td colspan="7">
                                </td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td colspan="7">
                                    <b>{{ date("d F Y", strtotime($row->created_at)) }}</b>
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
                                <a href="https://app.zoo-services.com/surplus/{{$row->id}}" >{{ $row->animal->scientific_name ?? "" }} - {{ $row->animal->common_name ?? "" }}</a>
                            </td>
                            <td>
                                @if ($row->organisation != null)
                                    {{ $row->organisation->name  }}
                                @elseif ($row->contact != null)
                                    {{ $row->contact->full_name }}
                                @endif
                            </td>
                            <td>
                                {{ ($row->country != null) ? $row->country->region->name : '' }}, {{ $row->origin_field ?? ""}}{{ !empty($row->age_field) ? ", " . $row->age_field : "" }}
                            </td>
                            <td>
                                <strong>M</strong>: {{ ucfirst($row->cost_currency) ?? ""}} {{ $row->costPriceM ?? ""}} / <strong>F</strong>: {{ ucfirst($row->cost_currency) ?? ""}} {{ $row->costPriceF ?? ""}} / <strong>U</strong>: {{ ucfirst($row->cost_currency) ?? ""}} {{ $row->costPriceU ?? ""}}
                            </td>
                            <td>
                                <strong>M</strong>: {{ ucfirst($row->sale_currency) ?? ""}} {{ $row->salePriceM - $row->costPriceM }} / <strong>F</strong>: {{ ucfirst($row->sale_currency) ?? ""}} {{ $row->salePriceF - $row->costPriceF }} / <strong>U</strong>: {{ ucfirst($row->sale_currency) ?? ""}} {{ $row->salePriceU - $row->costPriceU}}
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
