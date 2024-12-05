<table>
    <thead>
      <tr>
          <th>Common name</th>
          <th>Common name 2</th>
          <th>Scientific name</th>
          <th>Scientific name 2</th>
          <th>Iata</th>
          <th>Cites global</th>
          <th>Cites europe</th>
          <th>Body weight</th>
      </tr>
    </thead>
    <tbody>
        @foreach($animals as $animal)
        <tr>
            <td>{{ $animal->common_name }}</td>
            <td>{{ $animal->common_name_alt }}</td>
            <td>{{ $animal->scientific_name }}</td>
            <td>{{ $animal->scientific_name_alt }}</td>
            <td>{{ $animal->iata_code }}</td>
            <td>{{ (isset($animal->cites_global)) ? $animal->cites_global->key : '' }}</td>
            <td>{{ (isset($animal->cites_europe)) ? $animal->cites_europe->key : '' }}</td>
            <td>{{ $animal->body_weight }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
