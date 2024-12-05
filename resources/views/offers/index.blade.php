@extends('layouts.admin')

@section('header-content')

    <div class="float-right action-button">
        @if (Auth::user()->hasPermission('offers.create'))
            <a href="{{ route('offers.create') }}" class="btn btn-light">
                <i class="fas fa-fw fa-plus"></i> New
            </a>
        @endif
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterOffers">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('offers.showAll', session('offer_status')) }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        @if (Auth::user()->hasPermission('offers.update'))
            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                <i class="fas fa-fw fa-edit"></i> Edit selection
            </button>
        @endif

        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-tags"></i> Actions
        </button>
        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item action-all" code="send_all" href="#">Send selected offers</a>
            <a class="dropdown-item action-all" code="remind_all" href="#">Remind selected offers</a>
        </div>

        @if (Auth::user()->hasPermission('offers.delete'))
            <button type="button" id="deleteSelectedItems" class="btn btn-light">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        @endif
        @if (Auth::user()->hasPermission('offers.export-survey'))
            <a id="exportOffersRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportOffers">
                <i class="fas fa-fw fa-save"></i> Export
            </a>
        @endif
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-file-alt mr-2"></i> Inquiries/Offers</h1>

    <div class="d-flex flex-row items-center mb-1 filter-offer">
        <div class="d-flex align-items-center mr-2">
            <label class="text-white  mr-1">Select all:</label>
            <input type="checkbox" style="margin: -6px 0 0 0;" id="selectAll" name="selectAll" />
            <input type="hidden" id="countOffersVisible" value="{{ $offers->count() }}" />
        </div>
        <div class="d-flex align-items-center mr-2">
            <label class="text-white  mr-1">Status:</label>
            {!! Form::open(['id' => 'statusForm', 'route' => 'offers.offersWithStatus', 'method' => 'GET']) !!}
                <select class="custom-select custom-select-sm w-auto" id="statusField" name="statusField">
                    @foreach ($offerStatuses as $statusKey => $statusValue)
                        <option value="{{ $statusKey }}" @if(isset($statusField) && $statusField == $statusKey) selected @endif>{{$statusValue}}</option>
                    @endforeach
                </select>
            {!! Form::close() !!}
        </div>
        <div class="d-flex align-items-center mr-2">
            <label class="text-white  mr-1">Level:</label>
            {!! Form::open(['id' => 'statusLevelForm', 'route' => 'offers.offersWithStatusLevel', 'method' => 'GET']) !!}
            <select class="custom-select custom-select-sm w-auto" id="statusLevelField" name="statusLevelField">
                @foreach ($offerStatusesLevel as $statusLevelKey => $statusLevelValue)
                    <option value="{{ $statusLevelKey }}" @if(isset($statusLevelField) && $statusLevelField == $statusLevelKey) selected @endif>{{$statusLevelValue}}</option>
                @endforeach
            </select>
            {!! Form::close() !!}
        </div>
        <div class="d-flex align-items-center mr-2">
            <label class="text-white mr-1">Order by:</label>
            {!! Form::open(['id' => 'offersOrderByForm', 'route' => 'offers.orderBy', 'method' => 'GET']) !!}
                <select class="custom-select custom-select-sm w-auto" id="orderByField" name="orderByField">
                    @foreach ($orderByOptions as $orderByKey => $orderByValue)
                        <option value="{{ $orderByKey }}" @if(isset($orderByField) && $orderByField == $orderByKey) selected @endif>{{$orderByValue}}</option>
                    @endforeach
                </select>
                <select id="orderByDirection" name="orderByDirection" class="custom-select custom-select-sm w-auto">
                    <option @if(!isset($orderByDirection)) selected @endif value="desc">Descending</option>
                    <option @if(isset($orderByDirection) && $orderByDirection == 'asc') selected @endif value="asc">Ascending</option>
                </select>
            {!! Form::close() !!}
        </div>
        <div class="d-flex align-items-center">
            @foreach ($filterData as $key => $value)
                <a href="{{ route('offers.removeFromOfferSession', $key) }}" class="btn btn-sm btn-light btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
            @endforeach
        </div>
    </div>
@endsection

@section('main-content')

