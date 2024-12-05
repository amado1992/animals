<table>
    <thead>
      <tr>
          <th>ID</th>
          <th>EMAIL</th>
          <th>FULLNAME</th>
      </tr>
    </thead>
    <tbody>
        @foreach($surpluses as $key => $surplus)
            @if(!empty($surplus->organisation))
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $surplus->organisation["name"] ?? "" }}</td>
                    <td>{{ $surplus->organisation["email"] ?? "" }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
