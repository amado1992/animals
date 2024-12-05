<table>
    <thead>
      <tr>
          <th>Inbox No.</th>
          <th>Name</th>
          <th>Email</th>
      </tr>
    </thead>
    <tbody>
        @php
            $count = 1;
        @endphp
        @foreach($emails as $emailsByMonth)
            @foreach ($emailsByMonth as $email)
                @foreach ($email as $key => $sub_email)
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{{ $sub_email->name }}</td>
                        <td>{{ $sub_email->from_email }}</td>
                    <tr>
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>
