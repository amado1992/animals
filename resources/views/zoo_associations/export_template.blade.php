<table>
    <thead>
      <tr>
          <th>Area</th>
          <th>Name</th>
          <th>Website</th>
          <th>Status</th>
          <th>Started on</th>
          <th>Remark</th>
          <th>Checked on</th>
          <th>Checked by</th>
      </tr>
    </thead>
    <tbody>
      @foreach( $zooAssociations as $za )
      <tr>
          <td>{{ $za->area }}</td>
          <td>{{ $za->name }}</td>
          <td>{{ $za->website }}</td>
          <td>{{ $za->status }}</td>
          <td>{{ $za->started_date }}</td>
          <td>{{ $za->remark }}</td>
          <td>{{ $za->checked_date }}</td>
          <td>{{ ($za->user != null) ? $za->user->name : "" }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
