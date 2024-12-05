<table>
    <thead>
        <tr>
            <th colspan="5">General Information</th>
            @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                <th colspan="5">Cost prices</th>
            @endif
            @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                <th colspan="5">Sales prices</th>
            @endif
            <th></th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Species</th>
            <th>Availability</th>
            <th>Origin</th>
            <th>Age</th>
            @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                <th>Curr</th>
                <th>M</th>
                <th>F</th>
                <th>U</th>
                <th>P</th>
            @endif
            @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                <th>Curr</th>
                <th>M</th>
                <th>F</th>
                <th>U</th>
                <th>P</th>
            @endif
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stockRecords as $ourSurplus)
        <tr>
            <td>{{ date('d/m/y', strtotime($ourSurplus->updated_at)) }}</td>
            <td>{{ $ourSurplus->animal->common_name }} ({{ $ourSurplus->animal->scientific_name }})</td>
            <td>{{ $ourSurplus->availability_field }}</td>
            <td>{{ $ourSurplus->origin_field }}</td>
            <td>{{ $ourSurplus->age_field }}</td>
            @if (Auth::user()->hasPermission('standard-surplus.see-cost-prices'))
                <td>{{ $ourSurplus->cost_currency }}</td>
                <td>{{ number_format($ourSurplus->costPriceM, 2, '.', '') }}</td>
                <td>{{ number_format($ourSurplus->costPriceF, 2, '.', '') }}</td>
                <td>{{ number_format($ourSurplus->costPriceU, 2, '.', '') }}</td>
                <td>{{ number_format($ourSurplus->costPriceP, 2, '.', '') }}</td>
            @endif
            @if (Auth::user()->hasPermission('standard-surplus.see-sale-prices'))
                <td>{{ $ourSurplus->sale_currency }}</td>
                <td>{{ number_format($ourSurplus->salePriceM, 2, '.', '') }}</td>
                <td>{{ number_format($ourSurplus->salePriceF, 2, '.', '') }}</td>
                <td>{{ number_format($ourSurplus->salePriceU, 2, '.', '') }}</td>
                <td>{{ number_format($ourSurplus->salePriceP, 2, '.', '') }}</td>
            @endif
            <td>{{ $ourSurplus->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