<div class="card shadow mb-2">
    <div class="card-body flex-offer">
        <div class="d-flex mb-2" style="text-align: center; margin: -11px 0 0 0;">
            <div class="justify-content-start" style="width: 5%">
            </div>
            <div class="w-100">
                <div class="d-flex flex-wrap justify-content-start align-items-center" style="margin: -12px 0 0 0;">
                    <div style="flex-grow: 1" class="d-flex justify-content-start">
                        <div class="mr-2" style="width: 50%;">
                            <div class="row">
                                <div class="col">
                                    <b>Status</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-2" style="width: 50%;">
                            <div class="row">
                                <div class="col">
                                    <b>Number</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-2" style="width: 100%;">
                            <div class="row">
                                <div class="col">
                                    <b>Quant. & Species</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-5" style="width: 100%;">
                            <div class="row">
                                <div class="col">
                                    <b>Client</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-5" style="width: 100%;">
                            <div class="row">
                                <div class="col">
                                    <b>Supplier</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-2" style="width: 35%;">
                            <div class="row">
                                <div class="col">
                                    <b>Manager</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-2" style="width: 50%;">
                            <div class="row">
                                <div class="col">
                                    <b>Total species</b>
                                </div>
                            </div>
                        </div>

                        <div class="mr-2" style="width: 25%;">
                            <div class="row">
                                <div class="col">
                                    <b>Tasks</b>
                                </div>
                            </div>
                        </div>

                        <div style="width: 36%;">
                            <div class="row">
                                <div class="col">
                                    <b>Documents</b>
                                </div>
                            </div>
                        </div>
                        <div style="width: 51%;">
                            <div class="row">
                                <div class="col">
                                    <b>Sent</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($offers as $offer)
            <div class="d-flex d-flex-offer" style="text-align: left;">
                <div class="justify-content-start" style="width: 5%">
                    <div class="d-inline align-items-center mr-3">
                        <input type="checkbox" class="selector mr-1" value="{{ $offer->offerId }}" />
                        <a href="{{ route('offers.show', $offer->offerId) }}" class="mr-1" title="See details"><i class="fas fa-search"></i></a>
                    </div>
                    <div class="mt-1">
                        <div class="row">
                            <div class="col-md-1" style="margin: 0 0 0 8px;">
                                @if ($offer->should_be_reminded && $offer->times_reminded == 1)
                                    <i class="fas fa-stop-circle text-primary" title="Offer already reminded. Check offer status."></i>
                                @elseif ($offer->should_be_reminded)
                                    <i class="fas fa-bell text-danger" title="Remind offer before {{ $offer->next_reminder_at }}"></i>
                                @endif
                            </div>
                            <div class="col-md-6" style="margin: 5px 0 0 -9px;">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin: -13px 0 0 0;">
                                    <i class="fas fa-tags"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
                                    @if($offer->offer_status != 'Cancelled')
                                        <h6 class="dropdown-header">Email options</h6>
                                        @if($offer->offer_status != 'Inquiry' && Auth::user()->hasPermission('offers.send-offer'))
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'send_offer', 'main']) }}">Send offer</a>
                                        @endif
                                        @if($offer->offer_status === 'Pending' && $offer->should_be_reminded && $offer->times_reminded == 0 && Auth::user()->hasPermission('offers.send-reminders'))
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'remind_' . ($offer->times_reminded + 1), 'main']) }}">Remind offer ({{ $offer->times_reminded + 1 }})</a>
                                        @endif
                                        @if (Auth::user()->hasPermission('offers.other-email-options'))
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'not_available', 'main']) }}">Not available</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'special_conditions', 'main']) }}">Special conditions</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_approve', 'main']) }}">Offer to approve</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'transport_quotation', 'main']) }}">Freight application</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_approve_by_john', 'main']) }}">To approve by John</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Generate PDF</h6>
                                        @if (Auth::user()->hasPermission('offers.offer-document'))
                                            <a class="dropdown-item" href="{{ route('offers.create_offer_pdf', [$offer->offerId, 0, 'main']) }}">Offer</a>
                                            <a class="dropdown-item" href="{{ route('offers.create_offer_pdf', [$offer->offerId, 1, 'main']) }}">Offer x-x-x</a>
                                        @endif
                                        @if (Auth::user()->hasPermission('offers.calculation-document'))
                                            <a class="dropdown-item" href="{{ route('offers.create_offer_calculation_pdf', [$offer->offerId, 'offers_main']) }}">Calculation details</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    @if($offer->offer_status === 'Ordered' && !empty($offer->order))
                                        <a class="dropdown-item" href="{{ route('orders.show', $offer->order->id) }}" title="Go to order"><i class="fas fa-arrow-right"></i> Go to order</a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    @if (Auth::user()->hasPermission('offers.delete'))
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['offers.destroy', $offer->offerId], 'onsubmit' => 'return confirm("Are you sure to delete this project?")']) !!}
                                            <button class="dropdown-item"><i class="fas fa-trash text-danger"></i> Delete offer</button>
                                        {!! Form::close() !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100
                    @if($offer->should_be_reminded && $offer->times_reminded == 1 )
                        text-black
                    @elseif($offer->should_be_reminded)
                        text-danger
                    @endif">
                    <div class="d-flex flex-wrap justify-content-start align-items-center">
                        <div style="flex-grow: 1" class="d-flex justify-content-start">
                            <div class="mr-2" style="width: 50%;">
                                <div class="row">
                                    <div class="col">
                                        {{ $offer->offer_status }}
                                        @if(!empty($offer->status_level))
                                            @if ($offer->status_level === "Forapproval")
                                                (For approval)
                                            @elseif ($offer->status_level === "Tosearch")
                                                (To search)
                                            @elseif ($offer->status_level === "Sendoffer")
                                                (Send Offer)
                                            @else
                                                ({{$offer->status_level}})
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 50%;">
                                <div class="row">
                                    <div class="col">
                                        <span>{{ $offer->full_number }}</span><br>
                                        <span>{{ $offer->offer_type }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 100%;">
                                <div class="row">
                                    <div class="col">
                                        @if (count($offer->species_ordered) == 0)
                                            <span style="color: red;">No species added yet</span>
                                        @elseif (count($offer->species_ordered) == 1)
                                            <p>
                                                @foreach ($offer->species_ordered as $species)
                                                    {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                                        {{$species->oursurplus->animal->common_name}}<br> ({{$species->oursurplus->animal->scientific_name}})
                                                    @else
                                                        ERROR - NO STANDARD SURPLUS
                                                    @endif
                                                @endforeach
                                            </p>
                                        @elseif(count($offer->species_ordered) > 1)
                                            @php
                                                $species = $offer->species_ordered[0];
                                            @endphp
                                            <p>{{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                                @if ($species->oursurplus && $species->oursurplus->animal)
                                                    {{$species->oursurplus->animal->common_name}}<br> ({{$species->oursurplus->animal->scientific_name}})
                                                @else
                                                    ERROR - NO STANDARD SURPLUS
                                                @endif
                                            </p>
                                            <p class="modal-toggle see-more" style="margin: -16px 0 4px 0; font-weight: 700;">See More</p>
                                            <div style="display: none" class="hidden-info">
                                                <table style="width: 100%;">
                                                    <tbody>
                                                        @foreach ($offer->species_ordered as $species)
                                                            <tr>
                                                                <td>
                                                                    {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                                                    @if ($species->oursurplus && $species->oursurplus->animal)
                                                                        {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                                                    @else
                                                                        ERROR - NO STANDARD SURPLUS
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($species->oursurplus && $species->oursurplus->sale_currency)
                                                                        {{ $species->oursurplus->sale_currency }}
                                                                    @else
                                                                        ERROR - NO STANDARD SURPLUS SALE CURRENCY
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ number_format($species->total_sales_price, 2, '.', '') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-5" style="width: 100%; margin: 0 0 5px 0;">
                                <div class="row">
                                    <div class="col">
                                    @if ($offer->client)
                                        {{ ($offer->client->organisation && $offer->client->organisation->name) ? $offer->client->organisation->name : $offer->client->full_name }}<br>
                                        @if (!empty($offer->client->email))
                                            ({{ $offer->client->email }})
                                        @else
                                            ({{ $offer->client->organisation->email ?? "" }})
                                        @endif
                                        <br>
                                        {{ !empty($offer->client->country) ? $offer->client->country->name : '' }}
                                    @elseif($offer->organisation)
                                        {{ ($offer->organisation && $offer->organisation->name) ? $offer->organisation->name : "" }}<br>
                                        ({{ $offer->organisation->email ?? "" }})<br>
                                        {{ !empty($offer->organisation->country) ? $offer->organisation->country->name : '' }}
                                    @else
                                        <p>No client added yet.</p>
                                    @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-5" style="width: 100%; margin: 0 0 5px 0;">
                                <div class="row">
                                    <div class="col">
                                        @if ($offer->supplier)
                                            {{ ($offer->supplier->organisation && $offer->supplier->organisation->name) ? $offer->supplier->organisation->name : $offer->supplier->full_name }}<br>
                                            ({{ $offer->supplier->email }})<br>
                                            {{ !empty($offer->supplier->country) ? $offer->supplier->country->name : '' }}
                                        @else
                                            International Zoo Services
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 35%;">
                                <div class="row">
                                    <div class="col">
                                        @if(!empty($offer->manager))
                                            {{ $offer->manager->name }} {{ $offer->manager->last_name }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 50%;">
                                <div class="row">
                                    <div class="col">
                                        @if (count($offer->species_ordered) == 1)
                                            @foreach ($offer->species_ordered as $species)
                                                {{ ($species->oursurplus && $species->oursurplus->sale_currency) ? $species->oursurplus->sale_currency : 'ERROR' }} {{ number_format($species->total_sales_price, 2, '.', '') }}<br>
                                            @endforeach
                                        @elseif(count($offer->species_ordered) > 1)
                                            @php
                                                $species = $offer->species_ordered[0];
                                            @endphp
                                            {{ ($species->oursurplus && $species->oursurplus->sale_currency) ? $species->oursurplus->sale_currency : 'ERROR' }} {{ number_format($species->total_sales_price, 2, '.', '') }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 25%;">
                                <div class="row">
                                    <div class="col">
                                        @unless($offer->offer_today_tasks->isEmpty() && $offer->offer_other_tasks->isEmpty())
                                            <p>Yes</p>
                                        @else
                                            <p>No</p>
                                        @endunless
                                    </div>
                                </div>
                            </div>

                            <div style="width: 36%;">
                                <div class="row">
                                    <div class="col">
                                        @foreach($offer->all_docs as $doc)
                                            @php
                                                $file = pathinfo($doc);
                                            @endphp
                                            <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/'.$file['basename'])}}" target="_blank">Offer.pdf</a><br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div style="width: 51%;">
                                <div class="row">
                                    <div class="col">
                                        @if ($offer->date_send_offer)
                                            {{ $offer->date_send_offer }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="new-modal">
            <div class="modal-overlay modal-toggle"></div>
            <div class="modal-wrapper modal-transition">
                <div class="modal-header">
                    <button class="modal-close modal-toggle">
                        &#10005;
                    </button>
                    <h2 class="modal-heading">more info</h2>
                </div>
                <div class="modal-body">
                    <div class="modal-content">
                        <div class="modal-content-p"></div>
                    </div>
                </div>
            </div>
        </div>
        {{$offers->links()}}
    </div>
</div>

@include('offers.filter_modal', ['modalId' => 'filterOffers'])

@include('offers.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

@include('export_excel.export_options_modal', ['modalId' => 'exportOffers'])

@include('offers.email_preview_modal', ['modalId' => 'emailPreview'])

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    var emailPreviewEditor = '';

    var config = {
    // Define the toolbar groups as it is a more accessible solution.
    toolbarGroups: [{
            "name": "document",
            "groups": ["mode"]
            },
            {
            "name": "basicstyles",
            "groups": ["basicstyles"]
            },
            {
            "name": "links",
            "groups": ["links"]
            },
            {
            "name": "paragraph",
            "groups": ["list", "align"]
            },
            {
            "name": "insert",
            "groups": ["insert"]
            },
            {
            "name": "styles",
            "groups": ["styles"]
            },
            {
            "name": "colors",
            "groups": ["colors"]
            }
        ],
        extraPlugins: 'stylesheetparser',
        height: 200,
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
    };

    // see more modal
    $('.modal-toggle').on('click', function(e) {
        e.preventDefault();

        let text = $(this).parent().find('.hidden-info').html()
        $('.modal-content-p').html(text)
        $('.new-modal').toggleClass('is-visible');
    });

   $(".see-more-task").on('click', function(e) {
        e.preventDefault();
        let text = $("#tableSeeTaskBody").html();
        $('.modal-content-p').html(text);
        $('.new-modal').toggleClass('is-visible');
   });

    $(':checkbox:checked.selector').prop('checked', false);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    $('#filterOffers').on('hidden.bs.modal', function () {
        $("#filterOffers .animal-select2").val(null).trigger('change');
        $("#filterOffers .contact-select2").val(null).trigger('change');
        $(this).find('form').trigger('reset');
    });

    //Select2 filter animal
    $('#filterOffers [name=filter_animal_id]').on('change', function () {
        var animalId = $(this).val();

        if(animalId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.animal-by-id') }}",
                data: {
                    id: animalId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.animal.common_name.trim(), data.animal.id, true, true);
                    // Append it to the select
                    $('#filterOffers [name=filter_animal_id]').append(newOption);
                }
            });
        }
    });

    $('#editSelectedRecords').on('show.bs.modal', function () {
        $('#editSelectedRecords [name=edit_offer_status]').trigger('change');
    });

    $('#editSelectedRecords').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#sendEditSelectionForm').on('click', function(event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select offers to edit.");
        else {
            var modal_footer = $('#editSelectedRecords .modal-footer').html();
            $('#editSelectedRecords .modal-footer').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            var statusLevel = $('#editSelectedRecords [name=edit_offer_status_level]').val();
            if (statusLevel === "")
              statusLevel = "Inquiry";

            $.ajax({
                type:'POST',
                url:"{{ route('offers.editSelectedRecords') }}",
                data:{
                    items: ids,
                    offer_status: $('#editSelectedRecords [name=edit_offer_status]').val(),
                    status_level: statusLevel,
                    offer_currency: $('#editSelectedRecords [name=edit_offer_currency]').val(),
                    offer_type: $('#editSelectedRecords [name=edit_offer_type]').val(),
                    client_id: $('#editSelectedRecords [name=edit_selection_client_id]').val(),
                    supplier_id: $('#editSelectedRecords [name=edit_selection_supplier_id]').val(),
                    manager_id: $('#editSelectedRecords [name=edit_offer_manager]').val()
                },
                success:function(data){
                    $('#editSelectedRecords .modal-footer').html(modal_footer);
                    $.NotificationApp.send("Success message!", "The offer information was successfully updated", 'top-right', '#5ba035', 'success');
                    location.reload();
                }
            });
        }
    });

    $('.action-all').on('click', function(event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select offers.");
        else {
            var dropdownMenuButton = $("#dropdownMenuButton").html();
            $("#dropdownMenuButton").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.selectedOffersAction') }}",
                data:{
                    items: ids,
                    code: $(this).attr('code')
                },
                success:function(data) {
                    if (data.message){
                        alert(data.message);
                    }else{
                        $.NotificationApp.send("Success message!", "The mail of the selected offers was sent correctly", 'top-right', '#fff', 'success');
                        location.reload();
                    }
                },
                complete: function() {
                    $("#dropdownMenuButton").html(dropdownMenuButton);
                },
            });
        }
    });

    $('#statusField').on('change', function () {
        $('#statusForm').submit();
    });

    $('#statusLevelField').on('change', function () {
        $('#statusLevelForm').submit();
    });

    $('#orderByField').on('change', function () {
        $('#offersOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#offersOrderByForm').submit();
    });

    $('#deleteSelectedItems').on('click', function () {
        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select items to delete.");
        else if(confirm("Are you sure that you want to delete the selected projects?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.deleteItems') }}",
                data:{items: ids},
                success:function(data) {
                    if(!data.success)
                        alert(data.warning_msg);
                    location.reload();
                }
            });
        }
    });

    $('#exportOffersRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countOffersVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportOffers').modal('show');
    });

    $('#exportOffers').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportOffers [name=export_option]:checked').val();

        var ids = [];
        if(export_option == "selection") {
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }
        else {
            $(".selector").each(function(){
                ids.push($(this).val());
            });
        }

        if(ids.length == 0)
            alert("There are not records to export.");
        else {
            var url = "{{route('offers.export')}}?items=" + ids;
            window.location = url;

            $('#exportOffers').modal('hide');
        }
    });

});

</script>

@endsection
