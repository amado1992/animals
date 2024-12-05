@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('organisations.index') }}">Institutions</a></li>
    <li class="breadcrumb-item active">{{ $contact->name }}</li>
</ol>
@endsection

@section('header-content')
    <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i>{{ $contact->full_name }}</h1>

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
@endsection

@section('main-content')

<div class="row">
    <div class="col-md-7">
        <div class="card shadow mb-2">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Contact details</h5>
                @if ($contact->deleted_at == null)
                    <div class="d-inline-flex">
                        @if (Auth::user()->hasPermission('contacts.update'))
                            <a href="{{ route('contacts.edit', [$contact->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-fw fa-pen"></i> Edit
                            </a>
                        @endif
                        @if (Auth::user()->hasPermission('contacts.delete'))
                            {!! Form::open(['method' => 'DELETE', 'route' => ['contacts.destroy', $contact->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                <a href="#" onclick="$(this).closest('form').submit();" class="btn btn-danger ml-2">
                                    <i class="fas fa-fw fa-window-close"></i> Delete
                                </a>
                            {!! Form::close() !!}
                        @endif
                    </div>
                @else
                    <div>
                        <a href="{{ route('contacts.restoreContactDeleted', $contact->id) }}" class="btn btn-dark ml-2">
                            <i class="fas fa-fw fa-trash-restore"></i> Restore
                        </a>
                    </div>
                @endif
            </div>
            <div class="card-body p-1">
                <table class="table">
                    <tr>
                        <td class="border-top-0" style="width: 33%;">
                            <b>Email:</b><br>
                            <div class="d-inline-flex">
                                <span id="contactEmail"><a href="mailto:{{ $contact->email }}"><u>{{ $contact->email }}</u></a></span>
                                <a href="#" class="ml-2" id="copy-option" title="Copy to Clipboard">
                                    <i class="fas fa-copy"></i>
                                </a>
                            </div>
                        </td>
                        <td class="border-top-0" style="width: 33%;"><b>Mobile:</b><br>{{ $contact->mobile_phone }}</td>
                        <td class="border-top-0" style="width: 33%;"><b>Position:</b><br>{{ $contact->position }}</td>
                    </tr>
                    <tr>
                        <td class="border-top-0" style="width: 33%;"><b>Is member:</b><br>{{ ($contact->source == 'website') ? 'Yes' : 'No' }}</td>
                        @if ($contact->source == 'website')
                            <td class="border-top-0" style="width: 33%;"><b>Approved status:</b><br>{{ $contact->approved_status }}</td>
                        @endif
                        <td class="border-top-0" style="width: 33%;"><b>Mailing category:</b><br>{{ $contact->mailing }}</td>
                    </tr>
                    <tr>
                        <td class="border-top-0" style="width: 33%;"><b>City: </b>{{ $contact->city }}</td>
                        <td class="border-top-0" style="width: 33%;"><b>Country: </b>{{ ($contact->country) ? $contact->country->name : '' }}</td>
                        <td class="border-top-0">
                            <b>Interest in: </b>
                            @foreach($contact->interest_sections as $section)
                                <span>{{ $section->label }}&nbsp;-</span>
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow mb-2">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="email-received-contact-tab" data-toggle="tab" href="#email-received-contact" role="tab" aria-controls="email-received-contact" aria-selected="false">Received emails</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="email-contact-tab" data-toggle="tab" href="#email-contact" role="tab" aria-controls="email-contact" aria-selected="false">Sent Emails</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pending-offers-tab" data-toggle="tab" href="#pending-offers" role="tab" aria-controls="pending-offers" aria-selected="false">Pending offers</a>
                    </li>
                    @if (Auth::user()->hasPermission('orders.read'))
                        <li class="nav-item">
                            <a class="nav-link" id="pending-orders-tab" data-toggle="tab" href="#pending-orders" role="tab" aria-controls="pending-orders" aria-selected="false">Pending orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="realized-orders-tab" data-toggle="tab" href="#realized-orders" role="tab" aria-controls="realized-orders" aria-selected="false">Realized orders</a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade" id="pending-offers" role="tabpanel" aria-labelledby="pending-offers-tab">
                        @unless($contactPendingOffers->isEmpty())
                            <table class="table table-condensed">
                                <thead>
                                    <tr class="table-primary">
                                        <th style="width: 20%;">Req No.</th>
                                        <th style="width: 80%;">Quant. & Species</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($contactPendingOffers as $contactPendingOffer)
                                    <tr>
                                        <td><a href="{{ route('offers.show', $contactPendingOffer->id) }}" title="Show pending offer">{{ $contactPendingOffer->full_number }}</a></td>
                                        <td>
                                            @if ($contactPendingOffer->offer_species->count() == 0)
                                                <span style="color: red;">No species added yet</span>
                                            @else
                                                @foreach ($contactPendingOffer->species_ordered as $species)
                                                    {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $contactPendingOffers->links() }}
                        @else
                            <p>No pending offers related with this contact.</p>
                        @endunless
                    </div>

                    <div class="tab-pane fade show" id="pending-orders" role="tabpanel" aria-labelledby="pending-orders-tab">
                        @unless($contactPendingOrders->isEmpty())
                            <table class="table table-condensed">
                                <thead>
                                    <tr class="table-primary">
                                        <th style="width: 20%;">Order No.</th>
                                        <th style="width: 80%;">Quant. & Species</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($contactPendingOrders as $contactPendingOrder)
                                    <tr>
                                        <td><a href="{{ route('orders.show', $contactPendingOrder->id) }}" title="Show pending order">{{ $contactPendingOrder->full_number }}</a></td>
                                        <td>
                                            @if ($contactPendingOrder->offer->offer_species->count() == 0)
                                                <span style="color: red;">No species added yet</span>
                                            @else
                                                @foreach ($contactPendingOrder->offer->species_ordered as $species)
                                                    {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $contactPendingOrders->links() }}
                        @else
                            <p>No pending orders related with this contact.</p>
                        @endunless
                    </div>

                    <div class="tab-pane fade show" id="realized-orders" role="tabpanel" aria-labelledby="realized-orders-tab">
                        @unless($contactRealizedOrders->isEmpty())
                            <table class="table table-condensed">
                                <thead>
                                    <tr class="table-primary">
                                        <th style="width: 20%;">Order No.</th>
                                        <th style="width: 80%;">Quant. & Species</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($contactRealizedOrders as $contactRealizedOrder)
                                    <tr>
                                        <td><a href="{{ route('orders.show', $contactRealizedOrder->id) }}" title="Show realized order">{{ $contactRealizedOrder->full_number }}</a></td>
                                        <td>
                                            @if ($contactRealizedOrder->offer->offer_species->count() == 0)
                                                <span style="color: red;">No species added yet</span>
                                            @else
                                                @foreach ($contactRealizedOrder->offer->species_ordered as $species)
                                                    {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $contactRealizedOrders->links() }}
                        @else
                            <p>No realized orders related with this contact.</p>
                        @endunless
                    </div>
                    <div class="tab-pane fade show" id="email-contact" role="tabpanel" aria-labelledby="email-contact-tab">
                        @include('inbox.table_show', ['email_show' => $emails])
                    </div>
                    <div class="tab-pane fade show active" id="email-received-contact" role="tabpanel" aria-labelledby="email-received-contact-tab">
                        @include('inbox.table_show', ['email_show' => $emails_received])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        @if ($contact->deleted_at == null && !Auth::user()->hasRole('office'))
            <div class="card shadow mb-2">
                <div class="card-header">
                    <h5>Related institutions</h5>
                </div>
                <div class="card-body pt-1">
                    @if (count($related_organizations) > 0)
                        <p>
                            There are institutions that match with the same name or domain with this one.<br>
                            Do you want to merge?
                        </p>
                        <div>
                            <select class="custom-select custom-select-sm w-50" id="relatedOrganizationSelected" name="relatedOrganizationSelected">
                                <option value="0">--Select institution--</option>
                                @foreach ($related_organizations as $related_organization)
                                    <option value="{{ $related_organization->id }}">{{ ($related_organization->type) ? $related_organization->name .'-'. $related_organization->type->key : $related_organization->name }}</option>
                                @endforeach
                            </select>
                            <a href="#" id="mergeOption" class="btn btn-sm btn-light" contactId="{{$contact->id}}" contactOrganization="{{$contact->organisation->id}}">
                                <i class="fas fa-fw fa-copy"></i> Merge
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Contact institution details</h5>
                @if (Auth::user()->hasPermission('institutions.update') && $contact->organisation != null)
                    <div class="d-inline-block">
                        <a href="{{ route('organisations.show', [$contact->organisation->id]) }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-fw fa-search"></i> Show
                        </a>
                    </div>
                @endif
            </div>
            <div class="card-body p-1">
                @if ($contact->organisation != null)
                    <table class="table">
                        <tr>
                            <td class="border-top-0 font-weight-bold p-1" style="width: 25%;">Name:</td>
                            <td class="border-top-0 p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ $contact->organisation->name }}</td>
                            <td class="border-top-0 font-weight-bold p-1" style="width: 25%;">Type:</td>
                            <td class="border-top-0 p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ ($contact->organisation->type) ? $contact->organisation->type->label : ''}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold p-1">Country:</td>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ $contact->country->name }}</td>
                            <td class="font-weight-bold p-1">City:</td>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ $contact->organisation->city }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold p-1">Address:</td>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ $contact->organisation->address }}</td>
                            <td class="font-weight-bold p-1">Zip code:</td>
                            <td class="p-1">{{ $contact->organisation->zipcode }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold p-1">Main email:</td>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;"><a href="mailto:{{ $contact->organisation->email }}"><u>{{ $contact->organisation->email }}</u></a></td>
                            <td class="font-weight-bold p-1">Domain name:</td>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;">{{ $contact->organisation->domain_name }}</td>
                        </tr>
                        <tr>
                            <th class="font-weight-bold p-1">Website:</th>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;"><a href="//{{$contact->organisation->website}}" target="_blank"><u>{{ $contact->organisation->website }}</u></a></td>
                            <th class="font-weight-bold p-1">Facebook page:</th>
                            <td class="p-1" style="word-wrap: break-word; min-width: 100px;max-width: 100px; white-space:normal;"><a href="//{{$contact->organisation->facebook_page}}" target="_blank"><u>{{ $contact->organisation->facebook_page }}</u></a></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold p-1">Phone:</td>
                            <td class="p-1">{{ $contact->organisation->phone }}</td>
                            <td class="font-weight-bold p-1">Fax:</td>
                            <td class="p-1">{{ $contact->organisation->fax }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold p-1">Vat No.:</td>
                            <td class="p-1">{{ $contact->organisation->vat_number }}</td>
                            <td class="font-weight-bold p-1">Level:</td>
                            <td class="p-1">
                                @if($contact->organisation)
                                    {{ Form::select('level', config("constants.contacts.level_type"), $contact->organisation->level ?? "", array('class' => 'custom-select custom-select-sm form-control form-control-sm display-input-filter level_type level-'. $contact->organisation->level ?? "" .'', 'data_organisation_id' => $contact->organisation->id ?? '', 'data_value_old' => $contact->organisation->level ?? '', 'style' => 'width: 53px;')) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="p-1" colspan="4">
                                <b>Info status:</b>&nbsp;{{ $contact->organisation->info }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-1" colspan="4">
                                <b>Remarks:</b>&nbsp;{{ $contact->organisation->remarks }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-1" colspan="4">
                                <b>Interest in:</b>&nbsp;
                                @foreach($contact->organisation->interest as $section)
                                    <span>{{ $section->label }}&nbsp;-</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="p-1" colspan="4">
                                <b>Associations:</b>&nbsp;
                                @foreach($contact->organisation->associations as $association)
                                    <span>{{ $association->label }}&nbsp;-</span>
                                @endforeach
                            </td>
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

    //Select2 level updated
     $('.level_type').on('change', function () {
        var level = $(this).val();
        var id = $(this).attr("data_organisation_id");
        var value_old = $(this).attr("data_value_old");
        $(this).removeClass("level-" + value_old);
        $(this).addClass("level-" + level);
        $(this).attr("data_value_old", level);
        if(level !== null && id !== null) {
            $.ajax({
                type:'POST',
                url:"{{ route('organisations.editLevel') }}",
                data: {
                    id: id,
                    level: level
                },
                success:function(data) {
                    if(typeof data.message != "undefined"){
                        $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                    }
                }
            });
        }
    });

    // Catch window close
    window.addEventListener('beforeunload', (event) => {
        (event || window.event).preventDefault();
        (event || window.event).returnValue = "Are you sure you want to leave?";
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

    $('#copy-option').on('click', function() {
        var text = $("#contactEmail").text();
        var input = document.body.appendChild(document.createElement("input"));
        input.value = text;
        input.focus();
        input.select();
        document.execCommand('copy');
        input.parentNode.removeChild(input);
    });
});
</script>

@endsection
