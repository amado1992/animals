<table>
    <thead>
      <tr>
          <th>Name</th>
          <th>Iata</th>
          <th>Length</th>
          <th>Wide</th>
          <th>Height</th>
          <th>&nbsp;</th>
          <th>Vol.weight</th>
          <th>Curr</th>
          <th>Cost price</th>
          <th>Sale price</th>
      </tr>
    </thead>
    <tbody>
        @foreach($crates as $crate)
        <tr>
            <td>{{ $crate->name }}</td>
            <td>{{ $crate->iata_code }}</td>
            <td>{{ $crate->length }}</td>
            <td>{{ $crate->wide }}</td>
            <td>{{ $crate->height }}</td>
            <td>cm</td>
            <td>{{ $crate->weight }}</td>
            <td>{{ $crate->currency }}</td>
            <td>{{ $crate->cost_price }}</td>
            <td>{{ $crate->sale_price }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
