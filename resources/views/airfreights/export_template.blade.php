<table>
    <thead>
      <tr>
          <th>Offered date</th>
          <th>Departure continent</th>
          <th>Arrival continent</th>
          <th>Curr</th>
          <th>Vol. kg value</th>
          <th>Vol. kg cost</th>
          <th>Lowerdeck value</th>
          <th>Lowerdeck cost</th>
          <th>Maindeck value</th>
          <th>Maindeck cost</th>
          <th>Transport agent</th>
          <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
        @foreach($airfreights as $airfreight)
        <tr>
            <td>{{ date('d/m/y', strtotime($airfreight->offered_date)) }}</td>
            <td>{{ ($airfreight->from_continent) ? $airfreight->from_continent->name : '' }}</td>
            <td>{{ ($airfreight->to_continent) ? $airfreight->to_continent->name : '' }}</td>
            <td>{{ $airfreight->currency }}</td>
            <td>{{ $airfreight->volKg_weight_value }}</td>
            <td>{{ $airfreight->volKg_weight_cost }}</td>
            <td>{{ $airfreight->lowerdeck_value }}</td>
            <td>{{ $airfreight->lowerdeck_cost }}</td>
            <td>{{ $airfreight->maindeck_value }}</td>
            <td>{{ $airfreight->maindeck_cost }}</td>
            <td>{{ ($airfreight->agent) ? $airfreight->agent->email : '' }}</td>
            <td>{{ $airfreight->remarks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
