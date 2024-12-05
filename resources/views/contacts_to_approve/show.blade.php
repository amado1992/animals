@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('contacts-approve.index') }}">Contacts to approve</a></li>
    <li class="breadcrumb-item active">{{ $contact->name }}</li>
</ol>
@endsection

@section('header-content')
    <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i>{{ $contact->full_name }}</h1>
@endsection

@section('main-content')

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Contact details</h5>
                <div class="d-inline-block">
                    <a href="{{ route('contacts-approve.edit', [$contact->id]) }}" class="btn btn-dark">
                        <i class="fas fa-fw fa-pen"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold border-top-0">Position:</td>
                        <td class="border-top-0">{{ $contact->position }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Email:</td>
                        <td>{{ $contact->email }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">City:</td>
                        <td>{{ $contact->city }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Country:</td>
                        <td>{{ ($contact->country) ? $contact->country->name : '' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Mobile:</td>
                        <td>{{ $contact->mobile_phone }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Is member:</td>
                        <td>Yes</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Approved status:</td>
                        <td>{{ $contact->getApprovedStatusAttribute() }}</td>
                    </tr>
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
                                <option value="{{ $related_organization->id }}">{{ $related_organization->name }}-{{ is_object($related_organization->type) ? $related_organization->type->key : $related_organization->type["key"] }}</option>
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
            var url = '{{ route("organisations.compare", ["id1", "id2", "contacts_to_approve", "id3"]) }}';
            url = url.replace('id1', contact_organization);
            url = url.replace('id2', selected_organization);
            url = url.replace('id3', contact_id);
            window.location = url;
        }
    });

});

</script>

@endsection
