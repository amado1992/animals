<table>
    <thead>
      <tr>
          <th>Title</th>
          <th>First name</th>
          <th>Last name</th>
          <th>Institution</th>
          <th>Institution type</th>
          <th>Level</th>
          <th>Address</th>
          <th>Country</th>
          <th>City</th>
          <th>Email</th>
          <th>Mobile phone</th>
          <th>Mailing category</th>
      </tr>
    </thead>
    <tbody>
        @foreach($contacts as $contact)
        <tr>
            <td>{{ $contact->title }}</td>
            <td>{{ $contact->first_name }}</td>
            <td>{{ $contact->last_name }}</td>
            <td>{{ ($contact->organisation) ? $contact->organisation->name : '' }}</td>
            <td>{{ ($contact->organisation && $contact->organisation->type) ? $contact->organisation->type->key : '' }}</td>
            <td>{{ ($contact->organisation) ? $contact->organisation->level : '' }}</td>
            <td>{{ ($contact->organisation) ? $contact->organisation->address : '' }}</td>
            <td>{{ ($contact->country) ? $contact->country->name : '' }}</td>
            <td>{{ $contact->city }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->mobile_phone }}</td>
            <td>{{ $contact->mailing_category }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
