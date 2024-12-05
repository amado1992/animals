<table>
    <thead>
      <tr>
          <th>Date</th>
          <th>Looking for</th>
          <th>Species</th>
          <th>Origin</th>
          <th>Age</th>
          <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
        @foreach($standard_wanteds as $standard_wanted)
        <tr>
            <td>{{ date('d/m/y', strtotime($standard_wanted->created_at)) }}</td>
            <td>{{ $standard_wanted->looking_field }}</td>
            <td>{{ $standard_wanted->animal->common_name }} ({{ $standard_wanted->animal->scientific_name }})</td>
            <td>{{ $standard_wanted->origin_field }}</td>
            <td>{{ $standard_wanted->age_field }}</td>
            <td>{{ $standard_wanted->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
