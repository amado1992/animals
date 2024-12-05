<table>
    <thead>
      <tr>
          <th>Institution</th>
          <th>Type</th>
          <th>Address</th>
          <th>Country</th>
          <th>City</th>
          <th>Email</th>
          <th>Level</th>
          <th>Phone</th>
          <th>Website</th>
          <th>Facebook</th>
      </tr>
    </thead>
    <tbody>
        @foreach($institutions as $institution)
        <tr>
            <td>{{ (isset($institution->name)) ? $institution->name : '' }}</td>
            <td style="text-align: center;">{{ $institution->type_key }}</td>
            <td>{{ $institution->address }}</td>
            <td>{{ (isset($institution->country)) ? $institution->country : '' }}</td>
            <td>{{ $institution->city  }}</td>
            <td>{{ $institution->email }}</td>
            <td style="text-align: center;">{{ $institution->level }}</td>
            <td>{{ $institution->phone }}</td>
            <td>{{ $institution->website }}</td>
            <td>{{ $institution->facebook_page }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
