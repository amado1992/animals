@extends('layouts.admin')

@section('header-content')
    <div class="float-right d-inline-flex">
        <a href="{{ route('contacts-deleted.index') }}" title="Go back to the list" class="btn btn-light">
            <i class="fas fa-fw fa-list"></i>&nbsp;Go to list
        </a>
        <a href="{{ route('contacts-deleted.edit', [$contact->id]) }}" class="btn btn-light ml-2">
            <i class="fas fa-fw fa-pen"></i> Edit contact
        </a>
        {!! Form::open(['method' => 'DELETE', 'route' => ['contacts-deleted.destroy', $contact->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
            <a href="#" onclick="$(this).closest('form').submit();" class="btn btn-light ml-2">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </a>
        {!! Form::close() !!}
    </div>
    @if ($errors->any())
        <div class="row">
            <div class="col-md-6">
                <div class="alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    <p>
        <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i>{{ $contact->full_name }}</h1>
    </p>
@endsection

@section('main-content')

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5>Contact details</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold">Position:</td>
                        <td>{{ $contact->position }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Email:</td>
                        <td>{{ $contact->email }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Mobile:</td>
                        <td>{{ $contact->mobile_phone }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Is member:</td>
                        <td>{{ ($contact->source == 'website') ? 'Yes' : 'No' }}</td>
                    </tr>
                    @if ($contact->source == 'website')
                        <tr>
                            <td class="font-weight-bold">Approved status:</td>
                            <td>{{ $contact->getApprovedStatusAttribute() }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="font-weight-bold">Mailing category:</td>
                        <td>{{ $contact->getMailingAttribute() }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                @if (count($related_organizations) > 0)
                    <h5 style="color: red;">
                        There are institutions that match with the same name or domain with this one.<br>
                        Do you want to merge?
                    </h5>
                    <div class="mb-2">
                        <select class="custom-select w-50" id="relatedOrganizationSelected" name="relatedOrganizationSelected">
                            <option value="0">--Select institution--</option>
                            @foreach ($related_organizations as $related_organization)
                                <option value="{{ $related_organization->id }}">{{ $related_organization->name }}-{{ $related_organization->type->key }}</option>
                            @endforeach
                        </select>
                        <a href="#" id="mergeOption" class="btn btn-light" contactId="{{$contact->id}}" contactOrganization="{{$contact->organisation->id}}">
                            <i class="fas fa-fw fa-copy"></i> Merge
                        </a>
                    </div>
                @endif
                <h5>Contact institution details</h5>
            </div>
            <div class="card-body">
                @if ($contact->organisation != null)
                    <table class="table">
                        <tr>
                            <td class="font-weight-bold">Name:</td>
                            <td>{{ $contact->organisation->name }}</td>
                            <td class="font-weight-bold">Type:</td>
                            <td>{{ $contact->organisation->type->label }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Country:</td>
                            <td>{{ $contact->organisation->country->name }}</td>
                            <td class="font-weight-bold">City:</td>
                            <td>{{ $contact->organisation->city }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Address:</td>
                            <td>{{ $contact->organisation->address }}</td>
                            <td class="font-weight-bold">Zip code:</td>
                            <td>{{ $contact->organisation->zipcode }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Main email:</td>
                            <td>{{ $contact->organisation->email }}</td>
                            <td class="font-weight-bold">Domain name:</td>
                            <td>{{ $contact->organisation->domain_name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Website:</td>
                            <td>{{ $contact->organisation->website }}</td>
                            <td class="font-weight-bold">Facebook page:</td>
                            <td>{{ $contact->organisation->facebook_page }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Main phone:</td>
                            <td>{{ $contact->organisation->phone }}</td>
                            <td class="font-weight-bold">Main fax:</td>
                            <td>{{ $contact->organisation->fax }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Vat number:</td>
                            <td>{{ $contact->organisation->vat_number }}</td>
                            <td class="font-weight-bold">Level:</td>
                            <td>{{ $contact->organisation->level }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Remarks:</td>
                            <td colspan="3">{{ $contact->organisation->remarks }}</td>
                        </tr>
                    </table>
                @else
                    Contact doesn't belong to any institution.
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#mergeOption').on('click', function() {
        var contact_id = $(this).attr('contactId');
        var contact_organization = $(this).attr('contactOrganization');
        var selected_organization = $('#relatedOrganizationSelected').val();

        if(selected_organization == 0)
            alert("You must select an institution to merge.");
        else {
            var url = '{{ route("organisations.compare", ["id1", "id2", "contacts", "id3"]) }}';
            url = url.replace('id1', contact_organization);
            url = url.replace('id2', selected_organization);
            url = url.replace('id3', contact_id);
            window.location = url;
        }
    });

});

</script>

@endsection
