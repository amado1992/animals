@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('organisations.index') }}">Institutions</a></li>
    <li class="breadcrumb-item active">Contacts to approve</li>
</ol>
@endsection

@section('header-content')

    <div class="float-right">
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterContactsToApprove">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('contacts-approve.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-tags"></i> Approval options
        </button>
        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
            @foreach ($contact_approved_status as $key => $value)
                <a class="dropdown-item action-all" href="#" code="{{$key}}">{{$value}}</a>
            @endforeach
        </div>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-address-card mr-2"></i> {{ __('Contacts to approve') }}</h1>
    <p class="text-white">Here you can manage the new members of Zoo Services</p>

@endsection

@section('main-content')

    <div class="card shadow mb-2">
        <div class="card-body">
            <div class="d-flex flex-row items-center">
                <div class="d-flex align-items-center">
                    <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
                </div>

                <div class="d-flex align-items-center ml-3">
                    <span class="mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('contacts-approve.removeFromContactToApproveSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @unless($contacts->isEmpty())
        @foreach ($contacts as $contact)
            <div class="card shadow mb-2">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="justify-content-start" style="width: 3%">
                            <div class="mr-2">
                                <input type="checkbox" class="selector" value="{{ $contact->id }}" /><br />

                                <a href="{{ route('contacts-approve.show', [$contact->id]) }}" title="Show contact"><i class="fas fa-search"></i></a><br />

                                <div class="mt-1 mb-1">
                                    <a href="#" id="dropdownMenuButton" title="Approval options" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-tags"></i></a>
                                    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
                                        <h6 class="dropdown-header">Approval options</h6>
                                        @foreach ($contact_approved_status as $key => $value)
                                            <a class="dropdown-item" href="{{ route('contacts-approve.quickApprovalOption', [$contact->id, $key]) }}">{{$value}}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-100">
                            <div class="justify-content-start mb-2" style="width: 100%">
                                <table class="table table-striped table-sm mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th>Institution</th>
                                            <th style="width: 5%">Type</th>
                                            <th>Country</th>
                                            <th>Contact person</th>
                                            <th>Email address</th>
                                            <th>Phone number</th>
                                            <th>Mobile number</th>
                                            <th>Website</th>
                                            <th>Facebook page</th>
                                            <th>Mailing category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="word-wrap: break-word; min-width: 140px;max-width: 140px; white-space:normal;">
                                                @if($contact->organisation) {{ $contact->organisation->name }} @else - @endif
                                            </td>
                                            <td>
                                                @if($contact->organisation) {{ $contact->organisation->type->key }} @else - @endif
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">
                                                @if($contact->country) {{ $contact->country->name }} @else - @endif
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">
                                                {{ $contact->full_name }}
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 120px;max-width: 80px; white-space:normal;">
                                                {{ $contact->email }}
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">
                                                {{ ($contact->organisation != null) ? $contact->organisation->phone : '' }}
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">
                                                {{ $contact->mobile_phone }}
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 160px;max-width: 160px; white-space:normal;">
                                                @if($contact->organisation) <a href="//{{$contact->organisation->website}}" target="_blank"><u>{{ $contact->organisation->website }}</u></a>@else - @endif
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 160px;max-width: 160px; white-space:normal;">
                                                @if($contact->organisation) <a href="//{{$contact->organisation->facebook_page}}" target="_blank"><u>{{ $contact->organisation->facebook_page }}</u></a>@else - @endif
                                            </td>
                                            <td style="word-wrap: break-word; min-width: 80px;max-width: 80px; white-space:normal;">
                                                {{ ($contact->mailing != null) ? $mailing_categories[$contact->mailing_category] : '' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="alert alert-primary mb-0">
                                        <b>Active status: </b>{{ $contact->approved_status }}
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="alert alert-primary mb-0">
                                        <b>Other info: </b>
                                        @if($contact->organisation)
                                            {{ $contact->organisation->short_description }} - {{ $contact->organisation->public_zoos_relation }} - {{ $contact->organisation->animal_related_association }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{$contacts->links()}}
    @else
        <p> No members to approve </p>
    @endunless

    @include('contacts_to_approve.filter_modal', ['modalId' => 'filterContactsToApprove'])

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        $(':checkbox:checked').prop('checked', false);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    $('.action-all').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        var pCode = $(this).attr('code');

        if (ids.length == 0)
            alert("You must select some contacts.");
        else {
            var dropdownMenuButton = $("#dropdownMenuButton").html();
            $("#dropdownMenuButton").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('contacts-approve.quickSelectedApprovalOption') }}",
                data: {
                    items: ids,
                    code: pCode
                },
                success:function(data){
                    $.NotificationApp.send("Success message!", "The approval options was sent correctly", 'top-right', '#fff', 'success');
                    location.reload();
                },
                complete: function(r){
                    $("#dropdownMenuButton").html(dropdownMenuButton);
                }
            });
        }
    });

</script>

@endsection
