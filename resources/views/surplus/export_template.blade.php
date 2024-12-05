<table style="text-align: left">
    <thead>
        <tr>
            <th colspan="7" style="text-align: center;">General Information</th>
            <th colspan="5" style="text-align: center;">Cost prices</th>
            <th style="text-align: center;">Other info</th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Supplier</th>
            <th>Species name</th>
            <th>Species Scientific name</th>
            <th>Quantities</th>
            <th>Origin</th>
            <th>Age</th>
            <th>Curr</th>
            <th>M</th>
            <th>F</th>
            <th>U</th>
            <th>P</th>
            <th>Internal Remarks</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($surpluses as $surplus)
        <tr>
            <td>{{ date('d/m/y', strtotime($surplus->updated_at)) }}</td>
            <td>{{ ($surplus->organisation) ? $surplus->organisation->name : 'Not defined.' }}</td>
            <td>{{ $surplus->animal->common_name }}</td>
            <td>{{ $surplus->animal->scientific_name }}</td>
            <td>{{ $surplus->male_quantity }}-{{ $surplus->female_quantity }}-{{ $surplus->unknown_quantity }}</td>
            <td>{{ $surplus->origin_field }}</td>
            <td>{{ $surplus->age_field }}</td>
            <td>{{ $surplus->cost_currency }}</td>
            <td>{{ number_format($surplus->costPriceM, 2, '.', '') }}</td>
            <td>{{ number_format($surplus->costPriceF, 2, '.', '') }}</td>
            <td>{{ number_format($surplus->costPriceU, 2, '.', '') }}</td>
            <td>{{ number_format($surplus->costPriceP, 2, '.', '') }}</td>
            <td>{{ $surplus->intern_remarks }}</td>
            <td>{{ $surplus->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
