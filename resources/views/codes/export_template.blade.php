<table>
    <thead>
      <tr>
          <th>Site name</th>
          <th>Url</th>
          <th>Remarks</th>
          <th>Login username</th>
          <th>Login password</th>
      </tr>
    </thead>
    <tbody>
        @foreach($codes as $code)
        <tr>
            <td>{{ $code->siteName }}</td>
            <td>{{ $code->siteUrl }}</td>
            <td>{{ $code->siteRemarks }}</td>
            <td>{{ $code->loginUsername }}</td>
            <td>{{ ($code->loginPassword != null) ? Crypt::decryptString($code->loginPassword) : '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
