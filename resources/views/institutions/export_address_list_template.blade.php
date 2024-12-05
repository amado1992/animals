<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>EMAIL</th>
        <th>FULLNAME</th>
        <th>TYPE</th>
        <th>COUNTRY</th>
        <th>REGION</th>
    </tr>
    </thead>
    <tbody>
    @foreach($institutions as $key => $institution)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $institution->email }}</td>
            @if (!empty($institution->name) && $institution->name != " ")
                <td>{{ $institution->name}}</td>
            @else
                <td>{{ "Mr./Mrs." }}</td>
            @endif
            <td>{{ $institution->organisation_type }}</td>
            <td>{{ $institution->country }}</td>
            <td>{{ $institution->region }}</td>
        </tr>
    @endforeach
    <tr>
        <td>{{ $key + 2 }}</td>
        <td>johnrens@zoo-services.com</td>
        <td>John Rens</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>{{ $key + 3 }}</td>
        <td>marlies@zoo-services.com</td>
        <td>Marlies</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>
