<table>
    <thead>
      <tr>
          <th>Date</th>
          <th>Client institution</th>
          <th>Looking for</th>
          <th>Species</th>
          <th>Origin</th>
          <th>Age</th>
          <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
        @foreach($wanteds as $wanted)
        <tr>
            <td>{{ date('d/m/y', strtotime($wanted->created_at)) }}</td>
            <td>{{ ($wanted->organisation) ? $wanted->organisation->name : 'Not defined.' }}</td>
            <td>{{ $wanted->looking_field }}</td>
            <td>{{ $wanted->animal->common_name }} ({{ $wanted->animal->scientific_name }})</td>
            <td>{{ $wanted->origin_field }}</td>
            <td>{{ $wanted->age_field }}</td>
            <td>{{ $wanted->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
