<table class="table table-striped table-sm mb-0 text-center">
    <thead>
    <tr>
        <th style="font-weight: bold; margin-bottom: 10px;"></th>
        <th style="font-weight: bold; margin-bottom: 10px;">
            M
        </th>
        <th style="font-weight: bold; margin-bottom: 10px;">
            F
        </th>
        <th style="font-weight: bold; margin-bottom: 10px;">
            U
        </th>
        <th style="font-weight: bold; margin-bottom: 20px; width: 200px;">
            Name and Common Name
        </th>
        <th style="font-weight: bold; margin-bottom: 20px; width: 100px;">
            Supplier
        </th>
        <th style="font-weight: bold; margin-bottom: 10px;">
            Continent / Origin / Age
        </th>
        <th style="font-weight: bold; margin-bottom: 10px;">
            Supplier price
        </th>
        <th style="font-weight: bold; margin-bottom: 10px;">
            Profit
        </th>
    </tr>
    </thead>
    <tbody>
    <tr id="spinner" class="d-none">
        <td colspan="9">
            <b>Processing.. <span class="spinner-border spinner-border-sm" role="status"></span></b>
        </td>
    </tr>
    @foreach ($surplus_list as $row)
        @if($date != date("d-m-Y", strtotime($row->created_at)))
            @php($date = date("d-m-Y", strtotime($row->created_at)))
            <tr style="vertical-align: top;">
                <td colspan="9">
                    <b>{{ date("d F Y", strtotime($row->created_at)) }}</b>
                </td>
            </tr>
        @endif
        <tr>
            <td class="pr-0">
                <input type="checkbox" class="selector" onchange="selector()" value="{{ $row->id }}" />
            </td>
            <td style="word-wrap: break-word; white-space:normal;">
                {{ $row->quantityM ?? "" }}
            </td>
            <td style="word-wrap: break-word; white-space:normal;">
                {{ $row->quantityF ?? "" }}
            </td>
            <td style="word-wrap: break-word; white-space:normal;">
                {{ $row->quantityU ?? "" }}
            </td>
            <td style="word-wrap: break-word;  white-space:normal;">
                {{ $row->animal->scientific_name ?? "" }}<br> - {{ $row->animal->common_name ?? "" }}
            </td>
            <td>
                @if ($row->organisation != null)
                    {{ $row->organisation->name  }}
                @elseif ($row->contact != null)
                    {{ $row->contact->full_name }}
                @endif
            </td>
            <td style="word-wrap: break-word;  white-space:normal;">
                @if ($row->country != null && $row->country->region->name === "Europe (Eur. union + Swit + UK)")
                    Europe (EU+)
                @else
                    {{ $row->country->region->name }}
                @endif
                , {{ $row->origin_field ?? ""}}{{ !empty($row->age_field) ? ", " . $row->age_field : "" }}
            </td>
            <td style="word-wrap: break-word;  white-space:normal;">
                <strong>M</strong>: {{ ucfirst($row->cost_currency) ?? ""}} {{ $row->costPriceM ?? ""}} / <strong>F</strong>: {{ ucfirst($row->cost_currency) ?? ""}} {{ $row->costPriceF ?? ""}} / <strong>U</strong>: {{ ucfirst($row->cost_currency) ?? ""}} {{ $row->costPriceU ?? ""}}
            </td>
            <td style="word-wrap: break-word;  white-space:normal;">
                <strong>M</strong>: {{ ucfirst($row->sale_currency) ?? ""}} {{ $row->salePriceM - $row->costPriceM }} / <strong>F</strong>: {{ ucfirst($row->sale_currency) ?? ""}} {{ $row->salePriceF - $row->costPriceF }} / <strong>U</strong>: {{ ucfirst($row->sale_currency) ?? ""}} {{ $row->salePriceU - $row->costPriceU}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
