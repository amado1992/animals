<table>
    <thead>
      <tr>
          <th>NO</th>
          <th>EMAIL</th>
          <th>NAME</th>
      </tr>
    </thead>
    <tbody>
        @foreach($institutions as $key => $institution)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $institution["email"] }}</td>
            <td>{{ $institution["name"] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
