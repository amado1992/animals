<table>
    <thead>
      <tr>
          <th>ID</th>
          <th>EMAIL</th>
          <th>FULLNAME</th>
      </tr>
    </thead>
    <tbody>
        @foreach($contacts as $key => $contact)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->letter_name }}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{ $key + 2 }}</td>
            <td>johnrens@zoo-services.com</td>
            <td>John Rens</td>
        </tr>
    </tbody>
</table>
