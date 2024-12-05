<div class="row">
    @if(!empty($email->contact))
        <div class="col-md-6">
            <h5>Contact details</h5>
            <table class="table">
                <tr>
                    <td class="border-top-0" style="width: 50%;"><b>Name:</b><br>{{ $email->contact->first_name ?? "" }} {{ $email->contact->last_name ?? "" }}</td>
                    <td class="border-top-0" style="width: 50%;">
                        <b>Email:</b><br><a href="{{  route('contacts.show', [$email->contact_id]) }}" class="title" style="color: #13769dd1;">{{ $email->from_email }}</a>
                    </td>
                </tr>
                <tr>
                    <td class="border-top-0" style="width: 50%;"><b>City: </b>{{ $email->contact->city }}</td>
                    <td class="border-top-0" style="width: 50%;"><b>Country: </b>{{ ($email->contact->country) ? $email->contact->country->name : '' }}</td>
                </tr>
            </table>
        </div>
    @endif
    @if(!empty($email->organisation))
        <div class="col-md-6">
            <h5>Institution details</h5>
            <table class="table">
                <tr>
                    <td class="border-top-0" style="width: 50%;"><b>Name:</b><br>{{ $email->organisation->name }}</td>
                    <td class="border-top-0" style="width: 50%;">
                        <b>Email:</b><br><a href="{{  route('organisations.show', [$email->organisation_id]) }}" class="title" style="color: #13769dd1;">{{ $email->from_email }}</a>
                    </td>
                </tr>
                <tr>
                    <td class="border-top-0" style="width: 50%;"><b>City: </b>{{ $email->organisation->city }}</td>
                    @if(!empty($email->contact))
                        <td class="border-top-0" style="width: 50%;"><b>Country: </b>{{ ($email->contact->country) ? $email->contact->country->name : '' }}</td>
                    @endif
                </tr>
            </table>
        </div>
    @endif
</div>
