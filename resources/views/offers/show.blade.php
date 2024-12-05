@extends('layouts.admin')

@section('page-css')
    <link href="{{ asset('css/stylesTables.css') }}" rel="stylesheet">
@endsection
@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('offers.index') }}">Offers</a></li>
        <li class="breadcrumb-item active">{{ $offer->full_number }}</li>
        <li class="text-white ml-3 active" style="font-size: 16px;">Manager, {{ !empty($offer->manager) ? $offer->manager->name . " " . $offer->manager->last_name  : "" }}</li>
    </ol>
    {!! Form::hidden('selectedOfferTab', $selectedOfferTab) !!}
@endsection

@section('header-content')
    <div class="w-100 mt-n2 mb-2">
        <div class="d-flex flex-wrap">
            <div style="width: 22%;">
                <div class="card border-left-success shadow h-100">
                    <div class="card-header pb-1">
                        <div class="d-flex">
                            @if (Auth::user()->hasPermission('offers.update'))
                                <a href="#" class="btn btn-sm btn-dark" title="Edit offer" data-toggle="modal" data-target="#editModal"><i class="fas fa-edit"></i> Edit</a>
                            @endif
                            <div class="dropdown ml-2">
                                <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
                                    @if($offer->offer_status != 'Cancelled')
                                        <h6 class="dropdown-header">Email options</h6>
                                        @if($offer->offer_status != 'Inquiry' && Auth::user()->hasPermission('offers.send-offer'))
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'send_offer', 'details']) }}">Send offer</a>
                                        @endif
                                        @if($offer->offer_status === 'Pending' && $offer->should_be_reminded && $offer->times_reminded == 0 && Auth::user()->hasPermission('offers.send-reminders'))
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'remind_' . ($offer->times_reminded + 1), 'details']) }}">Remind offer ({{ $offer->times_reminded + 1 }})</a>
                                        @endif
                                        @if (Auth::user()->hasPermission('offers.other-email-options'))
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'not_available', 'details']) }}">Not available</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'special_conditions', 'details']) }}">Special conditions</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_approve', 'details']) }}">Offer to approve</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'transport_quotation', 'details']) }}">Freight application</a>
                                            <a class="dropdown-item" href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_approve_by_john', 'details']) }}">To approve by John</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Generate PDF</h6>
                                        @if (Auth::user()->hasPermission('offers.offer-document'))
                                            <a class="dropdown-item" href="{{ route('offers.create_offer_pdf', $offer->offerId) }}">Offer</a>
                                            <a class="dropdown-item" href="{{ route('offers.create_offer_pdf', [$offer->offerId, true]) }}">Offer x-x-x</a>
                                        @endif
                                        @if (Auth::user()->hasPermission('offers.calculation-document'))
                                            <a class="dropdown-item" href="{{ route('offers.create_offer_calculation_pdf', [$offer->offerId, 'offer_details']) }}">Calculation details</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    @if($offer->offer_status === 'Ordered')
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
                    <div class="card-body p-2">
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-6">
                                <b>Number: </b>{{ $offer->full_number }}
                            </div>
                            <div class="col-md-6">
                                <b>Date: </b>{{ date('Y-m-d', strtotime($offer->created_date)) }}
                            </div>
                        </div>
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-6">
                                @if ($offer->offer_status === "Pending")
                                    <b>Status: </b>
                                    {{ $offer->offer_status }}
                                    <br>
                               @else
                                <b>Status: </b>{{ $offer->offer_status }}<br>
                               @endif
                            </div>
                            <div class="col-md-6">
                                <b>Level: </b>
                                @if ($offer->status_level === "Forapproval")
                                    (For approval)
                                @elseif ($offer->status_level === "Tosearch")
                                    (To search)
                                @elseif ($offer->status_level === "Sendoffer")
                                    (Send Offer)
                                @else
                                    ({{ $offer->status_level }})
                                @endif
                            </div>
                        </div>
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-6">
                                <b>Type: </b>{{ $offer->offer_type }}<br>
                            </div>
                            <div class="col-md-6">
                                <b>Currency: </b>{{ $offer->offer_currency }}<br>
                            </div>
                        </div>
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-12">
                                <b>Cost price status: </b>{{ $offer->cost_price_status }}<br>
                            </div>
                        </div>
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-12">
                                <b>Next reminder planned: </b>{{ $offer->next_reminder_at }}<br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="width: 27%;">
                <div class="card border-left-success shadow h-100">
                    @if ($offer->client)
                    <div class="card-header pb-1">
                        <h5>Client</h5>
                    </div>
                    <div class="card-body p-2">
                        <b>Institution: </b>{{ ($offer->client->organisation) ? $offer->client->organisation->name : '' }} @if (!empty($offer->client->organisation))<a href="{{ route('organisations.edit', [$offer->client->organisation->id]) }}?edit=offer&edit_id={{ $offer->id }}"><i class="fas fa-fw fa-edit"></i></a>@endif<br>
                        <b>Contact: </b>{{ $offer->client->full_name }} @if (!empty($offer->client))<a href="{{ route('contacts.edit', [$offer->client->id]) }}?edit=offer&edit_id={{ $offer->id }}"><i class="fas fa-fw fa-edit"></i></a>@endif<br>
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-6">
                                <b>Email:
                                    @if (!empty($offer->client->email))
                                        </b><a href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_email_link', 'details']) }}?email_to={{ $offer->client->email }}" style="color: #4e73df; !important">{{ $offer->client->email }}</a><br>
                                    @elseif (!empty($offer->client->organisation->email))
                                        </b><a href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_email_link', 'details']) }}?email_to={{ $offer->client->organisation->email }}" style="color: #4e73df; !important">{{ $offer->client->organisation->email }}</a><br>
                                    @endif
                            </div>
                            <div class="col-md-6">
                                <b>Phone: </b>{{ $offer->client->mobile_phone ?? "" }}<br>
                            </div>
                        </div>
                        <div class="row" style="margin: 0 0 0 -8px !important;">
                            <div class="col-md-6">
                                <b>Country: </b>{{ ($offer->client->country) ? $offer->client->country->name : '' }}<br>
                                <b>Level: </b>{{ ($offer->client->organisation) ? $offer->client->organisation->level : '' }}
                            </div>
                            <div class="col-md-6">
                                <b>Destination: </b>{{ !empty($offer->delivery_airport) ? $offer->delivery_airport->name : "" }}, {{ !empty($offer->delivery_country) ? $offer->delivery_country->name : '' }}<br>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card-header pb-1">
                        <h5>Institution details</h5>
                    </div>
                    <div class="card-body p-2">
                        <b>Institution: </b>{{ ($offer->organisation) ? $offer->organisation->name : '' }}<br>
                        <b>Email: </b><a href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_email_link', 'details']) }}?email_to={{ $offer->organisation->email }}" style="color: #4e73df; !important">{{ $offer->organisation->email ?? "" }}</a><br>
                        <b>Phone: </b>{{ ($offer->organisation) ? $offer->organisation->phone : '' }}<br>
                        <b>Country: </b>{{ !empty($offer->organisation->country) ? $offer->organisation->country->name : '' }}<br>
						<b>Level: </b>{{ ($offer->organisation) ? $offer->organisation->level : '' }}
                    </div>
                    @endif
                </div>
            </div>

            <div style="width: 17%;">
                <div class="card border-left-success shadow h-100">
                    <div class="card-header pb-1">
                        <h5>Supplier</h5>
                    </div>
                    <div class="card-body p-2">
                        @if ($offer->supplier)
                            <b>Institution: </b>{{ ($offer->supplier->organisation) ? $offer->supplier->organisation->name : '' }}<br>
                            <b>Contact: </b>{{ $offer->supplier->full_name }}<br>
                            <b>Email: </b><a href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_email_link', 'details']) }}?email_to={{ $offer->supplier->email }}" style="color: #4e73df; !important">{{ $offer->supplier->email }}</a><br>
                            <b>Phone: </b>{{ $offer->supplier->mobile_phone ?? "" }}<br>
                            <b>Country: </b>{{ ($offer->supplier->country) ? $offer->supplier->country->name : '' }}<br>
							<b>Level: </b>{{ ($offer->supplier->organisation) ? $offer->supplier->organisation->level : '' }}
                        @else
                            <b>Institution: </b>International Zoo Services<br>
                            <b>Contact: </b><br>
                            <b>E-mail: </b><a href="{{ route('offers.sendEmailOption', [$offer->offerId, 'to_email_link', 'details']) }}?email_to={{ $offer->supplier->email }}" style="color: #4e73df; !important">izs@zoo-services.com</a><br>
                            <b>Phone: </b><br>
                            <b>Country: </b>The Netherlands
                        @endif
                    </div>
                </div>
            </div>

            <div style="width: 12%;">
                <div class="card border-left-success shadow h-100">
                    <div class="card-header pb-1">
                        <h5>Menu</h5>
                    </div>
                    <div class="card-body p-2">
                        <ul class="nav nav-tabs card-header-tabs" id="offerTabs" style="display: block;  padding: 0 5px 13px 5px;">
                            <li class="nav-item active">
                                <a class="nav-link" id="animals-tab" data-toggle="tab" href="#animalsTab" role="tab" aria-controls="animalsTab" aria-selected="false"><i class="fas fa-fw fa-paw"></i> Offer-prices</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="actions-tab" data-toggle="tab" href="#actionsTab" role="tab" aria-controls="actionsTab" aria-selected="false"><i class="fas fa-fw fa-tasks"></i> Actions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="files-tab" data-toggle="tab" href="#filesTab" role="tab" aria-controls="filesTab" aria-selected="false"><i class="fas fa-fw fa-file"></i> Files</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="email-received-tab" data-toggle="tab" href="#email-received" role="tab" aria-controls="email-received" aria-selected="true"><i class="fas fa-fw fa-envelope"></i> Received emails</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false"><i class="fas fa-fw fa-envelope"></i> Sent Emails</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div style="width: 11%;">
                <div class="card border-left-success shadow h-100">
                    <div class="card-header pb-1">
                        <div class="d-flex justify-content-between">
                            <h5>Tasks</h5>
                            <div>
                                <a href="#" title="New task" class="btn btn-sm btn-dark" id="newTask" data-toggle="modal" data-id="{{ $offer->offerId }}" style="padding: 2px 5px 1px 5px; font-size: 10px;"><i class="fas fa-plus"></i></a>
                                @if (count($offer->offer_today_tasks) > 0 || count($offer->offer_other_tasks) > 0)
                                    <a href="#" title="See more task" class="btn btn-sm btn-dark" id="seemoreTask" data-toggle="modal" style="padding: 2px 5px 1px 5px; font-size: 10px;"><i class="fas fa-search"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="overflow-auto" style="height: 150px;">
                            @unless($offer->offer_today_tasks->isEmpty() && $offer->offer_other_tasks->isEmpty())
                            <!-- Modal -->
                            <div class="modal fade" name="seemoreTaskModal" id="seemoreTaskModal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="seemoreTaskModalTitle">Tasks</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        @foreach( $offer->offer_today_tasks as $todayTask )
                                            <div>
                                                <a href="{{ route('offers.deleteOfferTask', $todayTask->id) }}" onclick="return confirm('Are you sure you want to delete this task?')"><i class="fas fa-window-close"></i></a>
                                                <a href="#" id="editTask" data-toggle="modal" data-id="{{ $todayTask->id }}" title="Edit task">{{ $todayTask->description }} ({{$todayTask->action}})</a>
                                            </div>
                                        @endforeach
                                        @foreach( $offer->offer_other_tasks as $otherTask )
                                            <div>
                                                <a href="{{ route('offers.deleteOfferTask', $otherTask->id) }}" onclick="return confirm('Are you sure you want to delete this task?')"><i class="fas fa-window-close"></i></a>
                                                <a href="#" id="editTask" data-toggle="modal" data-id="{{ $otherTask->id }}" title="Edit task">{{ $otherTask->description }} ({{$otherTask->action}})</a>
                                            </div>
                                        @endforeach
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @else
                                <p>No tasks to to.</p>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>

            <div style="width: 11%;">
                <div class="card border-left-success shadow h-100">
                    <div class="card-header pb-1">
                        <div class="d-flex justify-content-between">
                            <h5>Remarks</h5>
                            <div>
                                <a href="#" class="btn btn-sm btn-dark" title="New remarks" data-toggle="modal" data-target="#seemoreRemarks" style="padding: 2px 5px 1px 5px; font-size: 10px;"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="overflow-auto" style="height: 150px;">
                            <div class="remarkValue">
                                {{$offer->remarks ?? ""}}
                            </div>
                            <div class="modal fade" name="seemoreRemarksModal" id="seemoreRemarks" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        {!! Form::model($offer, ['method' => 'POST', 'route' => ['offers.updateRemark', $offer->id], 'id' => 'remarkForm'] ) !!}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="seemoreTaskModalTitle">Remarks</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        {!! Form::label('remarks', 'Remarks') !!}
                                                        {!! Form::textarea('remarks', null, ['class' => 'form-control', 'rows' => '2']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary btn-lg" id="remarkSave">Save</button>
                                            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('main-content')

    <div class="card shadow mb-4">
        <div class="card-body" style="padding: 25px 4px;">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="animalsTab" role="tabpanel" aria-labelledby="animals-tab">
                    @if (Auth::user()->hasPermission('offers.update'))
                        <div class="d-flex flex-row justify-content-between" style="padding: 0 9px 9px 9px;">
                            <div class="d-flex align-items-baseline">
                                <label class="mr-3"><input type="checkbox" id="selectAllSpecies" name="selectAllSpecies" class="ml-2"> Select all</label>
                                <a href="#" class="mr-2 btn btn-primary btn-sm" title="Add species to offer" data-toggle="modal" data-target="#addSpecies"><i class="fas fa-plus"></i>&nbsp;Add species</a>
                                <a href="#" class="mr-2 btn btn-danger btn-sm" id="deleteSelectedSpecies" title="Remove species from offer"><i class="fas fa-window-close"></i>&nbsp;Remove species</a>
                                <a href="#" class="mr-2 btn btn-secondary btn-sm" id="searchSelectedSpecies" title="Search mail for selected species"><i class="fas fa-envelope"></i>&nbsp;Search mail</a>
                                @if ($offer->sale_price_type != "ExZoo")
                                    <label class="font-weight-bold">Airfreight type:</label>
                                    <input type="radio" id="airfreightType" name="airfreightType" class="ml-2" value="volKgRates" @if($offer->airfreight_type == "volKgRates") checked @endif><label>&nbsp;vol.kg rate</label>
                                    <input type="radio" id="airfreightType" name="airfreightType" class="ml-2" value="byTruck" @if($offer->airfreight_type == "byTruck") checked @endif><label>&nbsp;by truck</label>
                                    <input type="radio" id="airfreightType" name="airfreightType" class="ml-2" value="pallets" @if($offer->airfreight_type == "pallets") checked @endif><label>&nbsp;pallets</label>
                                @endif
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="btn-group ml-2">
                                    @if($offer->offer_status === "Pending")
                                        <a href="{{ route('offers.quickChangeStatus', [$offer->offerId, 'Ordered']) }}" class="btn btn-primary btn-sm mr-2">Mark as ordered</a>
                                    @endif
                                    @if($offer->offer_status != "Cancelled" && $offer->offer_status != "Ordered")
                                        <a href="{{ route('offers.quickChangeStatus', [$offer->offerId, 'Cancelled']) }}" class="btn btn-danger btn-sm">Mark as cancel</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row" style="padding: 0 9px 9px 9px;">
                        <div class="col-md-12">
                            <div class="alert alert-warning border-left-warning" role="warning">
                                Please note to adjust the basic costs.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div id="offerSpeciesTable" class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                                @include('offers.offer_species_table')
                            </div>
                        </div>
                    </div>

                    @if ($offer->sale_price_type != "ExZoo")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;" id="offerSpeciesCratesTable">
                                    @include('offers.offer_species_crates_table')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($offer->sale_price_type != "ExZoo" && $offer->airfreight_type == "volKgRates")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;" id="offerSpeciesAirfreightsTable">
                                    @include('offers.offer_species_airfreights_table', ['offer_airfreight_type' => 'offer_volKg'])
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($offer->sale_price_type != "ExZoo")
                        @if ($offer->airfreight_type == "byTruck")
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;" id="offerTransportTruckDiv">
                                        <table id="offerTransportTruckTable" class="table table-sm" style="table-layout: unset;" width="100%" cellspacing="0">
                                            <thead>
                                            <tr class="green header">
                                                <th class="none"><span >TRANSPORT BY TRUCK</span></th>
                                                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                                                    <th class="none"><span > COST PRICES</span></th>
                                                @endif
                                                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                                                    <th class="none"><span >SALE PRICES</span></th>
                                                @endif
                                                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                                                    <th class="none"><span >PROFIT</span></th>
                                                @endif
                                            </tr>
                                            </thead>
                                            @if (Auth::user()->hasPermission('offers.update'))
                                                <tr class="greengray">
                                                    <th>
                                                        @if (Auth::user()->hasPermission('offers.update'))
                                                            {!! Form::open(['id' => 'selectOfferTransportTruck', 'class' => 'mt-n3']) !!}
                                                            <div class="row" style="margin: 27px -143px 0 16px;">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        {!! Form::label('truck_from_country', 'From country', ['class' => 'font-weight-bold']) !!}
                                                                        {!! Form::select('truck_from_country', $from_country, null, ['class' => 'form-control form-control-sm', 'placeholder' => '- select -']) !!}
                                                                        {!! Form::hidden('offer_id', $offer->offerId, ['class' => 'form-control']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        {!! Form::label('truck_to_country', 'To country', ['class' => 'font-weight-bold']) !!}
                                                                        {!! Form::select('truck_to_country', $to_country, null, ['class' => 'form-control form-control-sm', 'placeholder' => '- select -']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1 align-self-end">
                                                                    <div class="form-group">
                                                                        <button class="btn btn-sm btn-dark" id="selectOfferTransportTruckSave" type="submit"><i class="fas fa-save"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {!! Form::close() !!}
                                                        @endif
                                                    </th>
                                                    <th>
                                                    </th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            @endif
                                            <tbody id="offerTransportTruckTableBody">
                                                @include('offers.offer_transport_truck_table')
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($offer->airfreight_type == "pallets")
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;" id="offerAirfreightPalletsDiv">
                                        <table id="offerAirfreightPalletTable" class="table table-sm" style="table-layout: unset;" width="100%" cellspacing="0">
                                            <thead>
                                            <tr class="green header">
                                                <th class="none"><span >AIRFREIGHT BY PALLETS</span></th>
                                                @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                                                    <th class="none"><span > COST PRICES</span></th>
                                                @endif
                                                @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                                                    <th class="none"><span >SALE PRICES</span></th>
                                                @endif
                                                @if (Auth::user()->hasPermission('offers.see-profit-value'))
                                                    <th class="none"><span >TST.PROFIT</span></th>
                                                @endif
                                            </tr>
                                            @if (Auth::user()->hasPermission('offers.update'))
                                                <tr class="greengray header">
                                                    <th>
                                                        @if (Auth::user()->hasPermission('offers.update'))
                                                            <div class="d-flex mt-2 mb-2 ml-3">
                                                                @if (Auth::user()->hasPermission('airfreights.create') && $offer->airfreight_pallet == null)
                                                                    <a href="{{ route('airfreights.create', [$offer->id, 'offer_pallet']) }}" class="save save-airfreight d-flex center mb-2" title="Add new airfreight">
                                                                        <i class="fas fa-plus"></i>&nbsp;Add airfreight
                                                                    </a>
                                                                @endif
                                                                <a href="#" title="Select airfreight pallet" class="save save-airfreight d-flex center mb-2" id="selectAirfreightPallet" data-toggle="modal" data-id="{{ $offer->offerId }}" isPallet="1">
                                                                    <i class="fas fa-plane"></i>&nbsp;Select airfreight pallet
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </th>
                                                    <th>
                                                    </th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            @endif
                                            </thead>
                                            <tbody id="offerAirfreightPalletTableBody">
                                            @include('offers.offer_airfreight_pallet_table')
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" id="additionalTestsDiv" offerId="{{ $offer->offerId }}" isTest="1">
                                    <table class="table " id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                        <tr class="green header">
                                            <th class="none"><i class="fas fa-syringe fa-sm fa-fw mr-3 ml-2 fa-1x fa-rotate-270 text-black-400" aria-hidden="true"></i><span >TESTS</span></th>
                                            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                                                <th class="none"><span >COSTS</span></th>
                                            @endif
                                            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                                                <th class="none"><span >SALES</span></th>
                                            @endif
                                            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                                                <th class="none"><span >PROFIT</span></th>
                                            @endif
                                        </tr>
                                        </thead>
                                        @if (Auth::user()->hasPermission('offers.update'))
                                            {!! Form::open(['id' => 'addTestAdditionalCost', 'class' => 'form-inline mt-n3 mb-2']) !!}
                                            <tr class="greengray header">
                                                <th >
                                                    <div class="d-flex mt-2 mb-2 ml-3">
                                                        {!! Form::text('testAdditionalCostName', null, ['class' => 'form-control', 'placeholder' => 'Enter testname to add...']) !!}
                                                        {!! Form::hidden('offer_id', $offer->offerId, ['class' => 'form-control']) !!}
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="save d-flex center mb-2" id="addTestAdditionalCostSave" type="submit" style="margin: 8px 0 0 9px; width: 85px !important; float: left;">
                                                        <i class="fas fa-save align ml-2 " aria-hidden="true"></i>
                                                        <p class="align m-0 ml-2">Add test</p>
                                                    </div>
                                                    <a href="#" class="mr-2 btn btn-danger btn-sm d-fle mb-2 delete" id="deleteSelectedTest" title="Remove test from offer" style="width: 85px !important;"><i class="fas fa-window-close"></i>&nbsp;Delete</a>
                                                </th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            {!! Form::close() !!}
                                        @endif
                                        <tbody id="additionalTestsBody" class="additionalTestsBody">
                                        @include('offers.additional_tests_table')
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" id="additionalCostsDiv" offerId="{{ $offer->offerId }}" isTest="0">
                                    <table class="table " id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                        <tr class="green header">
                                            <th class="none"><i class="fas fa-money-check-alt fa-sm fa-fw mr-3 ml-2 fa-1x  text-black-400" aria-hidden="true"></i><span >BASIC COST</span></th>
                                            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                                                <th class="none"><span >BC COSTS</span></th>
                                            @endif
                                            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                                                <th class="none"><span >BC SALES</span></th>
                                            @endif
                                            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                                                <th class="none"><span >BC.PROFIT</span></th>
                                            @endif
                                        </tr>
                                        @if (Auth::user()->hasPermission('offers.update'))
                                            {!! Form::open(['id' => 'addBasicAdditionalCost', 'class' => 'form-inline mt-n3 mb-2']) !!}
                                            <tr class="greengray header">
                                                <th >
                                                    <div class="d-flex mt-2 mb-2 ml-3">
                                                        {!! Form::text('basicAdditionalCostName', null, ['class' => 'form-control', 'placeholder' => 'Enter cost name to add...']) !!}
                                                        {!! Form::hidden('offer_id', $offer->offerId, ['class' => 'form-control']) !!}
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="save d-flex center mb-2" id="additionalCostsDivSave" type="submit" style="margin: 8px 0 0 9px;">
                                                        <i class="fas fa-save align ml-2 " aria-hidden="true"></i>
                                                        <p class="align m-0 ml-2">Add cost</p>
                                                    </div>
                                                </th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            {!! Form::close() !!}
                                        @endif
                                        </thead>
                                        <tbody id="additionalCostsDivBody">
                                        @include('offers.additional_costs_table')
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;" id="totalAndProfitSection">
                                @include('offers.total_and_profit_table')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="actionsTab" role="tabpanel" aria-labelledby="actions-tab">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6">
                            <label class="mr-3"><input type="checkbox" id="selectAllActions" name="selectAllActions" class="ml-1"> Select all</label>
                            <a href="#" class="btn btn-primary btn-sm mr-2" title="Add actions to offer" data-toggle="modal" data-target="#actionOfferSelection"><i class="fas fa-plus"></i>&nbsp;Add actions</a>
                            <a href="#" class="btn btn-secondary btn-sm mr-2" id="editSelectedActions" title="Edit offer actions"><i class="fas fa-window-close"></i>&nbsp;Edit actions</a>
                            <a href="#" class="btn btn-danger btn-sm" id="deleteSelectedActions" title="Remove offer actions"><i class="fas fa-window-close"></i>&nbsp;Remove actions</a>
                        </div>
                    </div>
                    <table class="table table-sm" style="overflow-x: auto; overflow-y: hidden;" width="100%" cellspacing="0">
                        <tbody>
                            <tr class="table-success text-center">
                                <td colspan="8">GENERAL</td>
                            </tr>
                            <tr class="table-active">
                                <td style="width: 5%;"></td>
                                <td style="width: 20%;">Action to do</td>
                                <td style="width: 10%;">To be done by</td>
                                <td style="width: 10%;">Action date</td>
                                <td style="width: 10%;">Date remind</td>
                                <td style="width: 10%;">Date received</td>
                                <td style="width: 17%;">Remark</td>
                                <td style="width: 18%;">Store document</td>
                            </tr>
                            @foreach ($generalActions as $generalAction)
                                <tr>
                                    <td class="d-inline-flex">
                                        <input type="checkbox" class="selectorActionsOffer mr-2" value="{{ $generalAction->id }}">
                                        <a href="javascript:void(0)" title="Edit offer action" id="editAction" data-id="{{ $generalAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="javascript:void(0)" title="Upload file" id="uploadActionFile" data-id="{{ $generalAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('offers.sendEmailOption', [$generalAction->id, $generalAction->action->action_code, 'details', true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $generalAction->id }}"><i class="fas fa-envelope"></i></a>
                                        <a href="javascript:void(0)" id="saveStatus" name="{{ $generalAction->status }}" data-id="{{ $generalAction->id }}"><i id="icon{{ $generalAction->id }}" class="fas fa-@php echo ($generalAction->status === 'pending') ? 'exclamation' : 'check-circle'@endphp"></i></a>
                                    </td>
                                    <td>{{ $generalAction->action->action_description }}</td>
                                    <td>{{ ($generalAction->toBeDoneBy != null) ? $generalAction->toBeDoneBy : $generalAction->action->toBeDoneBy }}</td>
                                    <td>{{ ($generalAction->action_date != null) ? date('Y-m-d', strtotime($generalAction->action_date)) : '' }}</td>
                                    <td>{{ ($generalAction->action_remind_date != null) ? date('Y-m-d', strtotime($generalAction->action_remind_date)) : '' }}</td>
                                    <td>{{ ($generalAction->action_received_date != null) ? date('Y-m-d', strtotime($generalAction->action_received_date)) : '' }}</td>
                                    <td>{{ $generalAction->remark }}</td>
                                    <td>
                                        @if ($generalAction->action_document)
                                            <a href="{{ route('offers.delete_file', [$offer->id, $generalAction->action_document]) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$offer->full_number.'/'.$generalAction->action_document) }}" target="_blank">{{ $generalAction->action_document }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr><td colspan="8">&nbsp;</td></tr>
                            <tr class="table-success text-center">
                                <td colspan="8">VETERINARY ACTIONS</td>
                            </tr>
                            <tr class="table-active">
                                <td style="width: 5%;"></td>
                                <td style="width: 20%;">Action to do</td>
                                <td style="width: 10%;">To be done by</td>
                                <td style="width: 10%;">Action date</td>
                                <td style="width: 10%;">Date remind</td>
                                <td style="width: 10%;">Date received</td>
                                <td style="width: 17%;">Remark</td>
                                <td style="width: 18%;">Store document</td>
                            </tr>
                            @foreach ($veterinaryActions as $veterinaryAction)
                                <tr>
                                    <td class="d-inline-flex">
                                        <input type="checkbox" class="selectorActionsOffer mr-2" value="{{ $veterinaryAction->id }}">
                                        <a href="javascript:void(0)" title="Edit offer action" id="editAction" data-id="{{ $veterinaryAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="javascript:void(0)" title="Upload file" id="uploadActionFile" data-id="{{ $veterinaryAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('offers.sendEmailOption', [$veterinaryAction->id, $veterinaryAction->action->action_code, 'details', true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $veterinaryAction->id }}"><i class="fas fa-envelope"></i></a>
                                        <a href="javascript:void(0)" id="saveStatus" name="{{ $veterinaryAction->status }}" data-id="{{ $veterinaryAction->id }}"><i id="icon{{ $veterinaryAction->id }}" class="fas fa-@php echo ($veterinaryAction->status === 'pending') ? 'exclamation' : 'check-circle'@endphp"></i></a>
                                    </td>
                                    <td>{{ $veterinaryAction->action->action_description }}</td>
                                    <td>{{ ($veterinaryAction->toBeDoneBy != null) ? $veterinaryAction->toBeDoneBy : $veterinaryAction->action->toBeDoneBy }}</td>
                                    <td>{{ ($veterinaryAction->action_date != null) ? date('Y-m-d', strtotime($veterinaryAction->action_date)) : '' }}</td>
                                    <td>{{ ($veterinaryAction->action_remind_date != null) ? date('Y-m-d', strtotime($veterinaryAction->action_remind_date)) : '' }}</td>
                                    <td>{{ ($veterinaryAction->action_received_date != null) ? date('Y-m-d', strtotime($veterinaryAction->action_received_date)) : '' }}</td>
                                    <td>{{ $veterinaryAction->remark }}</td>
                                    <td>
                                        @if ($veterinaryAction->action_document)
                                            <a href="{{ route('offers.delete_file', [$offer->id, $veterinaryAction->action_document, 'veterinary_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$offer->full_number.'/veterinary_docs/'.$veterinaryAction->action_document) }}" target="_blank">{{ $veterinaryAction->action_document }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr><td colspan="8">&nbsp;</td></tr>
                            <tr class="table-success text-center">
                                <td colspan="8">CRATE CONSTRUCTION</td>
                            </tr>
                            <tr class="table-active">
                                <td style="width: 5%;"></td>
                                <td style="width: 20%;">Action to do</td>
                                <td style="width: 10%;">To be done by</td>
                                <td style="width: 10%;">Action date</td>
                                <td style="width: 10%;">Date remind</td>
                                <td style="width: 10%;">Date received</td>
                                <td style="width: 17%;">Remark</td>
                                <td style="width: 18%;">Store document</td>
                            </tr>
                            @foreach ($crateActions as $crateAction)
                                <tr>
                                    <td class="d-inline-flex">
                                        <input type="checkbox" class="selectorActionsOffer mr-2" value="{{ $crateAction->id }}">
                                        <a href="javascript:void(0)" title="Edit offer action" id="editAction" data-id="{{ $crateAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="#" title="Upload file" id="uploadActionFile" data-id="{{ $crateAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('offers.sendEmailOption', [$crateAction->id, $crateAction->action->action_code, 'details', true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $crateAction->id }}"><i class="fas fa-envelope"></i></a>
                                        <a href="javascript:void(0)" id="saveStatus" name="{{ $crateAction->status }}" data-id="{{ $crateAction->id }}"><i id="icon{{ $crateAction->id }}" class="fas fa-@php echo ($crateAction->status === 'pending') ? 'exclamation' : 'check-circle'@endphp"></i></a>
                                    </td>
                                    <td>{{ $crateAction->action->action_description }}</td>
                                    <td>{{ ($crateAction->toBeDoneBy != null) ? $crateAction->toBeDoneBy : $crateAction->action->toBeDoneBy }}</td>
                                    <td>{{ ($crateAction->action_date != null) ? date('Y-m-d', strtotime($crateAction->action_date)) : '' }}</td>
                                    <td>{{ ($crateAction->action_remind_date != null) ? date('Y-m-d', strtotime($crateAction->action_remind_date)) : '' }}</td>
                                    <td>{{ ($crateAction->action_received_date != null) ? date('Y-m-d', strtotime($crateAction->action_received_date)) : '' }}</td>
                                    <td>{{ $crateAction->remark }}</td>
                                    <td>
                                        @if ($crateAction->action_document)
                                            <a href="{{ route('offers.delete_file', [$offer->id, $crateAction->action_document, 'crates']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$offer->full_number.'/crates/'.$crateAction->action_document) }}" target="_blank">{{ $crateAction->action_document }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr><td colspan="8">&nbsp;</td></tr>
                            <tr class="table-success text-center">
                                <td colspan="8">TRANSPORT BOOKING</td>
                            </tr>
                            <tr class="table-active">
                                <td style="width: 5%;"></td>
                                <td style="width: 20%;">Action to do</td>
                                <td style="width: 10%;">To be done by</td>
                                <td style="width: 10%;">Action date</td>
                                <td style="width: 10%;">Date remind</td>
                                <td style="width: 10%;">Date received</td>
                                <td style="width: 17%;">Remark</td>
                                <td style="width: 18%;">Store document</td>
                            </tr>
                            @foreach ($transportActions as $transportAction)
                                <tr>
                                    <td class="d-inline-flex">
                                        <input type="checkbox" class="selectorActionsOffer mr-2" value="{{ $transportAction->id }}">
                                        <a href="javascript:void(0)" title="Edit offer action" id="editAction" data-id="{{ $transportAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="javascript:void(0)" title="Upload file" id="uploadActionFile" data-id="{{ $transportAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('offers.sendEmailOption', [$transportAction->id, $transportAction->action->action_code, 'details', true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $transportAction->id }}"><i class="fas fa-envelope"></i></a>
                                        <a href="javascript:void(0)" id="saveStatus" name="{{ $transportAction->status }}" data-id="{{ $transportAction->id }}"><i id="icon{{ $transportAction->id }}" class="fas fa-@php echo ($transportAction->status === 'pending') ? 'exclamation' : 'check-circle'@endphp"></i></a>
                                    </td>
                                    <td>{{ $transportAction->action->action_description }}</td>
                                    <td>{{ ($transportAction->toBeDoneBy != null) ? $transportAction->toBeDoneBy : $transportAction->action->toBeDoneBy }}</td>
                                    <td>{{ ($transportAction->action_date != null) ? date('Y-m-d', strtotime($transportAction->action_date)) : '' }}</td>
                                    <td>{{ ($transportAction->action_remind_date != null) ? date('Y-m-d', strtotime($transportAction->action_remind_date)) : '' }}</td>
                                    <td>{{ ($transportAction->action_received_date != null) ? date('Y-m-d', strtotime($transportAction->action_received_date)) : '' }}</td>
                                    <td>{{ $transportAction->remark }}</td>
                                    <td>
                                        @if ($transportAction->action_document)
                                            <a href="{{ route('offers.delete_file', [$offer->id, $transportAction->action_document, 'airfreight']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$offer->full_number.'/airfreight/'.$transportAction->action_document) }}" target="_blank">{{ $transportAction->action_document }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show" id="filesTab" role="tabpanel" aria-labelledby="files-tab">
                    <div class="row">
                        <div class="col-md-12">
                            @if (Auth::user()->hasPermission('offers.upload-files'))
                                <div class="mb-3 align-items-center">
                                    <form action="{{ route('offers.upload') }}" class="dropzone" id="upload-dropzone">
                                        @csrf
                                        <input type="hidden" name="offerId" value="{{ $offer->offerId }}" />
                                        <input type="radio" name="docCategory" value="others" checked><label class="mr-2">&nbsp;Others</label>
                                        <input type="radio" name="docCategory" value="airfreight"><label class="mr-2">&nbsp;Airfreight</label>
                                        <input type="radio" name="docCategory" value="crates"><label class="mr-2">&nbsp;Crates</label>
                                        <input type="radio" name="docCategory" value="cites_docs"><label class="mr-2">&nbsp;Cites docs</label>
                                        <input type="radio" name="docCategory" value="veterinary_docs"><label class="mr-2">&nbsp;Veterinary docs</label>
                                        <input type="radio" name="docCategory" value="documents"><label class="mr-2">&nbsp;General docs</label>
                                        <input type="radio" name="docCategory" value="suppliers_offers"><label>&nbsp;Offers of suppliers</label>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 border-right">
                            <h6>Airfreight</h6>
                            <div>
                                @foreach($offer->airfreight_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename'], 'airfreight']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/airfreight/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/airfreight/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Crates</h6>
                            <div>
                                @foreach($offer->crates_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename'], 'crates']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/crates/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/crates/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Documents</h6>
                            <h6 class="badge">Cites docs</h6>
                            <div>
                                @foreach($offer->cites_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename'], 'cites_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/cites_docs/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/cites_docs/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                            <div class="dropdown-divider"></div>
                            <h6 class="badge">Veterinary docs</h6>
                            <div>
                                @foreach($offer->veterinary_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename'], 'veterinary_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/veterinary_docs/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/veterinary_docs/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                            <div class="dropdown-divider"></div>
                            <h6 class="badge">General docs</h6>
                            <div>
                                @foreach($offer->documents_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename'], 'documents']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/documents/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/documents/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Offers of suppliers</h6>
                            <div>
                                @foreach($offer->suppliers_offers as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename'], 'suppliers_offers']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/suppliers_offers/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/suppliers_offers/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Others</h6>
                            <div>
                                @foreach($offer->others_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('offers.delete_file', [$offer->offerId, $file['basename']]) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" title="Add Dashboard" data-url="{{'/offers_docs/'.$offer->full_number.'/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$offer->full_number.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="email" role="tabpanel" aria-labelledby="email-tab">
                    @include('inbox.table_show', ['email_show' => $emails])
                </div>
                <div class="tab-pane fade show" id="email-received" role="tabpanel" aria-labelledby="email-received-tab">
                    @include('inbox.table_show', ['email_show' => $emails_received])
                </div>
            </div>
        </div>
    </div>

@include('offers.add_species_modal', ['modalId' => 'addSpecies', 'offerId' => $offer->offerId])

@include('tasks.task_form_modal', ['modalId' => 'offerTaskForm', 'route' => 'offers.offerTask'])

@include('offers.selected_species_airfreight_selection_modal', ['modalId' => 'setSpeciesAirfreightsValues'])
@include('offers.species_airfreight_selection_modal', ['modalId' => 'selectSpeciesAirfreight'])
@include('offers.offer_pallet_selection_modal', ['modalId' => 'selectOfferAirfreightPallet'])

@include('offers.select_action_modal', ['modalId' => 'actionOfferSelection'])
@include('offers.edit_selected_actions_modal', ['modalId' => 'editSelectedOfferActions'])
@include('uploads.upload_modal', ['modalId' => 'uploadActionDocument', 'route' => 'offers.uploadOfferActionDocument'])

@include('wanted.select_continent_country_modal', ['modalId' => 'selectContinentCountryModal'])
@include('general_documents.add_document_modal', ['modalId' => 'uploadGeneralDoc'])
@include('offers.edit_modal', ['modalId' => 'editModal'])

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Catch window close
    window.addEventListener('beforeunload', (event) => {
        (event || window.event).preventDefault();
        (event || window.event).returnValue = "Are you sure you want to leave?";
    });

    function alertUpdateStatus(url){
        Swal.fire({
            title: "Update approval",
            html: "Text warning approval yes/no:<br>If the offer is ready with prices for approval, select Aproval= <strong>yes</strong><br>If the offer is still an inquiry, not ready, select Approval= <strong>no</strong>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
            closeOnConfirm: false,
            showCancelButton: true,
            closeOnCancel: true,
        }).then((result) => {
            if(typeof result != "undefined" && result.isConfirmed){
                $.ajax({
                    type:'GET',
                    url:"{{ route('offers.quickChangeStatusLevelForapproval') }}",
                    data:{
                        id: "{{ $offer->id }}",
                    },
                    success:function(data){
                        if(data.error){
                            $.NotificationApp.send("Error message!", data.message , 'top-right', '#bf441d', 'error');
                        }else{
                            $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');
                        }

                    }
                });
            }
            window.location = url;
        });
    }

    $('#offerTabs a[href="'+$('[name=selectedOfferTab]').val()+'"]').tab('show');

    $('#offerTabs a').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            type:'POST',
            url:"{{ route('offers.selectedOfferTab') }}",
            data:{
                offerTab: $(this).attr('href')
            },
            success:function(data){
                $(this).tab('show');
            }
        });
    })

    $('#showFilesTab').on('click', function () {
        $.ajax({
            type:'POST',
            url:"{{ route('offers.selectedOfferTab') }}",
            data:{
                offerTab: "#filesTab"
            },
            success:function(data){
                $('#offerTabs a[href="#filesTab"]').tab('show'); // Select tab by name
            }
        });
    });

    $(':checkbox:checked.selectorSpecies').prop('checked', false);

    $('#selectAllSpecies').on('change', function () {
        $(":checkbox.selectorSpecies").prop('checked', this.checked);
    });

    $(':checkbox:checked.selectorActionsOffer').prop('checked', false);

    $('#selectAllActions').on('change', function () {
        $(":checkbox.selectorActionsOffer").prop('checked', this.checked);
    });

    $(':checkbox:checked.selectorSpeciesAirfreight').prop('checked', false);

    $(document).on('change', '#selectAllSpeciesAirfreight', function () {
        $(":checkbox.selectorSpeciesAirfreight").prop('checked', this.checked);
    });

    $('#addSpecies').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        /*$(this).find('form')[0].reset();*/
    });

    $('#tempSelectedSpecies').on('click', function () {
        var offerId = $('#addSpecies [name=offer_id]').val();

        var m = $('#addSpecies [name=quantityM]').val();
        var f = $('#addSpecies [name=quantityF]').val();
        var u = $('#addSpecies [name=quantityU]').val();
        var p = $('#addSpecies [name=quantityP]').val();

        var ourSurplusId = $('#addSpecies [name=select_surplus]').val();
        var select2Data = $('#addSpecies [name=select_surplus]').select2('data');
        var origin = $('#addSpecies [name=origin]').val();
        var region = $('#addSpecies [name=region]').val();
        var continent = $('#addSpecies [name=continent]').val();
        var region_text = $('#addSpecies [name=region_text]').val();

        if(!$.isNumeric(m) || !$.isNumeric(f) || !$.isNumeric(u) || !$.isNumeric(p))
            alert("Quantities must be a number.");
        else if ((m + f + u + p) == 0)
            alert("You must insert a quantity.");
        else if (m < 0 || f < 0 || u < 0 || p < 0)
            alert("Quantity value cannot be negative.");
        else if (continent == "")
            alert("You must select a continent");
        else if (ourSurplusId == 0)
            alert("You must select a surplus species.");
        else {
            var tempSelectedSpecies = $("#tempSelectedSpecies").html();
            $("#tempSelectedSpecies").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.checkAddingSpeciesRules') }}",
                data:{
                    offerId: offerId,
                    ourSurplusId: ourSurplusId
                },
                success:function(data) {
                    if(data.error){
                        $.NotificationApp.send("Error message!", data.message , 'top-right', '#bf441d', 'error');
                        $("#tempSelectedSpecies").html(tempSelectedSpecies);
                    }

                    if(data.existInOtherClientOffer) {
                        if(!confirm("The client already have this surplus in offers. Do you want to add anyway?"))
                            return;
                    }
                    else if(data.existInClientSurpluses) {
                        if(!confirm("The client already have this species in his surpluses. Do you want to add anyway?"))
                            return;
                    }

                    $("#selectedSpecies").append('<tr class="item" males="' + m + '" females="' + f + '" unsexed="' + u + '" par="' + p + '" surplusId="' + ourSurplusId + '" origin="' + origin + '" region="' + region + '">' +
                                '<td><a href="#" class="remove-tr"><i class="fas fa-window-close"></i></a></td>' +
                                '<td style="text-align: center;">' + m + '</td>' +
                                '<td style="text-align: center;">' + f + '</td>' +
                                '<td style="text-align: center;">' + u + '</td>' +
                                '<td style="text-align: center;">' + p + '</td>' +
                                '<td style="text-align: center;">' + origin + '</td>' +
                                '<td style="text-align: center;">' + region_text + '</td>' +
                                '<td>' + select2Data[0].animal.common_name + ' (' + select2Data[0].animal.scientific_name + ')<br>Curr: ' + data.ourSurplus.sale_currency + ' M: ' + data.ourSurplus.salePriceM + '.00 F: ' + data.ourSurplus.salePriceF + '.00 U: ' + data.ourSurplus.salePriceU + '.00 P: ' + data.ourSurplus.salePriceP + '.00</td>' +
                                '<td></td>' +
                            '</tr>');

                    $("#tempSelectedSpecies").html(tempSelectedSpecies);

                    $('#addSpecies [name=quantityM]').val(0);
                    $('#addSpecies [name=quantityF]').val(0);
                    $('#addSpecies [name=quantityU]').val(0);
                    $('#addSpecies [name=quantityP]').val(0);
                    $('#addSpecies [name=origin]').val(null).trigger('change');
                    $('#addSpecies [name=region]').val('');
                    $('#addSpecies [name=region_text]').val('');
                    $('#addSpecies [name=select_surplus]').val(null).trigger('change');
                }
            });
        }
    });

    $(document).on('click', '.remove-tr', function(){
        $(this).parents('tr').remove();
    });

    $('#addSpecies').on('submit', function (event) {
        event.preventDefault();

        var selectedSurplus = [];
        $('#addSpecies #selectedSpecies tr.item').each(function(){
            var row = $(this);
            selectedSurplus.push([row.attr('surplusId'), row.attr('males'), row.attr('females'), row.attr('unsexed'), row.attr('par'), row.attr('origin'), row.attr('region')]);
        });

        if(selectedSurplus.length == 0)
            alert('You must add species to the list.');
        else {
            var addSpeciesButton = $("#addSpeciesButton").html();
            $("#addSpeciesButton").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.addOfferSpecies') }}",
                data:{
                    items: selectedSurplus,
                    offerId: $('#addSpecies [name=offer_id]').val()
                },
                success:function(data) {
                    $.NotificationApp.send("Success message!", "Add New Species successfully", 'top-right', '#fff', 'success');
                    $('#addSpecies').modal('show');
                    location.reload();
                    /*alert(data.msg);*/
                },complete: function() {
                    $("#addSpeciesButton").html(addSpeciesButton);
                },
            });
        }
    });

    $('#remarkSave').on('click', function (event) {
        event.preventDefault();
        var btn_save = $('#remarkSave').html();
        $('#remarkSave').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url:"{{ route('offers.updateRemark') }}",
            data:{
                id: '{{$offer->id}}',
                remarks: $('#seemoreRemarks [name=remarks]').val()
            },
            success:function(data) {
                if(typeof data.error != "undefined" && data.error === true){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $('.remarkValue').html(data.remark);
                    $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');
                }
                $('#remarkSave').html(btn_save);
                $('#seemoreRemarks').modal('hide');
            }
        });
    });

    $('#deleteSelectedSpecies').on('click', function () {
        var ids = [];
        $(":checked.selectorSpecies").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select species to delete.");
        else if(confirm("Are you sure that you want to delete the selected species?")) {
            var deleteSelectedSpecies = $("#deleteSelectedSpecies").html();
            $("#deleteSelectedSpecies").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.deleteSpecies') }}",
                data:{items: ids},
                success:function(data) {
                    $.NotificationApp.send("Success message!", "Species removed successfully", 'top-right', '#fff', 'success');
                    location.reload();
                },complete: function() {
                    $("#deleteSelectedSpecies").html(deleteSelectedSpecies);
                },
            });
        }
    });

    $('#deleteSelectedTest').on('click', function () {
        var ids = [];
        $(":checked.selectorTest").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select species to delete.");
        else if(confirm("Are you sure that you want to delete the selected species?")) {
            var deleteSelectedTest = $("#deleteSelectedTest").html();
            $("#deleteSelectedTest").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'GET',
                url:"{{ route('offers.deleteCost') }}",
                data:{items: ids},
                success:function(data) {
                    $.NotificationApp.send("Success message!", "Delete successfully", 'top-right', '#fff', 'success');
                    location.reload();
                },complete: function() {
                    $("#deleteSelectedTest").html(deleteSelectedTest);
                },
            });
        }
    });

    /* New task modal dialog */
    $('#newTask').on('click', function () {
        var offerId = $(this).data('id');

        $('#offerTaskForm').modal('show');
        $('#offerTaskForm [name=id]').val(offerId);
        $("#offerTaskForm [name=due_date]").prop('disabled', true);
    });

    /* See more task modal dialog */
    $('#seemoreTask').on('click', function () {
        $('#seemoreTaskModal').modal('show');
    });

    /* Edit offer task */
    $(document).on('click', '#editTask', function () {
        var taskId = $(this).data('id');
        var offerId = $('#newTask').data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('api.task-by-id') }}",
            data:{
                id: taskId,
            },
            success:function(data){
                var dueDate = new Date(data.task.due_date);
                //convert day to 2 digits
                var dueDateDigitDay = (dueDate.getDate() < 10) ? '0' + dueDate.getDate() : dueDate.getDate();
                //convert month to 2 digits
                var dueDateDigitMonth = (dueDate.getMonth() < 9) ? '0' + (dueDate.getMonth()+1) : (dueDate.getMonth()+1);

                dueDate = dueDate.getFullYear() + '-' + dueDateDigitMonth + '-' + dueDateDigitDay;

                $('#offerTaskForm [name=id]').val(offerId);
                $('#offerTaskForm [name=task_id]').val(taskId);
                $('#offerTaskForm [name=description]').val(data.task.description);
                $('#offerTaskForm [name=action]').val(data.task.action);
                $('#offerTaskForm [name=due_date]').val(dueDate);
                $('#offerTaskForm [name=user_id]').val(data.task.user_id);
                $('#offerTaskForm').modal('show');
                $("#offerTaskForm [name=due_date]").prop('disabled', true);
            }
        });
    });

    $('#offerTaskForm input[name=quick_action_dates]').change(function() {
        var quickActionDate = $('#offerTaskForm input[name=quick_action_dates]:checked').val();

        if (quickActionDate == 'specific')
            $("#offerTaskForm [name=due_date]").prop('disabled', false);
        else
            $("#offerTaskForm [name=due_date]").prop('disabled', true);
    });

    $('#offerTaskForm').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $(document).on('click', '#airfreightType', function () {
        var value = $(this).val();
        var idOffer = $('#newTask').data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('offers.saveOfferAirfreightType') }}",
            data:{
                value: value,
                idOffer: idOffer
            },
            success:function(data){
                location.reload();
            }
        });
    });

    $(document).on('change', '#offerSpeciesTable [name=offerQuantityM]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerQuantityF]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerQuantityU]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerQuantityP]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerCostPriceM]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerCostPriceF]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerCostPriceU]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerCostPriceP]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerSalePriceM]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerSalePriceF]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerSalePriceU]', offerSpeciesChanged);
    $(document).on('change', '#offerSpeciesTable [name=offerSalePriceP]', offerSpeciesChanged);

    function offerSpeciesChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');
        var idOfferSpecies = $(sender.parents('tr')).attr('offerSpeciesId');
        var oldValue = sender.attr('oldValue');
        var value = sender.val();
        if(!$.isNumeric(value)) {
            sender.val(oldValue);
            alert("Value must be a number.");
        }
        else if(value < 0) {
            sender.val(oldValue);
            alert('Value cannot be less than zero');
        }
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveSpeciesValues') }}",
                data:{
                    idOfferSpecies: idOfferSpecies,
                    column: column,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    $('#offerSpeciesTable').html(data.html);
                    $('#offerSpeciesAirfreightsTable').html(data.speciesAirfreightsHtml);
                    $('#offerSpeciesCratesTable').html(data.speciesCratesHtml);
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    $.NotificationApp.send("Success message!", "Species data updated successfully", 'top-right', '#fff', 'success');
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                },complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        }
    }


    $(document).on('change', '#offerSpeciesCrateTable [name=crateSelection]', crateSelectionChanged);

    function crateSelectionChanged() {
        var sender = $(this);
        var container = $(sender.parents('div'));
        var divId = container.attr('id');
        var idOfferSpeciesCrate = $(sender.parents('tr')).attr('offerSpeciesCrateId');

        var idCrate = sender.val();

        $.ajax({
            type:'POST',
            url:"{{ route('offers.updateOfferSpeciesCrateByCrateSelected') }}",
            data:{
                idOfferSpeciesCrate: idOfferSpeciesCrate,
                idCrate: idCrate
            },
            success:function(data){
                $('#'+divId).html(data.html);
                $('#offerSpeciesAirfreightsTable').html(data.speciesAirfreightsHtml);
                $('#offerAirfreightPalletTableBody').html(data.offerSpeciesPallets);
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                if(data.totalsOffer.trim().length > 0)
                    $('#totalsOfferDiv').html(data.totalsOffer);
            }
        });
    }

    $(document).on('change', '#offerSpeciesCrateTable [name=quantity_males]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=quantity_females]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=quantity_unsexed]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=quantity_pairs]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=length]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=wide]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=height]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=cost_price]', offerSpeciesCrateChanged);
    $(document).on('change', '#offerSpeciesCrateTable [name=sale_price]', offerSpeciesCrateChanged);

    function offerSpeciesCrateChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');
        var idOfferSpeciesCrate = $(sender.parents('tr')).attr('offerSpeciesCrateId');
        var oldValue = sender.attr('oldValue');
        var value = sender.val();
        if(!$.isNumeric(value)) {
            sender.val(oldValue);
            alert("Value must be a number.");
        }
        else if(value < 0) {
            sender.val(oldValue);
            alert('Value cannot be less than zero');
        }
        else {
            if(!column){
                var resetButton = $(".reset-value").html();
                $(".reset-value").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            }
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveSpeciesCrateValues') }}",
                data:{
                    idOfferSpeciesCrate: idOfferSpeciesCrate,
                    column: column,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    if(!column){
                        $(".reset-value").html();
                    }
                    $('#offerSpeciesCrateTable').html(data.html);
                    $('#offerSpeciesAirfreightsTable').html(data.speciesAirfreightsHtml);
                    $('#offerAirfreightPalletTableBody').html(data.offerSpeciesPallets);
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    $.NotificationApp.send("Success message!", "Species Create data updated successfully", 'top-right', '#fff', 'success');
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                },complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        }
    }

    $(document).on('click', '.reset-value', function (e) {
        var idOffer = $(this).attr('data-id');

        if(idOffer){
            var buttonValue = $(this).html();
            $(this).html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveSpeciesCrateValues') }}",
                data:{
                    idOfferSpeciesCrate: idOffer
                },
                success:function(data){
                    $(".reset-value").html(buttonValue);
                    $('#offerSpeciesCrateTable').html(data.html);
                    $('#offerSpeciesAirfreightsTable').html(data.speciesAirfreightsHtml);
                    $('#offerAirfreightPalletTableBody').html(data.offerSpeciesPallets);
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    $.NotificationApp.send("Success message!", "Species Create data updated successfully", 'top-right', '#fff', 'success');
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                }
            });
        }
    });

    $('#setSpeciesAirfreightsValues').on('submit', function (event) {
        event.preventDefault();

        var selectedSpeciesAirfreights = [];
        $(":checked.selectorSpeciesAirfreight").each(function(){
            selectedSpeciesAirfreights.push($(this).val());
        });

        if(selectedSpeciesAirfreights.length == 0)
            alert('You must select species airfreight records.');
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveSelectedSpeciesAirfreightsValues') }}",
                data:{
                    items: selectedSpeciesAirfreights,
                    costValue: $('#setSpeciesAirfreightsValues [name=airfreight_cost_value]').val(),
                    salesValue: $('#setSpeciesAirfreightsValues [name=airfreight_sales_value]').val()
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $(document).on('click', '#selectAirfreights', function () {
        var offerSpeciesId = $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('offers.getOfferSpeciesAirfreights') }}",
            data:{
                offerSpeciesId: offerSpeciesId
            },
            success:function(data){
                $('#selectSpeciesAirfreight #selectedAirfreights tbody').empty();
                $.each(data.airfreights, function(key, item) {
                    $('#selectSpeciesAirfreight #selectedAirfreights').append('<tr class="item" airfreightId="' + item.id  + '">' +
                        '<td><a href="#" class="remove-airfreight-tr" offerSpeciesAirfreightId="' + item.offerSpeciesAirfreightId + '"><i class="fas fa-window-close"></i></a></td>' +
                        '<td style="text-align: center;">' + item.departureContinent + '</td>' +
                        '<td style="text-align: center;">' + item.arrivalContinent + '</td>' +
                        '<td style="text-align: center;">' + item.type + '</td>' +
                        '<td style="text-align: center;">' + item.currency + '</td>' +
                        '<td style="text-align: center;">' + item.volKg_weight_cost + '</td>' +
                        '<td style="text-align: center;">' + item.remarks + '</td>' +
                    '</tr>');
                });
            }
        });

        $('#selectSpeciesAirfreight [name=offer_species_id]').val(offerSpeciesId);
        $('#selectSpeciesAirfreight').modal('show');
    });

    $('#selectSpeciesAirfreight').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $(document).on('click', '#selectAirfreightPallet', function () {
        var offerId = $(this).data('id');

        $('#selectOfferAirfreightPallet [name=offer_id]').val(offerId);
        $('#selectOfferAirfreightPallet').modal('show');
    });

    $('#selectOfferAirfreightPallet').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#selectSpeciesAirfreight #getAirfreights').on('click', function () {
        var departure_continent = $('#selectSpeciesAirfreight [name=departure_continent]').val();
        var arrival_continent = $('#selectSpeciesAirfreight [name=arrival_continent]').val();

        var button = $(this);
        var buttonValue = button.html();
        button.html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
        $.ajax({
            type:'POST',
            url:"{{ route('airfreights.getAirfreightsByCountriesAndAirports') }}",
            data:{
                isPallet: 0,
                departure_continent: departure_continent,
                arrival_continent: arrival_continent
            },
            success:function(data) {
                $('#selectSpeciesAirfreight [name=freights]').empty();
                $('#selectSpeciesAirfreight [name=freights]').append('<option value="">- select -</option>');
                $.each(data.freights, function(key, value) {
                    $('#selectSpeciesAirfreight [name=freights]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            },complete: function() {
                button.html(buttonValue);
            }
        });
    });

    $('#selectOfferAirfreightPallet #getAirfreights').on('click', function () {
        var departure_continent = $('#selectOfferAirfreightPallet [name=departure_continent]').val();
        var arrival_continent = $('#selectOfferAirfreightPallet [name=arrival_continent]').val();

        var button = $(this);
        var buttonValue = button.html();
        button.html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");

        $.ajax({
            type:'POST',
            url:"{{ route('airfreights.getAirfreightsByCountriesAndAirports') }}",
            data:{
                isPallet: 1,
                departure_continent: departure_continent,
                arrival_continent: arrival_continent
            },
            success:function(data){
                $('#selectOfferAirfreightPallet [name=freights]').empty();
                $('#selectOfferAirfreightPallet [name=freights]').append('<option value="">- select -</option>');
                $.each(data.freights, function(key, value) {
                    $('#selectOfferAirfreightPallet [name=freights]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            },complete: function() {
                button.html(buttonValue);
            }
        });
    });

    $('#selectSpeciesAirfreight #tempAddSelectedAirfreight').on('click', function () {
        var selectedFreightId = $('#selectSpeciesAirfreight [name=freights]').val();

        if (selectedFreightId == null)
            alert("You must select an airfreight.");
        else {
            var tempAddSelectedAirfreight = $("#tempAddSelectedAirfreight").html();
            $("#tempAddSelectedAirfreight").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('airfreights.getAirfreightById') }}",
                data:{
                    selectedFreightId: selectedFreightId
                },
                success:function(data){
                    $('#selectSpeciesAirfreight #selectedAirfreights').append('<tr class="item" airfreightId="' + data.airfreight.id  + '">' +
                        '<td><a href="#" class="remove-airfreight-tr" offerSpeciesAirfreightId="' + data.airfreight.offerSpeciesAirfreightId + '"><i class="fas fa-window-close"></i></a></td>' +
                        '<td style="text-align: center;">' + data.airfreight.departureContinent + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.arrivalContinent + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.type + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.currency + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.volKg_weight_cost + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.remarks + '</td>' +
                    '</tr>');
                },
                complete: function () {
                    $("#tempAddSelectedAirfreight").html(tempAddSelectedAirfreight);
                },
            });
        }
    });

    $(document).on('click', '.remove-airfreight-tr', function() {
        var sender = $(this);
        var offerSpeciesAirfreightId = sender.attr('offerSpeciesAirfreightId');

        if(offerSpeciesAirfreightId.trim().length > 0) {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.removeOfferSpeciesAirfreight') }}",
                data:{
                    offerSpeciesAirfreightId: offerSpeciesAirfreightId
                },
                success:function(data){
                    $('#offerSpeciesAirfreightsTable').html(data.html);
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                }
            });
        }

        sender.parents('tr').remove();
        $('#selectSpeciesAirfreight').modal('hide');
    });

    $('#selectSpeciesAirfreight').on('submit', function (event) {
        event.preventDefault();

        var offerSpeciesId = $('#selectSpeciesAirfreight [name=offer_species_id]').val();

        var selectedAirfreights = [];
        $('#selectSpeciesAirfreight #selectedAirfreights tr.item').each(function(){
            selectedAirfreights.push($(this).attr('airfreightId'));
        });

        if(selectedAirfreights.length > 0) {
            var buttonSpeciesAirfreights = $("#buttonSpeciesAirfreights").html();
            $("#buttonSpeciesAirfreights").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveOfferSpeciesAirfreights') }}",
                data:{
                    items: selectedAirfreights,
                    offerSpeciesId: offerSpeciesId
                },
                success:function(data){
                    if(data.success) {
                        $('#offerSpeciesAirfreightsTable').html(data.html);
                        $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    }

                    $('#selectSpeciesAirfreight').modal('hide');
                },
                complete: function() {
                    $("#buttonSpeciesAirfreights").html(buttonSpeciesAirfreights);
                },
            });
        }
        else
            alert("You have not selected any airfreight.");
    });

    $('#selectOfferAirfreightPallet').on('submit', function (event) {
        event.preventDefault();

        var offerId = $('#selectOfferAirfreightPallet [name=offer_id]').val();
        var departure_continent = $('#selectOfferAirfreightPallet [name=departure_continent]').val();
        var arrival_continent = $('#selectOfferAirfreightPallet [name=arrival_continent]').val();
        var selectedPallet = $('#selectOfferAirfreightPallet [name=freights]').val();

        if(departure_continent != "" && arrival_continent != "" && selectedPallet != "") {
            var saveOfferAirfreightPalletSave = $("#saveOfferAirfreightPalletSave").html();
            $("#saveOfferAirfreightPalletSave").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveOfferAirfreightPallet') }}",
                data:{
                    offerId: offerId,
                    departure_continent: departure_continent,
                    arrival_continent: arrival_continent,
                    selectedPallet: selectedPallet
                },
                success:function(data){
                    if(data.success) {
                        $.NotificationApp.send("Success message!", "Offer Airfreight updated successfully", 'top-right', '#fff', 'success');
                        $('#offerAirfreightPalletTableBody').html(data.html);
                        $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    }

                    $('#selectOfferAirfreightPallet').modal('hide');
                },complete: function() {
                    $("#saveOfferAirfreightPalletSave").html(saveOfferAirfreightPalletSave);
                }
            });
        }
        else
            alert("You need to select origin, destination, and pallet freight.");
    });

    $(document).on('change', '#offerSpeciesAirfreightTable [name=cost_volKg]', offerSpeciesAirfreightChanged);
    $(document).on('change', '#offerSpeciesAirfreightTable [name=sale_volKg]', offerSpeciesAirfreightChanged);

    function offerSpeciesAirfreightChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');
        var idOfferSpeciesAirfreight = $(sender.parents('tr')).attr('speciesAirfreightId');

        var oldValue = sender.attr('oldValue');
        var value = sender.val();
        if(!$.isNumeric(value)) {
            sender.val(oldValue);
            alert("Value must be a number.");
        }
        else if(value < 0) {
            sender.val(oldValue);
            alert('Value cannot be less than zero');
        }
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveSpeciesAirfreightVolKgRateValues') }}",
                data:{
                    idOfferSpeciesAirfreight: idOfferSpeciesAirfreight,
                    column: column,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    $('#offerSpeciesAirfreightTable').html(data.html);
                    $.NotificationApp.send("Success message!", "Flights data updated successfully", 'top-right', '#fff', 'success');
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                },complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        }
    }

    $('#selectOfferTransportTruck').on('submit', function (event) {
        event.preventDefault();

        var offerId = $('#selectOfferTransportTruck [name=offer_id]').val();
        var from_country = $('#selectOfferTransportTruck [name=truck_from_country]').val();
        var to_country = $('#selectOfferTransportTruck [name=truck_to_country]').val();

        if(from_country != "" && to_country != "") {
            var selectOfferTransportTruckSave = $("#selectOfferTransportTruckSave").html();
            $("#selectOfferTransportTruckSave").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveOfferTransportTruck') }}",
                data:{
                    offerId: offerId,
                    from_country: from_country,
                    to_country: to_country
                },
                success:function(data){
                    if(data.success) {
                        $.NotificationApp.send("Success message!", "Offer Transport Truck data updated successfully", 'top-right', '#fff', 'success');
                        $('#offerTransportTruckTableBody').html(data.html);
                        $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    }
                },
                complete: function() {
                    $("#selectOfferTransportTruckSave").html(selectOfferTransportTruckSave);
                },
            });
        }
        else
            alert("You need to select origin and destination.");
    });

    $(document).on('change', '#offerAirfreightPalletTable [name=pallet_quantity]', offerAirfreightPalletChanged);
    $(document).on('change', '#offerAirfreightPalletTable [name=pallet_cost_value]', offerAirfreightPalletChanged);
    $(document).on('change', '#offerAirfreightPalletTable [name=pallet_sale_value]', offerAirfreightPalletChanged);

    function offerAirfreightPalletChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var idOfferAirfreightPallet = $(sender.parents('tr')).attr('offerAirfreightPalletId');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');

        var oldValue = sender.attr('oldValue');
        var value = sender.val();
        if(!$.isNumeric(value)) {
            sender.val(oldValue);
            alert("Value must be a number.");
        }
        else if(value < 0) {
            sender.val(oldValue);
            alert('Value cannot be less than zero');
        }
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveOfferAirfreightPalletValues') }}",
                data:{
                    idOfferAirfreightPallet: idOfferAirfreightPallet,
                    column: column,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    $('#offerAirfreightPalletTableBody').html(data.html);
                    $.NotificationApp.send("Success message!", "Airfreight Pallet data updated successfully", 'top-right', '#fff', 'success');
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                },complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        }
    }

    $(document).on('change', '#offerTransportTruckTable [name=total_km]', offerTransportTruckChanged);
    $(document).on('change', '#offerTransportTruckTable [name=cost_rate_per_km]', offerTransportTruckChanged);
    $(document).on('change', '#offerTransportTruckTable [name=sale_rate_per_km]', offerTransportTruckChanged);

    function offerTransportTruckChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var idOfferTransportTruck = $(sender.parents('tr')).attr('offerTransportTruckId');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');

        var oldValue = sender.attr('oldValue');
        var value = sender.val();
        if(!$.isNumeric(value)) {
            sender.val(oldValue);
            alert("Value must be a number.");
        }
        else if(value < 0) {
            sender.val(oldValue);
            alert('Value cannot be less than zero');
        }
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveOfferTransportTruckValues') }}",
                data:{
                    idOfferTransportTruck: idOfferTransportTruck,
                    column: column,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    $('#offerTransportTruckTableBody').html(data.html);
                    $.NotificationApp.send("Success message!", "Transport Truck data updated successfully", 'top-right', '#fff', 'success');
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                },complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        }
    }

    $(document).on('change', '.offerBasicTestRow [name=quantity]', additionalCostsChanged);
    $(document).on('change', '.offerBasicCostRow [name=quantity]', additionalCostsChanged);
    $(document).on('change', '.offerBasicTestRow [name=costPrice]', additionalCostsChanged);
    $(document).on('change', '.offerBasicCostRow [name=costPrice]', additionalCostsChanged);
    $(document).on('change', '.offerBasicTestRow [name=salePrice]', additionalCostsChanged);
    $(document).on('change', '.offerBasicCostRow [name=salePrice]', additionalCostsChanged);

    function additionalCostsChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var container = $(sender.parents('tr'));
        var divId = sender.parents('tbody').attr('id');
        var isTest = container.parents('div').attr('isTest');
        var idOffer = container.parents('div').attr('offerId');
        var idAdditionalCost = container.attr('idAdditionalCost');

        var oldValue = sender.attr('oldValue');
        var value = sender.val();
        if(!$.isNumeric(value)) {
            sender.val(oldValue);
            alert("Value must be a number.");
        }
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveAdditionalCostsValues') }}",
                data:{
                    isTest: isTest,
                    idOffer: idOffer,
                    idAdditionalCost: idAdditionalCost,
                    column: column,
                    value: value
                },
                beforeSend: function() {
                    sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
                },
                success:function(data){
                    if(divId === "additionalTestsBody"){
                        $('.'+divId).html(data.html);
                    }else{
                        $('#'+divId).html(data.html);
                    }
                    $.NotificationApp.send("Success message!", "Test data updated successfully", 'top-right', '#fff', 'success');
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    if(data.totalsOffer.trim().length > 0)
                        $('#totalsOfferDiv').html(data.totalsOffer);
                },complete: function() {
                    sender.css("background", "#FFF");
                }
            });
        }
    }

    $('#addTestAdditionalCostSave').on('click', function (event) {
        event.preventDefault();

        var offerId = $('[name=offer_id]').val();
        var name = $('[name=testAdditionalCostName]').val();

        if(name != "") {
            var addTestAdditionalCostSave = $("#addTestAdditionalCostSave").html();
            $("#addTestAdditionalCostSave").html('<span class="spinner-border spinner-border-sm" style="margin: 0 auto;" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('offers.addAdditionalCost') }}",
                data:{
                    offerId: offerId,
                    name: name,
                    is_test: '1'
                },
                success:function(data){
                    if(data.success) {
                        $.NotificationApp.send("Success message!", "Test data updated successfully", 'top-right', '#fff', 'success');
                        $('#addTestAdditionalCost [name=testAdditionalCostName]').val('');
                        location.reload();
                    }
                },complete: function() {
                    $("#addTestAdditionalCostSave").html(addTestAdditionalCostSave);
                },
            });
        }
        else
            $.NotificationApp.send("Error message!", "You need to write a name.", 'top-right', '#fff', 'error');
    });

    $('#additionalCostsDivSave').on('click', function (event) {
        event.preventDefault();

        var offerId = $('[name=offer_id]').val();
        var name = $('[name=basicAdditionalCostName]').val();

        if(name != "") {
            var additionalCostsDivSave = $("#additionalCostsDivSave").html();
            $("#additionalCostsDivSave").html('<span class="spinner-border spinner-border-sm" style="margin: 0 auto;" role="status"></span>');
            $.ajax({
                type:'POST',
                url:"{{ route('offers.addAdditionalCost') }}",
                data:{
                    offerId: offerId,
                    name: name,
                    is_test: '0'
                },
                success:function(data){
                    if(data.success) {
                        $.NotificationApp.send("Success message!", "Cost data updated successfully", 'top-right', '#fff', 'success');
                        $('#addBasicAdditionalCost [name=basicAdditionalCostName]').val('');
                        location.reload();
                    }
                },complete: function() {
                    $("#additionalCostsDivSave").html(additionalCostsDivSave);
                },
            });
        }
        else
            $.NotificationApp.send("Error message!", "You need to write a name.", 'top-right', '#fff', 'error');
    });

    $("#actionOfferSelection [name=select_action_category]").change( function() {
        var actionCategory = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('api.getActionsPerCategory') }}",
            data:{
                category: actionCategory,
                belongsTo: 'Offer'
            },
            success:function(data) {
                if(data.success) {
                    $('[name=action_selection]').empty();
                    $.each(data.cmbData, function(key, value) {
                        $('[name=action_selection]').append('<option value="'+ key +'">' + value +'</option>');
                    });
                }
            }
        });
    });

    $("#actionOfferSelection #resetBtn").click(function() {
        $('[name=action_selection]').empty();
        $("#actionOfferSelection").find('form').trigger('reset');
    });

    $('#actionOfferSelection').on('submit', function (event) {
        event.preventDefault();

        var orderId = $('#newTask').data('id');
        var actions = $('#actionOfferSelection [name=action_selection]').val();

        if(actions.length == 0)
            alert("You must select actions to add.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.addActionsToOffer') }}",
                data:{
                    orderId: orderId,
                    actions: actions
                },
                success:function(data) {
                    location.reload();
                }
            });
        }
    });

    $('#editSelectedActions').on('click', function () {
        $('#editSelectedOfferActions').find('form').trigger('reset');
        $('#editSelectedOfferActions [name=offer_action_id]').val(null);
        $('#editSelectedOfferActions').modal('show');
    });

    $('#editSelectedOfferActions').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#sendEditSelectedActions').on('click', function (event) {
        event.preventDefault();

        var offerActionId = $('#editSelectedOfferActions [name=offer_action_id]').val();

        var ids = [];
        $(":checked.selectorActionsOffer").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0 && offerActionId.trim().length == 0)
            alert("You must select actions to edit.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.editSelectedActions') }}",
                data:{
                    items: ids,
                    offerActionId: offerActionId,
                    toBeDoneBy: $('#editSelectedOfferActions [name=toBeDoneBy]').val(),
                    actionDate: $('#editSelectedOfferActions [name=action_date]').val(),
                    actionRemindDate: $('#editSelectedOfferActions [name=action_remind_date]').val(),
                    actionReceivedDate: $('#editSelectedOfferActions [name=action_received_date]').val(),
                    actionRemark: $('#editSelectedOfferActions [name=remark]').val()
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#deleteSelectedActions').on('click', function () {
        var ids = [];
        $(":checked.selectorActionsOffer").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select actions to delete.");
        else if(confirm("Are you sure that you want to delete the selected actions?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.deleteSelectedActions') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    /* Upload offer action document */
    $(document).on('click', '#uploadActionFile', function () {
        var offerActionId = $(this).data('id');

        $('#uploadActionDocument').modal('show');
        $('#uploadActionDocument [name=id]').val(offerActionId);
    });

    /* Edit offer action */
    $(document).on('click', '#editAction', function () {
        var offerActionId = $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('api.offer-action-by-id') }}",
            data:{
                id: offerActionId,
            },
            success:function(data) {
                var actionDate = null;
                if (data.offerAction.action_date != null) {
                    actionDate = new Date(data.offerAction.action_date);
                    //convert day to 2 digits
                    var actionDateDigitDay = (actionDate.getDate() < 10) ? '0' + actionDate.getDate() : actionDate.getDate();
                    //convert month to 2 digits
                    var actionDateDigitMonth = (actionDate.getMonth() < 9) ? '0' + (actionDate.getMonth()+1) : (actionDate.getMonth()+1);
                    actionDate = actionDate.getFullYear() + '-' + actionDateDigitMonth + '-' + actionDateDigitDay;
                }

                var actionRemindDate = null;
                if (data.offerAction.action_remind_date != null) {
                    actionRemindDate = new Date(data.offerAction.action_remind_date);
                    //convert day to 2 digits
                    var actionRemindDateDigitDay = (actionRemindDate.getDate() < 10) ? '0' + actionRemindDate.getDate() : actionRemindDate.getDate();
                    //convert month to 2 digits
                    var actionRemindDateDigitMonth = (actionRemindDate.getMonth() < 9) ? '0' + (actionRemindDate.getMonth()+1) : (actionRemindDate.getMonth()+1);
                    actionRemindDate = actionRemindDate.getFullYear() + '-' + actionRemindDateDigitMonth + '-' + actionRemindDateDigitDay;
                }

                var actionReceivedDate = null;
                if (data.offerAction.action_received_date != null) {
                    actionReceivedDate = new Date(data.offerAction.action_received_date);
                    //convert day to 2 digits
                    var actionReceivedDateDigitDay = (actionReceivedDate.getDate() < 10) ? '0' + actionReceivedDate.getDate() : actionReceivedDate.getDate();
                    //convert month to 2 digits
                    var actionReceivedDateDigitMonth = (actionReceivedDate.getMonth() < 9) ? '0' + (actionReceivedDate.getMonth()+1) : (actionReceivedDate.getMonth()+1);
                    actionReceivedDate = actionReceivedDate.getFullYear() + '-' + actionReceivedDateDigitMonth + '-' + actionReceivedDateDigitDay;
                }

                $('#editSelectedOfferActions [name=offer_action_id]').val(offerActionId);
                $('#editSelectedOfferActions [name=toBeDoneBy]').val(data.offerAction.toBeDoneBy);
                $('#editSelectedOfferActions [name=action_date]').val(actionDate);
                $('#editSelectedOfferActions [name=action_remind_date]').val(actionRemindDate);
                $('#editSelectedOfferActions [name=action_received_date]').val(actionReceivedDate);
                $('#editSelectedOfferActions [name=remark]').val(data.offerAction.remark);
                $('#editSelectedOfferActions').modal('show');
            }
        });
    });

    /* Update offer action status */
    $(document).on('click', '#saveStatus', function () {
       var actionId = $(this).data('id');
       var actionStatus = $(this).attr('name');
       var objectType = 'offer';
       $.ajax({
         type:'POST',
         url:"{{ route('api.update-action-status') }}",
         data:{
            id: actionId,
            status: actionStatus,
            objectType: objectType
         },
         success:function(data){
            if (!data.error) {
               $("#icon" + actionId).toggleClass('fa-check-circle fa-exclamation');
            }
         }
       });
    });

    $('#searchSelectedSpecies').on('click', function () {
        var ids = [];
        $(":checked.selectorSpecies").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0 || ids.length > 1)
            alert("You must select one species to search.");
        else {
            var idAnimal = ids[0];
            var idOffer = $('#addSpecies [name=offer_id]').val();

            $('#selectContinentCountryForMailing').trigger('reset');

            $('#selectContinentCountryForMailing [name=triggered_id]').val(idOffer);
            $('#selectContinentCountryForMailing [name=animal_id]').val(idAnimal);
            $('#selectContinentCountryModal').modal('show');
        }
    });

    $('#selectContinentCountryForMailing').submit(function (event) {
        event.preventDefault();

        var triggeredFrom = "offers";
        var idTriggered = $('#selectContinentCountryForMailing [name=triggered_id]').val();
        var idAnimal = $('#selectContinentCountryForMailing [name=animal_id]').val();
        var bodyText = $('#selectContinentCountryForMailing [name=select_body_text]:checked').val();
        var idArea = $('#selectContinentCountryForMailing [name=select_area]').val();
        var idCountry = $('#selectContinentCountryForMailing [name=select_country]').val();

        $('#selectContinentCountryModal').modal('hide');

        var url = "{{route('wanted.wantedEmailToSuppliers')}}?triggeredFrom=" + triggeredFrom + "&idTriggered=" + idTriggered + "&idAnimal=" + idAnimal + "&bodyText=" + bodyText + "&idArea=" + idArea + "&idCountry=" + idCountry;
        window.location = url;
    });

    $(".surpluses-filter-select2").on("change", function(){
        var id_species = $(this).val();
        var tempSelectedSpecies = $("#tempSelectedSpecies").html();
        $("#tempSelectedSpecies").addClass("disabled");
        $("#tempSelectedSpecies").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
        $.ajax({
            type:'GET',
            url:"{{ route('our-surplus.getRegionSpecies') }}",
            data:{
                id: id_species,
            },
            success:function(data){
                if(data.error){
                    $("#tempSelectedSpecies").html(tempSelectedSpecies);
                    $("#tempSelectedSpecies").removeClass("disabled");
                }else{
                    $('#addSpecies [name=region]').val(data.region);
                    $('#addSpecies [name=region_text]').val(data.text);
                    $("#tempSelectedSpecies").removeClass("disabled");
                    $("#tempSelectedSpecies").html(tempSelectedSpecies);
                }
            }
        });

    })


    $("#addSpecies [name=origin], #addSpecies [name=continent]").change(function(){
        var origin = $('#addSpecies [name=origin]').val();
        var continent = $('#addSpecies [name=continent]').val();
        $('.surpluses-filter-select2').attr("data-origin", origin);
        $('.surpluses-filter-select2').attr("data-region", continent);
        if(origin != "" && continent != ""){
            $('.surpluses-filter-select2').removeAttr("disabled");
        }else{
            $('.surpluses-filter-select2').attr("disabled", "disabled");
        }
    });


    $(document).on('click', '#setExtraFee', function () {
        var sender = $(this);
        var idOffer = sender.attr('offerId');

        $.ajax({
            type:'POST',
            url:"{{ route('offers.setExtraFee') }}",
            data:{
                idOffer: idOffer,
                column: "extra_fee",
                value: (sender.is(':checked')) ? 1 : 0
            },
            success:function(data){
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                if(data.totalsOffer.trim().length > 0)
                    $('#totalsOfferDiv').html(data.totalsOffer);
            }
        });
    });

    $(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var form = $('#offerForm');
    original = form.serialize();

    form.submit(function() {
        window.onbeforeunload = null
    })

    window.onbeforeunload = function() {
        if (form.serialize() != original)
            return 'Are you sure you want to leave?'
    }

    $('[name=delivery_country_id]').trigger('change');

    $('[name=offer_status]').trigger('change');

    if ($("[name=institution_client_id]").val() != null) {
        $("[name=institution_client_id]").trigger('change');
    }

    if ($("[name=institution_supplier_id]").val() != null) {
        $("[name=institution_supplier_id]").trigger('change');
    }else{
        $.ajax({
            type:'POST',
            url:"{{ route('api.institution-by-name') }}",
            data: {
                name: 'International Zoo Services',
            },
            success:function(data) {
                if (data.institution != null)
                {
                    // create the option and append to Select2
                    var newOption = new Option(data.institution.name.trim(), data.institution.id, true, true);
                    // Append it to the select
                    $('[name=institution_supplier_id]').append(newOption);
                    $('[name=institution_supplier_id]').val(data.institution.id).trigger('change');
                }
            }
        });
    }
    });

    $('[name=offer_status]').on('change', function(event) {
    event.preventDefault();
    ($(this).val() == "Pending") ? $('#fieldStatusLevel').show() : $('#fieldStatusLevel').hide();
    });

    $('input[name=filter_supplier_option]').change(function() {
    var checkedOption = $('input[name=filter_supplier_option]:checked').val();

    if (checkedOption == 'institution_supplier') {
        $('[name=supplier_id]').removeClass("d-none");
        $('#supplierSelect2').addClass("d-none");

        $("[name=institution_supplier_id]").prop('disabled', false);

        $("[name=supplier_id]").prop('disabled', false);
    }
    else {
        $('[name=supplier_id]').addClass("d-none");
        $('#supplierSelect2').removeClass("d-none");

        $("[name=institution_supplier_id]").prop('disabled', true);

        $("[name=supplier_id]").prop('disabled', true);
    }

    $("[name=institution_supplier_id]").val(null).trigger('change');

    $('[name=supplier_id]').empty();
    $('[name=supplier_id]').append('<option value="">- select -</option>');
    });

    $('[name=delivery_country_id]').change( function () {
    var value = $(this).val();
    var deliveryAirportId = $('[name=hidden_delivery_airport_id]').val();

    if(value != null) {
        $.ajax({
            type:'POST',
            url:"{{ route('countries.getAirportsByCountryId') }}",
            data:{
                value: value,
            },
            success:function(data) {
                if(data.success) {
                    $('[name=delivery_airport_id]').empty();
                    $('[name=delivery_airport_id]').append('<option value="">- select -</option>');
                    $.each(data.airports, function(key, value) {
                        var selected = (key == deliveryAirportId || data.total_airports == 1) ? 'selected' : '';

                        $('[name=delivery_airport_id]').append('<option value="'+ key +'" ' + selected + '>' + value +'</option>');
                    });
                }
            }
        });
    }
    else {
        $('[name=delivery_airport_id]').empty();
        $('[name=delivery_airport_id]').append('<option value="">- select -</option>');
    }
    });

    //Load institution contacts when institution client is selected.
    $('[name=institution_client_id]').on('change', function () {
    var offerId = $('[name=hidden_offer_id]').val();
    var clientId = $('[name=hidden_client_id]').val();
    var institutionId = $(this).val();

    if(institutionId != null) {
        $.ajax({
            type:'POST',
            url:"{{ route('api.institution-contacts') }}",
            data: {
                value: institutionId,
            },
            success:function(data) {
                $('[name=client_id]').empty();
                $('[name=client_id]').append('<option value="">- select client -</option>');

                $.each(data.contacts, function(i, item) {
                    var selected = (item.id == clientId || data.contacts.length == 1) ? 'selected' : '';

                    var full_name = '';
                    if(item.title)
                        full_name += item.title + " ";
                    if(item.first_name)
                        full_name += item.first_name + " ";
                    if(item.last_name)
                        full_name += item.last_name;

                    $('[name=client_id]').append('<option value="'+ item.id +'" ' + selected + '>' + full_name.trim() + " (" + item.email +')</option>');

                    if (selected.trim() == '')
                        $('[name=client_id]').addClass('text-danger');
                    else
                        $('[name=client_id]').removeClass('text-danger');
                });

                if (offerId.trim() == '') {
                    $('[name=delivery_country_id]').val(data.organization.country.id);
                    $('[name=delivery_country_id]').trigger('change');
                }

                // create the option and append to Select2
                var newOption = new Option(data.organization.name.trim(), data.organization.id, true, true);
                // Append it to the select
                $('[name=institution_client_id]').append(newOption);
            }
        });
    }
    });

    //Load institution contacts when institution supplier is selected.
    $('[name=institution_supplier_id]').on('change', function () {
    var supplierId = $('[name=hidden_supplier_id]').val();
    var institutionId = $(this).val();

    if(institutionId != null) {
        $.ajax({
            type:'POST',
            url:"{{ route('api.institution-contacts') }}",
            data: {
                value: institutionId,
            },
            success:function(data) {
                $('[name=supplier_id]').empty();
                $('[name=supplier_id]').append('<option value="">- select supplier -</option>');

                $.each(data.contacts, function(i, item) {
                    var selected = (item.id == supplierId || data.contacts.length == 1) ? 'selected' : '';

                    var full_name = '';
                    if(item.title)
                        full_name += item.title + " ";
                    if(item.first_name)
                        full_name += item.first_name + " ";
                    if(item.last_name)
                        full_name += item.last_name;

                    $('[name=supplier_id]').append('<option value="'+ item.id +'" ' + selected + '>' + full_name.trim() + " (" + item.email +')</option>');

                    if (selected.trim() == '')
                        $('[name=supplier_id]').addClass('text-danger');
                    else
                        $('[name=supplier_id]').removeClass('text-danger');
                });

                // create the option and append to Select2
                var newOption = new Option(data.organization.name.trim(), data.organization.id, true, true);
                // Append it to the select
                $('[name=institution_supplier_id]').append(newOption);
            }
        });
    }
    });

    //Load contacts country when contact client is selected.
    $('[name=contact_client_id], [name=contact_supplier_id]').on('change', function () {
    var contactId = $(this).val();
    if(contactId != null) {
        $.ajax({
            type:'POST',
            url:"{{ route('api.contacts-country') }}",
            data: {
                value: contactId,
            },
            beforeSend: function() {
                $('[name=delivery_country_id]').css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 94%");
            },
            success:function(data) {
                $('[name=delivery_country_id] option:selected').removeAttr("selected");
                $('[name=delivery_country_id]').val(data.contacts.country.id);
                $('[name=delivery_country_id]').trigger('change');
            },complete: function() {
                $('[name=delivery_country_id]').css("background", "#FFF");
            }
        });
    }
    });
    $("#edit_offer").on("click", function(){
    $(this).html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
    });

    $(".related_surplus").on("click", function () {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var t = $(this);
        var id = t.attr("data-id");
        var key = t.attr("data-key");
        var button = t.html();
        var show = t.attr("data-show");
        var id_data = "{{ $offer->id }}";
        $(".item_related_" + key).removeClass("d-none");
        if(show === "true") {
            t.html('<span class="spinner-border spinner-border-sm" role="status" style="float: right; margin: 0 45px 0 0;"></span>');
            t.attr("data-show", "false");

            $.ajax({
                type:'POST',
                url:"{{ route('offers.getItemRelatedSurplus') }}",
                dataType: "JSON",
                data: {
                    id: id,
                    id_data: id_data,
                    type: "offer"
                },
                success:function(data) {
                    if(data.error){

                    }else{
                        $(".item_related_" + key).html(data.content);
                    }
                },complete: function() {
                    t.html(button);
                    t.find("i").removeClass("mdi mdi-chevron-down");
                    t.find("i").addClass("mdi mdi-chevron-up");
                }
            });
        }else{
            t.attr("data-show", "true");
            t.find("i").removeClass("mdi mdi-chevron-up");
            t.find("i").addClass("mdi mdi-chevron-down");
            $(".item_related_" + key).addClass("d-none");
            $(".item_related_" + key).html("");

        }
    });

    $(".related_species").on("click", function () {
        var scroll_position = $(document).scrollTop();
        $('html, body').animate({
            scrollTop: scroll_position
        }, 200);
        var t = $(this);
        var id = t.attr("data-id");
        var key = t.attr("data-key");
        var button = t.html();
        var show = t.attr("data-show");
        var id_data = '{{ $offer->id }}';
        $(".item_related_species_same_" + key).removeClass("d-none");
        if (show === 'true') {
            t.html('<span class="spinner-border spinner-border-sm" role="status" style="margin-left: 4px;"></span>');
            t.attr("data-show", "false");

            $.ajax({
                type:'POST',
                url:'{{ route('offers.getSpeciesWithSameContinentAsOrigin') }}',
                dataType: 'JSON',
                data: {
                    id: id,
                    id_data: id_data,
                    type: 'offer'
                },
                success:function(data) {
                    if (data.error) {
                        $.NotificationApp.send('Error message!', data.message , 'top-right', '#bf441d', 'error');
                    } else {
                        $(".item_related_species_same_" + key).html(data.content);
                    }
                },
                complete: function() {
                    t.html(button);
                    t.find("i").removeClass("mdi mdi-chevron-down");
                    t.find("i").addClass("mdi mdi-chevron-up");
                }
            });
        } else {
            t.attr("data-show", "true");
            t.find("i").removeClass("mdi mdi-chevron-up");
            t.find("i").addClass("mdi mdi-chevron-down");
            $(".item_related_species_same_" + key).addClass("d-none");
            $(".item_related_species_same_" + key).html("");
        }
    });
});
function setOriginRegion(value){
        var sender = $(".originSpecies-"+value);
        var origin = sender.val();
        var idOfferSpecies = $(sender.parents('tr')).attr('offerSpeciesId');

        $.ajax({
            type:'POST',
            url:"{{ route('offers.setOriginRegion') }}",
            data:{
                idOfferSpecies: idOfferSpecies,
                origin: origin
            },
            beforeSend: function() {
                sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
            },
            success:function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                }
            },
            complete: function() {
                sender.css("background", "#FFF");
            }
        });
}

function setRegionRegion(value){
        var sender = $(".regionSpecies-"+value);
        var region = sender.val();
        var idOfferSpecies = $(sender.parents('tr')).attr('offerSpeciesId');

        $.ajax({
            type:'POST',
            url:"{{ route('offers.setOriginRegion') }}",
            data:{
                idOfferSpecies: idOfferSpecies,
                region: region
            },
            beforeSend: function() {
                sender.css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 50%");
            },
            success:function(data){
                if(data.error){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $.NotificationApp.send("Success message!", data.message, 'top-right', '#fff', 'success');
                }
            },
            complete: function() {
                sender.css("background", "#FFF");
            }
        });
}
</script>

<!--
   include jquery script to be executed in offer and order detail windows:
   - update costs via api
   - update background colors selectbox costs
-->
<script src="{{ asset('js/jquery-costs-status.js') }}"></script>

@endsection
