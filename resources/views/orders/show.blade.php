@extends('layouts.admin')
@section('page-css')
    <link href="{{ asset('css/stylesTables.css') }}" rel="stylesheet">
@endsection
@section('subnav-content')
    <ol class="breadcrumb border-0 m-0 bg-primary align-items-center">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">
            {!! Form::open(['id' => 'allOrdersForm', 'route' => 'orders.quickChangeOrder', 'method' => 'GET']) !!}
            <select class="form-control form-control-sm w-auto" id="orderFullNumberValue" name="orderFullNumberValue">
                @foreach ($allOrders as $orderRecord)
                    <option value="{{$orderRecord->id}}"
                            @if($orderRecord->id == $order->id) selected @endif>{{$orderRecord->full_number}}</option>
                @endforeach
            </select>
            {!! Form::close() !!}
        </li>
    </ol>
    {!! Form::hidden('selectedOrderTab', $selectedOrderTab) !!}
@endsection

@section('header-content')
<div class="d-flex align-items-center">
    <h1 class="h3 text-white">
        Order: {{$order->full_number}} Status: {{$order->order_status}}
    </h1>
</div>
<div class="d-flex align-items-center mb-3">
    <h5 class="text-white">
        @if(!empty($order->manager))
            Manager: {{ $order->manager->name }} {{ $order->manager->last_name }}
        @endif
    </h5>
    @if (Auth::user()->hasPermission('orders.update'))
        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-secondary ml-2"><i class="fas fa-edit"></i> Edit</a>
    @endif
    <div class="dropdown ml-2">
        <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Options
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            @if (Auth::user()->hasPermission('orders.order-documents'))
                <h6 class="dropdown-header">Documents</h6>
                <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'reservation_supplier']) }}" title="Create reservation supplier document" id="reservation_supplier"><i class="fas fa-file-pdf"></i>&nbsp;Reservation for supplier</a>
                <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'reservation_client']) }}" title="Create reservation client document" id="reservation_client"><i class="fas fa-file-pdf"></i>&nbsp;Reservation for client</a>
                <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'proforma_invoice']) }}" title="Create proforma invoice document" id="proforma_invoice"><i class="fas fa-file-pdf"></i>&nbsp;Proforma invoice</a>
                <a class="dropdown-item" href="#" id="packingList" title="Create packing list document" data-toggle="modal" data-target="#packingListDialog"><i class="fas fa-file-pdf"></i>&nbsp;Packing list</a>
                <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'statement_izs']) }}" title="Create Statement IZS" id="statement_izs"><i class="fas fa-file-pdf"></i>&nbsp;Statement IZS</a>
                @if (Auth::user()->hasPermission('offers.calculation-document'))
                    <a class="dropdown-item" href="{{ route('offers.create_offer_calculation_pdf', [$order->offer->id, 'order_details']) }}"><i class="fas fa-file-pdf"></i>&nbsp;Calculation details</a>
                @endif
                <a class="dropdown-item" id="showFilesTab" href="#"><i class="fas fa-file"></i> Files</a>
                <div class="dropdown-divider"></div>
            @endif
            <a class="dropdown-item" href="{{ route('offers.show', $order->offer->id) }}" title="Go to offer"><i class="fas fa-arrow-right"></i> Go to offer</a>
            @if (Auth::user()->hasPermission('orders.delete'))
                <div class="dropdown-divider"></div>
                {!! Form::open(['method' => 'DELETE', 'route' => ['orders.destroy', $order->id], 'onsubmit' => 'return confirm("Are you sure to delete this project?")']) !!}
                <button class="dropdown-item"><i class="fas fa-trash text-danger"></i> Delete order</button>
                {!! Form::close() !!}
            @endif
        </div>
    </div>
</div>

<div class="w-100 mt-n2 mb-2">
    <div class="d-flex flex-wrap">
        <div style="width: 16%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Client details</h5>
                </div>
                <div class="card-body p-2">
                    <b>Sales type: </b>{{ $order->sale_type }}<br>
                    <b>Sales currency: </b>{{ $order->sale_currency }}<br>
                    <b>Institution: </b>{{ ($order->client->organisation) ? $order->client->organisation->name : '' }}<br>
                    <b>Contact: </b>{{ $order->client->full_name }}<br>
                    <b>E-mail: </b><a href="{{ route('orders.sendEmailOption', [$order->id, 'to_email_link', 'details']) }}?email_to={{ $order->client->email }}" style="color: #4e73df; !important">{{ $order->client->email }}</a><br>
                    <b>Phone: </b>{{ $order->client->mobile_phone ?? "" }}<br>
                    <b>Country: </b>{{ ($order->client->country) ? $order->client->country->name : '' }}
                </div>
            </div>
        </div>

        <div style="width: 16%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Supplier details</h5>
                </div>
                <div class="card-body p-2">
                    @if ($order->supplier)
                        <b>Costs type: </b>{{ $order->cost_type }}<br>
                        <b>Costs currency: </b>{{ $order->cost_currency }}<br>
                        <b>Institution: </b>{{ ($order->supplier->organisation) ? $order->supplier->organisation->name : '' }}<br>
                        <b>Contact: </b>{{ $order->supplier->full_name }}<br>
                        <b>E-mail: </b><a href="{{ route('orders.sendEmailOption', [$order->id, 'to_email_link', 'details']) }}?email_to={{ $order->supplier->email }}" style="color: #4e73df; !important">{{ $order->supplier->email }}</a><br>
                        <b>Phone: </b>{{ $order->supplier->mobile_phone ?? "" }}<br>
                        <b>Country: </b>{{ ($order->supplier->country) ? $order->supplier->country->name : '' }}
                    @else
                        <b>Institution: </b>International Zoo Services<br>
                        <b>Contact: </b><br>
                        <b>E-mail: </b>izs@zoo-services.com<br>
                        <b>Phone: </b><br>
                        <b>Country: </b>The Netherlands
                    @endif
                </div>
            </div>
        </div>

        <div style="width: 17%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Other contacts details</h5>
                </div>
                <div class="card-body p-2">
                    <div class="overflow-auto" style="height: 150px;">
                        <div>
                            <u>Contact final destination</u><br>
                            @if ($order->contact_final_destination)
                                <b>Institution: </b>{{ ($order->contact_final_destination->organisation) ? $order->contact_final_destination->organisation->name : '' }}<br>
                                <b>Contact: </b>{{ $order->contact_final_destination->full_name }}<br>
                                <b>E-mail: </b>{{ $order->contact_final_destination->email }}<br>
                                <b>Country: </b>{{ ($order->contact_final_destination->country) ? $order->contact_final_destination->country->name : '' }}
                            @endif
                        </div><br>
                        <div>
                            <u>Contact origin</u><br>
                            @if ($order->contact_origin)
                                <b>Institution: </b>{{ ($order->contact_origin->organisation) ? $order->contact_origin->organisation->name : '' }}<br>
                                <b>Contact: </b>{{ $order->contact_origin->full_name }}<br>
                                <b>E-mail: </b>{{ $order->contact_origin->email }}<br>
                                <b>Country: </b>{{ ($order->contact_origin->country) ? $order->contact_origin->country->name : '' }}
                            @endif
                        </div><br>
                        <div>
                            <u>Airfreight agent</u><br>
                            @if ($order->airfreight_agent)
                                <b>Institution: </b>{{ ($order->airfreight_agent->organisation) ? $order->airfreight_agent->organisation->name : '' }}<br>
                                <b>Contact: </b>{{ $order->airfreight_agent->full_name }}<br>
                                <b>E-mail: </b>{{ $order->airfreight_agent->email }}<br>
                                <b>Country: </b>{{ ($order->airfreight_agent->country) ? $order->airfreight_agent->country->name : '' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="width: 17%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <div class="d-flex justify-content-between">
                        <h5>Tasks</h5>
                        <div>
                            <a href="#" title="New task" class="btn btn-sm btn-dark" id="newTask" data-toggle="modal" data-id="{{ $order->id }}"><i class="fas fa-plus"></i> New</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="overflow-auto" style="height: 150px;">
                        @unless($order->order_today_tasks->isEmpty() && $order->order_other_tasks->isEmpty())
                            @foreach( $order->order_today_tasks as $todayTask )
                                <div>
                                    <a href="{{ route('orders.deleteOrderTask', $todayTask->id) }}" onclick="return confirm('Are you sure you want to delete this task?')"><i class="fas fa-window-close"></i></a>
                                    <a href="#" id="editTask" data-toggle="modal" data-id="{{ $todayTask->id }}" title="Edit task">{{ $todayTask->description }} ({{$todayTask->action}})</a>
                                    (<b>Due date: </b>{{ ($todayTask->due_date != null) ? date('d-m-Y', strtotime($todayTask->due_date)) : '' }})
                                </div>
                                <div>
                                    <b>Finished date: </b>{{ ($todayTask->finished_at != null) ? date('d-m-Y', strtotime($todayTask->finished_at)) : '' }}
                                </div>
                            @endforeach
                            @foreach( $order->order_other_tasks as $otherTask )
                                <div>
                                    <a href="{{ route('orders.deleteOrderTask', $otherTask->id) }}" onclick="return confirm('Are you sure you want to delete this task?')"><i class="fas fa-window-close"></i></a>
                                    <a href="#" id="editTask" data-toggle="modal" data-id="{{ $otherTask->id }}" title="Edit task">{{ $otherTask->description }} ({{$otherTask->action}})</a>
                                    (<b>Due date: </b>{{ ($otherTask->due_date != null) ? date('d-m-Y', strtotime($otherTask->due_date)) : '' }})
                                </div>
                                <div>
                                    <b>Finished date: </b>{{ ($otherTask->finished_at != null) ? date('d-m-Y', strtotime($otherTask->finished_at)) : '' }}
                                </div>
                            @endforeach
                        @else
                            <p>No tasks to to.</p>
                        @endunless
                    </div>
                </div>
            </div>
        </div>

        <div style="width: 17%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <div class="d-flex justify-content-between">
                        <h5>Remarks</h5>
                        <div>
                            <a href="#" class="btn btn-sm btn-dark" title="New remarks" data-toggle="modal" data-target="#seemoreRemarks"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="overflow-auto" style="height: 150px;">
                        <div class="remarkValue">
                            @if (!empty($order->order_remarks))
                                <a href="#" onclick="deleteRemarks()" id="remarkSaveDelete" title="Delete remar."><i class="fas fa-window-close mr-1"></i></a>
                            @endif
                            {{$order->order_remarks ?? ""}}
                        </div>
                        <div class="modal fade" name="seemoreRemarksModal" id="seemoreRemarks" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    {!! Form::model($order, ['method' => 'POST', 'route' => ['orders.updateRemark', $order->id], 'id' => 'remarkForm'] ) !!}
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
                                                    {!! Form::textarea('order_remarks', null, ['class' => 'form-control', 'rows' => '2']) !!}
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

        <div style="width: 17%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Invoices</h5>
                </div>
                <div class="card-body p-2">
                    <div class="overflow-auto" style="height: 150px;">
                        @foreach ($order_invoices as $invoice)
                            @if (($invoice->invoice_type == "credit"))
                                <div class="d-flex">
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id], 'onsubmit' => 'return confirm("Are you sure to delete this invoice?")']) !!}
                                        <a href="#" onclick="$(this).closest('form').submit();" title="Delete invoice."><i class="fas fa-window-close mr-2"></i></a>
                                    {!! Form::close() !!}
                                    <a href="#" title="Set invoice payment." id="setCreditInvoicePayment" data-id="{{ $invoice->id }}"><i class="fas fa-edit mr-2"></i></a>
                                    <a href="#" title="Edit and generate invoice again." id="editCreditInvoice" data-id="{{ $invoice->id }}"><i class="fas fa-file-export mr-2"></i></a>
                                    <a href="{{ route('orders.sendClientInvoice', [$order->id, $invoice->id]) }}"><i class="fas fa-envelope mr-2"></i></a>
                                    <a href="#" class="addDasboard mr-2" data-url="{{'/orders_docs/'.$order->full_number.'/outgoing_invoices/'.$invoice->invoice_file}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('orders_docs/'.$order->full_number.'/outgoing_invoices/'.$invoice->invoice_file)}}" target="_blank" title="Download invoice.">{{ $invoice->invoice_file }}</a>
                                </div>
                            @else
                                <div class="d-flex">
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id], 'onsubmit' => 'return confirm("Are you sure to delete this invoice?")']) !!}
                                        <a href="#" onclick="$(this).closest('form').submit();" title="Delete invoice."><i class="fas fa-window-close mr-2"></i></a>
                                    {!! Form::close() !!}
                                    <a href="#" id="setDebitInvoicePayment" title="Set invoice payment." data-id="{{ $invoice->id }}"><i class="fas fa-edit mr-2"></i></a>
                                    <a href="#" class="addDasboard mr-2" data-url="{{'/orders_docs/'.$order->full_number.'/incoming_invoices/'.$invoice->invoice_file}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('orders_docs/'.$order->full_number.'/incoming_invoices/'.$invoice->invoice_file)}}" target="_blank" title="Download invoice.">{{ $invoice->invoice_file }}</a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('main-content')

    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="orderTabs">
                <li class="nav-item">
                    <a class="nav-link" id="animals-tab" data-toggle="tab" href="#animalsTab" role="tab" aria-controls="animalsTab" aria-selected="false"><i class="fas fa-fw fa-paw"></i> Offer-prices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoicesTab" role="tab" aria-controls="invoicesTab" aria-selected="false"><i class="fas fa-fw fa-wallet"></i> Invoices</a>
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
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show" id="animalsTab" role="tabpanel" aria-labelledby="animals-tab">
                    @if (Auth::user()->hasPermission('offers.update'))
                        <div class="row align-items-start">
                            <div class="col-md-6 d-flex">
                                <div>
                                    <label class="mr-3"><input type="checkbox" id="selectAllSpecies" name="selectAllSpecies" class="ml-2"> Select all</label>
                                    <a href="#" class="mr-2 btn btn-primary btn-sm" title="Add species to offer" data-toggle="modal" data-target="#addSpecies"><i class="fas fa-plus"></i>&nbsp;Add species</a>
                                    <a href="#" class="mr-5 btn btn-danger btn-sm" id="deleteSelectedSpecies" title="Remove species from offer"><i class="fas fa-window-close"></i>&nbsp;Remove species</a>
                                </div>
                                <div>
                                    <label>
                                        <b>Cost price status:</b> {{ $order->cost_price_status }}
                                        <br><b>Realized date:</b> {{($order->realized_date) ? date('Y-m-d', strtotime($order->realized_date)) : '' }}
                                    </label>
                                </div>
                            </div>
                            @if ($order->sale_price_type != "ExZoo")
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Airfreight type:</label>
                                    <input type="radio" id="airfreightType" name="airfreightType" class="ml-2" value="volKgRates" @if($order->offer->airfreight_type == "volKgRates") checked @endif><label>&nbsp;vol.kg rate</label>
                                    <input type="radio" id="airfreightType" name="airfreightType" class="ml-2" value="byTruck" @if($order->offer->airfreight_type == "byTruck") checked @endif><label>&nbsp;by truck</label>
                                    <input type="radio" id="airfreightType" name="airfreightType" class="ml-2" value="pallets" @if($order->offer->airfreight_type == "pallets") checked @endif><label>&nbsp;pallets</label>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div id="offerSpeciesTable" class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                                @include('offers.offer_species_table')
                            </div>
                        </div>
                    </div>

                    @if ($order->sale_price_type != "ExZoo")
                        <div class="row">
                            <div class="col-md-12">
                                <div id="offerSpeciesCratesTable" class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                                    @include('offers.offer_species_crates_table')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($order->sale_price_type != "ExZoo" && $offer->airfreight_type == "volKgRates")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;" id="offerSpeciesAirfreightsTable">
                                    @include('offers.offer_species_airfreights_table', ['offer_airfreight_type' => 'order_volKg'])
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($order->sale_price_type != "ExZoo")
                        @if ($order->offer->airfreight_type == "byTruck")
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
                                                                        {!! Form::hidden('offer_id', $order->offer->id, ['class' => 'form-control']) !!}
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

                        @if ($order->offer->airfreight_type == "pallets")
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
                                                                @if (Auth::user()->hasPermission('airfreights.create') && $order->offer->airfreight_pallet == null)
                                                                    <a href="{{ route('airfreights.create', [$order->offer->id, 'offer_pallet']) }}" class="save save-airfreight d-flex center mb-2" title="Add new airfreight">
                                                                        <i class="fas fa-plus"></i>&nbsp;Add airfreight
                                                                    </a>
                                                                @endif
                                                                <a href="#" title="Select airfreight pallet" class="save save-airfreight d-flex center mb-2" id="selectAirfreightPallet" data-toggle="modal" data-id="{{ $order->offer->id }}" isPallet="1">
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
                                <div class="table-responsive" id="additionalTestsDiv" offerId="{{ $order->offer->id }}" isTest="1">
                                    <table class="table " id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                        <tr class="green header">
                                            <th class="none"><i class="fas fa-syringe fa-sm fa-fw mr-3 ml-2 fa-1x fa-rotate-270 text-black-400" aria-hidden="true"></i><span >TESTS</span></th>
                                            @if (Auth::user()->hasPermission('offers.see-cost-prices'))
                                                <th class="none"><span > TEST COSTS</span></th>
                                            @endif
                                            @if (Auth::user()->hasPermission('offers.see-sale-prices'))
                                                <th class="none"><span >TEST SALES</span></th>
                                            @endif
                                            @if (Auth::user()->hasPermission('offers.see-profit-value'))
                                                <th class="none"><span >TST.PROFIT</span></th>
                                            @endif
                                        </tr>
                                        </thead>
                                        @if (Auth::user()->hasPermission('offers.update'))
                                            {!! Form::open(['id' => 'addTestAdditionalCost', 'class' => 'form-inline mt-n3 mb-2']) !!}
                                            <tr class="greengray header">
                                                <th >
                                                    <div class="d-flex mt-2 mb-2 ml-3">
                                                        {!! Form::text('testAdditionalCostName', null, ['class' => 'form-control', 'placeholder' => 'Enter testname to add...']) !!}
                                                        {!! Form::hidden('offer_id', $order->offer->id, ['class' => 'form-control']) !!}
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="save d-flex center mb-2" id="addTestAdditionalCostSave" type="submit" style="margin: 8px 0 0 9px;">
                                                        <i class="fas fa-save align ml-2 " aria-hidden="true"></i>
                                                        <p class="align m-0 ml-2">Add test</p>
                                                    </div>
                                                </th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            {!! Form::close() !!}
                                        @endif
                                        <tbody id="additionalTestsBody">
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
                                                        {!! Form::hidden('offer_id', $order->offer->id, ['class' => 'form-control']) !!}
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
                            <div class="table-responsive" id="totalAndProfitSection" style="overflow-x: auto; overflow-y: hidden;">
                                @include('offers.total_and_profit_table')
                            </div>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->hasPermission('orders.order-invoices'))
                    <div class="tab-pane fade show" id="invoicesTab" role="tabpanel" aria-labelledby="invoices-tab">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5 class="text-center text-success">Order totals</h5>
                                <div class="table-responsive" id="totalsOfferDiv" orderId="{{ $order->id }}">
                                    @include('offers.totals_offer_table')
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="accordion mb-3" id="createInvoice">
                                    <div class="card mb-1">
                                        <a href="#" class="pl-2" data-toggle="collapse" data-target="#collapseCreateInvoice" aria-expanded="true" aria-controls="collapseCreateInvoice">
                                            <h5>Create invoice</h5>
                                        </a>
                                        <div id="collapseCreateInvoice" class="collapse" aria-labelledby="headingCreateInvoice" data-parent="#createInvoice">
                                            <div class="card-body">
                                                {!! Form::open(['id' => 'createInvoice', 'route' => 'orders.create_invoice', 'class' => 'mt-n3']) !!}
                                                <div class="row mb-2">
                                                    <div class="col-md-2">
                                                        {!! Form::label('payment_type', 'Payment type', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::select('payment_type', $payment_type, null, ['id' => 'payment_type', 'class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                                                        {!! Form::hidden('order_id', $order->id, ['id' => 'order_id', 'class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {!! Form::label('invoice_percent', 'Percent', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::text('invoice_percent', null, ['id' => 'invoice_percent', 'class' => 'form-control', 'required']) !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {!! Form::label('invoice_amount', 'Amount', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::text('invoice_amount', null, ['id' => 'invoice_amount', 'class' => 'form-control', 'required']) !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {!! Form::label('bank_account_number', 'No.', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::text('bank_account_number', $invoiceBankAccountNo, ['id' => 'bank_account_number', 'class' => 'form-control', 'required']) !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {!! Form::label('invoice_date', 'Date', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::date('invoice_date', \Carbon\Carbon::now(), ['id' => 'invoice_date', 'class' => 'form-control', 'required']) !!}
                                                    </div>
                                                    <div class="col-md-2 align-self-end mb-1">
                                                        <button class="btn btn-primary" type="submit">Create</button>
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion" id="uploadInvoice">
                                    <div class="card mb-1">
                                        <a href="#" class="pl-2" data-toggle="collapse" data-target="#collapseUploadInvoice" aria-expanded="true" aria-controls="collapseUploadInvoice">
                                            <h5>Upload invoice</h5>
                                        </a>
                                        <div id="collapseUploadInvoice" class="collapse" aria-labelledby="headingUploadInvoice" data-parent="#uploadInvoice">
                                            <div class="card-body">
                                                {!! Form::open(['id' => 'uploadOrderInvoice', 'route' => 'orders.upload_invoice', 'class' => 'mt-n3', 'files' => 'true']) !!}
                                                <div class="row mb-2">
                                                    <div class="col-md-2">
                                                        {!! Form::label('invoice_from', 'From', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::select('invoice_from', $invoice_from, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                                                        {!! Form::hidden('order_id', $order->id, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {!! Form::label('bank_account', 'Bank account', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::select('bank_account_id', $bankAccounts, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                                                    </div>
                                                    <div class="col-md-2">
                                                        {!! Form::label('invoice_amount', 'Amount', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::text('upload_invoice_amount', null, ['id' => 'upload_invoice_amount', 'class' => 'form-control', 'required']) !!}
                                                    </div>
                                                    <div class="col-md-6">
                                                        {!! Form::label('contact', 'Contact person', ['class' => 'font-weight-bold']) !!}
                                                        <select class="contact-select2 form-control" type="default" style="width: 100%" name="invoice_contact_id" required></select>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        {!! Form::label('invoice_file', 'File', ['class' => 'font-weight-bold']) !!}
                                                        {!! Form::file('file') !!}
                                                    </div>
                                                    <div class="col-md-2 align-self-end mb-1">
                                                        <button class="btn btn-primary" type="button" id="uploadInvoiceSubmitBtn">Upload</button>
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5 class="text-center text-success">Credit invoices</h5>
                                <table id="orderInvoices" class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th style="width: 100px;">Actions</th>
                                            <th>Invoice No.</th>
                                            <th>Curr</th>
                                            <th>Credit amount</th>
                                            <th>Paid amount</th>
                                            <th>Banking cost</th>
                                            <th>Invoice date</th>
                                            <th>Paid on</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order_invoices as $invoice)
                                            @if (($invoice->invoice_type == "credit"))
                                                <tr>
                                                    <td class="d-inline-flex">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id], 'onsubmit' => 'return confirm("Are you sure to delete this invoice?")']) !!}
                                                            <a href="#" onclick="$(this).closest('form').submit();" title="Delete invoice."><i class="fas fa-window-close mr-2"></i></a>
                                                        {!! Form::close() !!}
                                                        <a href="{{Storage::url('orders_docs/'.$order->full_number.'/outgoing_invoices/'.$invoice->invoice_file)}}" target="_blank" title="Download invoice."><i class="fas fa-file-alt mr-2"></i></a>
                                                        <a href="#" title="Set invoice payment." id="setCreditInvoicePayment" data-id="{{ $invoice->id }}"><i class="fas fa-edit mr-2"></i></a>
                                                        <a href="#" title="Edit and generate invoice again." id="editCreditInvoice" data-id="{{ $invoice->id }}"><i class="fas fa-file-export mr-2"></i></a>
                                                        <a href="{{ route('orders.sendClientInvoice', [$order->id, $invoice->id]) }}"><i class="fas fa-envelope"></i></a>
                                                    </td>
                                                    <td class="text-center">{{ $invoice->full_number }}</td>
                                                    <td class="text-center">{{ $invoice->invoice_currency }}</td>
                                                    <td class="text-center">{{ $invoice->invoice_percent . ' % - ' . number_format($invoice->invoice_amount, 2, '.', '') }}</td>
                                                    <td class="text-center">{{ number_format($invoice->paid_value, 2, '.', '') }}</td>
                                                    <td class="text-center">{{ number_format($invoice->banking_cost, 2, '.', '') }}</td>
                                                    <td class="text-center">{{($invoice->invoice_date) ? date('Y-m-d', strtotime($invoice->invoice_date)) : '' }}</td>
                                                    <td class="text-center">{{($invoice->paid_date) ? date('Y-m-d', strtotime($invoice->paid_date)) : ''}}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr class="font-weight-bold text-center text-danger">
                                            <td></td>
                                            <td>TOTAL:</td>
                                            <td></td>
                                            <td>{{ number_format($totalCreditInvoiceAmount, 2, '.', '') }}</td>
                                            <td>{{ number_format($totalCreditInvoicePaidAmount, 2, '.', '') }}</td>
                                            <td>{{ number_format($totalCreditInvoiceBankingCostAmount, 2, '.', '') }}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5 class="text-center text-success">Debit invoices</h5>
                                <table id="orderInvoices" class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th style="width: 100px;">Actions</th>
                                            <th>Invoice from</th>
                                            <th>Curr</th>
                                            <th>Debit amount</th>
                                            <th>Paid amount</th>
                                            <th>Banking cost</th>
                                            <th>Invoice date</th>
                                            <th>Paid on</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order_invoices as $invoice)
                                            @if (($invoice->invoice_type == "debit"))
                                                <tr>
                                                    <td class="d-inline-flex">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id], 'onsubmit' => 'return confirm("Are you sure to delete this invoice?")']) !!}
                                                            <a href="#" onclick="$(this).closest('form').submit();" title="Delete invoice."><i class="fas fa-window-close mr-2"></i></a>
                                                        {!! Form::close() !!}
                                                        <a href="{{Storage::url('orders_docs/'.$order->full_number.'/incoming_invoices/'.$invoice->invoice_file)}}" target="_blank" title="Download invoice."><i class="fas fa-file-alt mr-2"></i></a>
                                                        <a href="#" id="setDebitInvoicePayment" title="Set invoice payment." data-id="{{ $invoice->id }}"><i class="fas fa-edit mr-2"></i></a>
                                                    </td>
                                                    <td class="text-center">{{ $invoice->invoice_from }}</td>
                                                    <td class="text-center">{{ $invoice->invoice_currency }}</td>
                                                    <td class="text-center">{{ number_format($invoice->invoice_amount, 2, '.', '') }}</td>
                                                    <td class="text-center">{{ number_format($invoice->paid_value, 2, '.', '') }}</td>
                                                    <td class="text-center">{{ number_format($invoice->banking_cost, 2, '.', '') }}</td>
                                                    <td class="text-center">{{($invoice->invoice_date) ? date('Y-m-d', strtotime($invoice->invoice_date)) : '' }}</td>
                                                    <td class="text-center">{{($invoice->paid_date) ? date('Y-m-d', strtotime($invoice->paid_date)) : ''}}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr class="font-weight-bold text-center text-danger">
                                            <td></td>
                                            <td>TOTAL:</td>
                                            <td></td>
                                            <td>{{ number_format($totalDebitInvoiceAmount, 2, '.', '') }}</td>
                                            <td>{{ number_format($totalDebitInvoicePaidAmount, 2, '.', '') }}</td>
                                            <td>{{ number_format($totalDebitInvoiceBankingCostAmount, 2, '.', '') }}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="tab-pane fade show" id="actionsTab" role="tabpanel" aria-labelledby="actions-tab">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-6">
                            <label class="mr-3"><input type="checkbox" id="selectAllActions" name="selectAllActions" class="ml-1"> Select all</label>
                            <a href="#" class="btn btn-primary btn-sm mr-2" title="Add actions to order" data-toggle="modal" data-target="#actionOrderSelection"><i class="fas fa-plus"></i>&nbsp;Add actions</a>
                            <a href="javascript:void(0)" class="btn btn-secondary btn-sm mr-2" id="editSelectedActions" title="Edit order actions"><i class="fas fa-window-close"></i>&nbsp;Edit actions</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm" id="deleteSelectedActions" title="Remove order actions"><i class="fas fa-window-close"></i>&nbsp;Remove actions</a>
                        </div>
                    </div>
                    <table class="table table-sm" style="overflow-x: auto; overflow-y: hidden;" width="100%" cellspacing="0">
                        <tbody>
                            <tr class="table-success text-center">
                                <td colspan="8">RESERVATION</td>
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
                            @foreach ($reservationActions as $reservationAction)
                                <tr>
                                    <td class="d-inline-flex">
                                        <input type="checkbox" class="selectorActionsOrder mr-2" value="{{ $reservationAction->id }}">
                                        <a href="javascript:void(0)" title="Edit order action" id="editAction" data-id="{{ $reservationAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="javascript:void(0)" title="Upload file" id="uploadActionFile" data-id="{{ $reservationAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('orders.sendEmailOption', [$reservationAction->id, $reservationAction->action->action_code, true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $reservationAction->id }}"><i class="fas fa-envelope"></i></a>
                                        <a href="javascript:void(0)" id="saveStatus" name="{{ $reservationAction->status }}" data-id="{{ $reservationAction->id }}"><i id="icon{{ $reservationAction->id }}" class="fas fa-@php echo ($reservationAction->status === 'pending') ? 'exclamation' : 'check-circle'@endphp"></i></a>
                                    </td>
                                    <td>{{ $reservationAction->action->action_description }}</td>
                                    <td>{{ ($reservationAction->toBeDoneBy != null) ? $reservationAction->toBeDoneBy : $reservationAction->action->toBeDoneBy }}</td>
                                    <td>{{ ($reservationAction->action_date != null) ? date('Y-m-d', strtotime($reservationAction->action_date)) : '' }}</td>
                                    <td>{{ ($reservationAction->action_remind_date != null) ? date('Y-m-d', strtotime($reservationAction->action_remind_date)) : '' }}</td>
                                    <td>{{ ($reservationAction->action_received_date != null) ? date('Y-m-d', strtotime($reservationAction->action_received_date)) : '' }}</td>
                                    <td>{{ $reservationAction->remark }}</td>
                                    <td>
                                        @if ($reservationAction->action_document)
                                            <a href="{{ route('orders.delete_file', [$order->id, $reservationAction->action_document, 'order']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('orders_docs/'.$order->full_number.'/'.$reservationAction->action_document) }}" target="_blank">{{ $reservationAction->action_document }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr><td colspan="8">&nbsp;</td></tr>
                            <tr class="table-success text-center">
                                <td colspan="8">PERMIT APPLICATION</td>
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
                            @foreach ($permitActions as $permitAction)
                                <tr>
                                    <td class="d-inline-flex">
                                        <input type="checkbox" class="selectorActionsOrder mr-2" value="{{ $permitAction->id }}">
                                        <a href="javascript:void(0)" title="Edit order action" id="editAction" data-id="{{ $permitAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="javascript:void(0)" title="Upload file" id="uploadActionFile" data-id="{{ $permitAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('orders.sendEmailOption', [$permitAction->id, $permitAction->action->action_code, true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $permitAction->id }}"><i class="fas fa-envelope"></i></a>
                                        <a href="javascript:void(0)" id="saveStatus" name="{{ $permitAction->status }}" data-id="{{ $permitAction->id }}"><i id="icon{{ $permitAction->id }}" class="fas fa-@php echo ($permitAction->status === 'pending') ? 'exclamation' : 'check-circle'@endphp"></i></a>
                                    </td>
                                    <td>{{ $permitAction->action->action_description }}</td>
                                    <td>{{ ($permitAction->toBeDoneBy != null) ? $permitAction->toBeDoneBy : $permitAction->action->toBeDoneBy }}</td>
                                    <td>{{ ($permitAction->action_date != null) ? date('Y-m-d', strtotime($permitAction->action_date)) : '' }}</td>
                                    <td>{{ ($permitAction->action_remind_date != null) ? date('Y-m-d', strtotime($permitAction->action_remind_date)) : '' }}</td>
                                    <td>{{ ($permitAction->action_received_date != null) ? date('Y-m-d', strtotime($permitAction->action_received_date)) : '' }}</td>
                                    <td>{{ $permitAction->remark }}</td>
                                    <td>
                                        @if ($permitAction->action_document)
                                            <a href="{{ route('orders.delete_file', [$order->id, $permitAction->action_document, 'cites_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$order->offer->full_number.'/cites_docs/'.$permitAction->action_document) }}" target="_blank">{{ $permitAction->action_document }}</a>
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
                                        <input type="checkbox" class="selectorActionsOrder mr-2" value="{{ $veterinaryAction->id }}">
                                        <a href="javascript:void(0)" title="Edit order action" id="editAction" data-id="{{ $veterinaryAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="javascript:void(0)" title="Upload file" id="uploadActionFile" data-id="{{ $veterinaryAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('orders.sendEmailOption', [$veterinaryAction->id, $veterinaryAction->action->action_code, true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $veterinaryAction->id }}"><i class="fas fa-envelope"></i></a>
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
                                            <a href="{{ route('orders.delete_file', [$order->id, $veterinaryAction->action_document, 'veterinary_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$order->offer->full_number.'/veterinary_docs/'.$veterinaryAction->action_document) }}" target="_blank">{{ $veterinaryAction->action_document }}</a>
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
                                        <input type="checkbox" class="selectorActionsOrder mr-2" value="{{ $crateAction->id }}">
                                        <a href="#" title="Edit order action" id="editAction" data-id="{{ $crateAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="#" title="Upload file" id="uploadActionFile" data-id="{{ $crateAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('orders.sendEmailOption', [$crateAction->id, $crateAction->action->action_code, true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $crateAction->id }}"><i class="fas fa-envelope"></i></a>
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
                                            <a href="{{ route('orders.delete_file', [$order->id, $crateAction->action_document, 'crates']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$order->offer->full_number.'/crates/'.$crateAction->action_document) }}" target="_blank">{{ $crateAction->action_document }}</a>
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
                                        <input type="checkbox" class="selectorActionsOrder mr-2" value="{{ $transportAction->id }}">
                                        <a href="#" title="Edit order action" id="editAction" data-id="{{ $transportAction->id }}"><i class="fas fa-edit mr-2"></i></a>
                                        <a href="#" title="Upload file" id="uploadActionFile" data-id="{{ $transportAction->id }}"><i class="fas fa-upload mr-2"></i></a>
                                        <a href="{{ route('orders.sendEmailOption', [$transportAction->id, $transportAction->action->action_code, true]) }}" title="Send action email" id="sendActionEmail" data-id="{{ $transportAction->id }}"><i class="fas fa-envelope"></i></a>
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
                                            <a href="{{ route('orders.delete_file', [$order->id, $transportAction->action_document, 'airfreight']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close mr-1"></i></a>
                                            <a href="{{ Storage::url('offers_docs/'.$order->offer->full_number.'/airfreight/'.$transportAction->action_document) }}" target="_blank">{{ $transportAction->action_document }}</a>
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
                            @if (Auth::user()->hasPermission('orders.upload-files'))
                                <div class="mb-3 align-items-center">
                                    <form action="{{ route('orders.upload') }}" class="dropzone" id="upload-dropzone">
                                        @csrf
                                        <input type="hidden" name="orderId" value="{{ $order->id }}" />
                                        Document type:
                                        <label class="ml-1 mr-2"><input type="radio" name="docCategory" value="order" checked> Order</label>
                                        <label class="mr-2"><input type="radio" name="docCategory" value="airfreight"> Airfreight</label>
                                        <label class="mr-2"><input type="radio" name="docCategory" value="crates"> Crates</label>
                                        <label class="mr-2"><input type="radio" name="docCategory" value="cites_docs"> Cites docs</label>
                                        <label class="mr-2"><input type="radio" name="docCategory" value="veterinary_docs"> Veterinary docs</label>
                                        <label class="mr-2"><input type="radio" name="docCategory" value="documents"> General docs</label>
                                        <label class="mr-2"><input type="radio" name="docCategory" value="suppliers_offers"> Offers of suppliers</label>
                                        <label><input type="radio" name="docCategory" value="others"> Others</label>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-2 border-right">
                            <h6>Order documents</h6>
                            <div>
                                @foreach($order->order_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('orders.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'order']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/orders_docs/'.$order->full_number.'/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('orders_docs/'.$order->full_number.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Outgoing invoices</h6>
                            <div>
                                @foreach($order->outgoing_invoices as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('orders.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'outgoing_invoices']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/orders_docs/'.$order->full_number.'/outgoing_invoices/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('orders_docs/'.$order->full_number.'/outgoing_invoices/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Incoming invoices</h6>
                            <div>
                                @foreach($order->incoming_invoices as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('orders.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'incoming_invoices']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/orders_docs/'.$order->full_number.'/incoming_invoices/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('orders_docs/'.$order->full_number.'/incoming_invoices/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Airfreight</h6>
                            <div>
                                @foreach($order->offer->airfreight_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'airfreight']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/orders_docs/'.$order->offer->full_number.'/airfreight/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/airfreight/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Crates</h6>
                            <div>
                                @foreach($order->offer->crates_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'crates']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/orders_docs/'.$order->offer->full_number.'/crates/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/crates/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Documents</h6>
                            <h6 class="badge">Cites docs</h6>
                            <div>
                                @foreach($order->offer->cites_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'cites_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/orders_docs/'.$order->offer->full_number.'/cites_docs/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/cites_docs/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                            <div class="dropdown-divider"></div>
                            <h6 class="badge">Veterinary docs</h6>
                            <div>
                                @foreach($order->offer->veterinary_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'veterinary_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/offers_docs/'.$order->offer->full_number.'/veterinary_docs/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/veterinary_docs/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                            <div class="dropdown-divider"></div>
                            <h6 class="badge">General docs</h6>
                            <div>
                                @foreach($order->offer->documents_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'documents']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/offers_docs/'.$order->offer->full_number.'/documents/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/documents/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 border-right">
                            <h6>Offers of suppliers</h6>
                            <div>
                                @foreach($order->offer->suppliers_offers as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('offers.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'suppliers_offers']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/offers_docs/'.$order->offer->full_number.'/suppliers_offers/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/suppliers_offers/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 border-right">
                            <h6>Offer others</h6>
                            <div>
                                @foreach($order->offer->others_docs as $doc)
                                    @php
                                        $file = pathinfo($doc);
                                    @endphp
                                    @if (Auth::user()->hasPermission('orders.delete-files'))
                                        <a href="{{ route('orders.delete_file', [$order->id, $file['basename'], 'others']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="#" class="addDasboard" data-url="{{'/offers_docs/'.$order->offer->full_number.'/'.$file['basename']}}"><i class="mdi mdi-view-dashboard"></i></a>
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
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

@include('offers.add_species_modal', ['modalId' => 'addSpecies', 'offerId' => $order->offer->id])

@include('tasks.task_form_modal', ['modalId' => 'orderTaskForm', 'route' => 'orders.orderTask'])

@include('offers.selected_species_airfreight_selection_modal', ['modalId' => 'setSpeciesAirfreightsValues'])
@include('offers.species_airfreight_selection_modal', ['modalId' => 'selectSpeciesAirfreight'])
@include('offers.offer_pallet_selection_modal', ['modalId' => 'selectOfferAirfreightPallet'])

@include('orders.invoice_preview_modal', ['modalId' => 'invoicePreview'])
@include('orders.document_preview_modal', ['modalId' => 'documentPreview'])

@include('orders.edit_invoice_modal', ['modalId' => 'editInvoiceDialog'])
@include('orders.set_invoice_payment_modal', ['modalId' => 'setInvoicePaymentDialog'])

@include('orders.packing_list_modal', ['modalId' => 'packingListDialog'])

@include('orders.select_action_modal', ['modalId' => 'actionOrderSelection'])
@include('orders.edit_selected_actions_modal', ['modalId' => 'editSelectedOrderActions'])
@include('uploads.upload_modal', ['modalId' => 'uploadActionDocument', 'route' => 'orders.uploadOrderActionDocument'])
@include('general_documents.add_document_modal', ['modalId' => 'uploadGeneralDoc'])


@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    var invoiceDocEditor, orderDocEditor = '';

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
        height: 300,
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
    };

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

    $('#orderTabs a[href="'+$('[name=selectedOrderTab]').val()+'"]').tab('show');

    $('#orderTabs a').on('click', function (e) {
        e.preventDefault();

        var orderId = $('#newTask').data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('orders.selectedOrderTab') }}",
            data:{
                orderTab: $(this).attr('href'),
                orderId: orderId
            },
            success:function(data) {
                $('#totalsOfferDiv').html(data.html);
                $(this).tab('show');
            }
        });
    })

    $('#showFilesTab').on('click', function () {
        $.ajax({
            type:'POST',
            url:"{{ route('orders.selectedOrderTab') }}",
            data:{
                orderTab: "#filesTab"
            },
            success:function(data){
                $('#orderTabs a[href="#filesTab"]').tab('show'); // Select tab by name
            }
        });
    });

    $('#orderFullNumberValue').on('change', function () {
        $('#allOrdersForm').submit();
    });

    $(':checkbox:checked.selectorSpecies').prop('checked', false);

    $('#selectAllSpecies').on('change', function () {
        $(":checkbox.selectorSpecies").prop('checked', this.checked);
    });

    $(':checkbox:checked.selectorSpeciesAirfreight').prop('checked', false);

    $(document).on('change', '#selectAllSpeciesAirfreight', function () {
        $(":checkbox.selectorSpeciesAirfreight").prop('checked', this.checked);
    });

    $(':checkbox:checked.selectorActionsOrder').prop('checked', false);

    $('#selectAllActions').on('change', function () {
        $(":checkbox.selectorActionsOrder").prop('checked', this.checked);
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

        var addSpeciesButton = $("#addSpeciesButton").html();
        $("#addSpeciesButton").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
        $.ajax({
            type:'POST',
            url:"{{ route('offers.addOfferSpecies') }}",
            data:{
                items: selectedSurplus,
                offerId: $('#addSpecies [name=offer_id]').val()
            },
            success:function(data){
                $.NotificationApp.send("Success message!", "Add New Species successfully", 'top-right', '#fff', 'success');
                location.reload();
                /*alert(data.msg);*/
            },complete: function() {
                $("#addSpeciesButton").html(addSpeciesButton);
            },
        });
    });

    $('#remarkSave').on('click', function (event) {
        event.preventDefault();
        var btn_save = $('#remarkSave').html();
        $('#remarkSave').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        $.ajax({
            type:'POST',
            url:"{{ route('orders.updateRemark') }}",
            data:{
                id: '{{$order->id}}',
                remarks: $('#seemoreRemarks [name=order_remarks]').val()
            },
            success:function(data) {
                if(typeof data.error != "undefined" && data.error === true){
                    $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
                }else{
                    $('.remarkValue').html('<a href="#" onclick="deleteRemarks()" title="Delete remar."><i class="fas fa-window-close mr-1"></i></a>' + data.remark);
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
                success:function(data){
                    $.NotificationApp.send("Success message!", "Species removed successfully", 'top-right', '#fff', 'success');
                    location.reload();
                },complete: function() {
                    $("#deleteSelectedSpecies").html(deleteSelectedSpecies);
                },
            });
        }
    });

    /* New task modal dialog */
    $('#newTask').on('click', function () {
        var orderId = $(this).data('id');

        $('#orderTaskForm').modal('show');
        $('#orderTaskForm [name=id]').val(orderId);
        $("#orderTaskForm [name=due_date]").prop('disabled', true);
    });

    /* Edit order task */
    $(document).on('click', '#editTask', function () {
        var taskId = $(this).data('id');
        var orderId = $('#newTask').data('id');

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

                $('#orderTaskForm [name=id]').val(orderId);
                $('#orderTaskForm [name=task_id]').val(taskId);
                $('#orderTaskForm [name=description]').val(data.task.description);
                $('#orderTaskForm [name=action]').val(data.task.action);
                $('#orderTaskForm [name=due_date]').val(dueDate);
                $('#orderTaskForm [name=user_id]').val(data.task.user_id);
                $('#orderTaskForm').modal('show');
                $("#orderTaskForm [name=due_date]").prop('disabled', true);
            }
        });
    });

    $('#orderTaskForm input[name=quick_action_dates]').change(function() {
        var quickActionDate = $('#orderTaskForm input[name=quick_action_dates]:checked').val();

        if (quickActionDate == 'specific')
            $("#orderTaskForm [name=due_date]").prop('disabled', false);
        else
            $("#orderTaskForm [name=due_date]").prop('disabled', true);
    });

    $('#orderTaskForm').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $(document).on('click', '#airfreightType', function () {
        var value = $(this).val();
        var idOffer = $('#additionalTestsDiv').attr('offerId');

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
            alert('Value must be a number.');
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
                $('#offerAirfreightPalletsDiv').html(data.offerSpeciesPallets);
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
            success:function(data){
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
            var buttonSpeciesAirfreights = $("#saveOfferAirfreightPalletSave").html();
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
                },
                complete: function() {
                    $("#saveOfferAirfreightPalletSave").html(buttonSpeciesAirfreights);
                },
            });
        }
        else
            alert("You need to select origin and destination.");
    });

    $(document).on('change', '#offerSpeciesAirfreightTable [name=cost_volKg]', offerSpeciesAirfreightChanged);
    $(document).on('change', '#offerSpeciesAirfreightTable [name=sale_volKg]', offerSpeciesAirfreightChanged);

    function offerSpeciesAirfreightChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');
        var idOfferSpeciesAirfreight = $(sender.parents('tr')).attr('speciesAirfreightId');

        var value = sender.val();

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
                        $.NotificationApp.send("Success message!", "Transport by truck data updated successfully", 'top-right', '#fff', 'success');
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

        var value = sender.val();

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

    $(document).on('change', '#offerTransportTruckTable [name=total_km]', offerTransportTruckChanged);
    $(document).on('change', '#offerTransportTruckTable [name=cost_rate_per_km]', offerTransportTruckChanged);
    $(document).on('change', '#offerTransportTruckTable [name=sale_rate_per_km]', offerTransportTruckChanged);

    function offerTransportTruckChanged() {
        var sender = $(this);
        var column = sender.attr('name');
        var idOfferTransportTruck = $(sender.parents('tr')).attr('offerTransportTruckId');
        var container = $(sender.parents('div'));
        var divId = container.attr('id');

        var value = sender.val();

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

        var value = sender.val();

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
                $('#'+divId).html(data.html);
                $.NotificationApp.send("Success message!", "Test data updated successfully", 'top-right', '#fff', 'success');
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                if(data.totalsOffer.trim().length > 0)
                    $('#totalsOfferDiv').html(data.totalsOffer);
            },complete: function() {
                sender.css("background", "#FFF");
            }
        });
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
                        $.NotificationApp.send("Success message!", "Basic Cost data updated successfully", 'top-right', '#fff', 'success');
                        $('#addBasicAdditionalCost [name=basicAdditionalCostName]').val('');
                        location.reload();
                    }
                },complete: function() {
                    $("#additionalCostsDivSave").html(additionalCostsDivSave);
                },
            });
        }
        else
            alert("You need to write a name.");
    });

    $(document).on('change', '#createInvoice [name=payment_type]', paymentTypeChanged);

    function paymentTypeChanged() {
        var sender = $(this);
        var value = sender.val();
        var idOrder = $('#newTask').data('id');

        if (value == 'deposit') {
            $('#createInvoice [name=invoice_percent]').val(30);
            $('#createInvoice [name=invoice_percent]').trigger('keyup');
        }
        else if (value == 'balance') {
            $.ajax({
                type:'POST',
                url:"{{ route('orders.getInvoicesBalancePercentLeft') }}",
                data:{
                    idOrder: idOrder
                },
                success:function(data){
                    $('#createInvoice [name=invoice_percent]').val(data.percent);
                    $('#createInvoice [name=invoice_amount]').val(data.amount);
                }
            });
        }
        else {
            $('#createInvoice [name=invoice_percent]').val("");
            $('#createInvoice [name=invoice_amount]').val("");
        }
    }

    $(document).on('keyup', '#createInvoice [name=invoice_percent]', invoiceAmountBasedOnPercent);

    function invoiceAmountBasedOnPercent() {
        var sender = $(this);
        var value = sender.val();
        var idOrder = $('#newTask').data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('orders.getInvoiceAmountBasedOnPercent') }}",
            data:{
                value: value,
                idOrder: idOrder
            },
            success:function(data){
                if(data.success)
                    $('#createInvoice [name=invoice_amount]').val(data.amount);
                else {
                    $('#createInvoice [name=invoice_percent]').val("");
                    $('#createInvoice [name=invoice_amount]').val("");
                    alert(data.msg);
                }
            }
        });
    }

    $(document).on('change', '#editInvoiceDialog [name=invoice_percent]', invoiceAmountBasedOnPercentDialog);

    function invoiceAmountBasedOnPercentDialog() {
        var sender = $(this);
        var value = sender.val();
        var idOrder = $('#newTask').data('id');
        var idInvoice = $('#editInvoiceDialog [name=invoice_id]').val();

        $.ajax({
            type:'POST',
            url:"{{ route('orders.getInvoiceAmountBasedOnPercent') }}",
            data:{
                value: value,
                idOrder: idOrder,
                idInvoice: idInvoice
            },
            success:function(data){
                if(data.success)
                    $('#editInvoiceDialog [name=invoice_amount]').val(data.amount);
                else {
                    $('#editInvoiceDialog [name=invoice_percent]').val(data.invoice.invoice_percent);
                    alert(data.msg);
                }
            }
        });
    }

    $('#createInvoice').on('submit', function (event) {
        event.preventDefault();

        var percentValue = $('#createInvoice #invoice_percent').val();
        var amountValue = $('#createInvoice #invoice_amount').val();

        if (!$.isNumeric(amountValue) || !$.isNumeric(percentValue))
            alert("Amount and percent values must be a valid number.");
        else if (amountValue == 0)
            alert("You cannot generate an invoice with amount in zero.");
        else {
            if (percentValue == 0 && !confirm("Are you sure that you want to generate an invoice with percent in zero?"))
                return;

            if (invoiceDocEditor) {
                invoiceDocEditor.destroy();
                invoiceDocEditor = null;
            }

            var form = document.forms.namedItem("createInvoice");
            var formdata = new FormData(form);

            $.ajax({
                type:'POST',
                url:"{{ route('orders.create_invoice') }}",
                contentType: false,
                data: formdata,
                processData: false,
                success:function(data){
                    if(data.success) {
                        if (data.checkRulesMessages)
                            alert(data.checkRulesMessages);

                        $('#invoicePreview').modal('show');
                        invoiceDocEditor = CKEDITOR.replace('invoice_html', config);
                        $('#invoicePreview [name=invoice_html]').val(data.invoice_info);
                        $('#invoicePreview [name=payment_type]').val(data.invoice_payment_type);
                        $('#invoicePreview [name=invoice_percent]').val(data.invoice_percent);
                        $('#invoicePreview [name=invoice_amount]').val(data.invoice_amount);
                        $('#invoicePreview [name=bank_account_number]').val(data.invoice_number);
                        $('#invoicePreview [name=invoice_date]').val(data.invoice_date);
                    }
                    else
                        alert(data.msg);
                }
            });
        }
    });

    $('#uploadInvoiceSubmitBtn').on('click', function (event) {
        event.preventDefault();

        var amountValue = $('#uploadOrderInvoice #upload_invoice_amount').val();

        if (!$.isNumeric(amountValue))
            alert("Amount value must be a valid number.");
        else if (amountValue == 0)
            alert("You cannot upload an invoice with amount in zero.");
        else
            $('#uploadOrderInvoice').submit();
    });

    $(document).on('click', '#editCreditInvoice', function () {
        var invoiceId = $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('invoices.ajaxGetInvoiceById') }}",
            data: {
                invoiceId: invoiceId
            },
            success:function(data){
                if(data.success) {
                    $('#editInvoiceDialog [name=invoice_id]').val(invoiceId);
                    $('#editInvoiceDialog [name=bank_account_number]').val(data.invoice.bank_account_number);
                    $('#editInvoiceDialog [name=invoice_date]').val(data.invoice.invoice_date);
                    $('#editInvoiceDialog [name=invoice_percent]').val(data.invoice.invoice_percent);
                    $('#editInvoiceDialog [name=invoice_amount]').val(data.invoice.invoice_amount);
                    $('#editInvoiceDialog').modal('show');
                }
            }
        });
    });

    $('#editInvoiceDialog').on('submit', function (event) {
        event.preventDefault();

        if (invoiceDocEditor) {
            invoiceDocEditor.destroy();
            invoiceDocEditor = null;
        }

        var form = document.forms.namedItem("editOrderInvoice");
        var formdata = new FormData(form);

        $.ajax({
            type:'POST',
            url:"{{ route('orders.editOrderInvoice') }}",
            contentType: false,
            data: formdata,
            processData: false,
            success:function(data) {
                if(data.success) {
                    $('#editInvoiceDialog').modal('hide');
                    $('#invoicePreview').modal('show');
                    invoiceDocEditor = CKEDITOR.replace('invoice_html', config);
                    $('#invoicePreview [name=invoice_html]').val(data.invoice_info);
                    $('#invoicePreview [name=invoice_id]').val(data.invoice_id);
                    $('#invoicePreview [name=payment_type]').val(data.invoice_payment_type);
                    $('#invoicePreview [name=invoice_percent]').val(data.invoice_percent);
                    $('#invoicePreview [name=invoice_amount]').val(data.invoice_amount);
                    $('#invoicePreview [name=bank_account_number]').val(data.invoice_number);
                    $('#invoicePreview [name=invoice_date]').val(data.invoice_date);
                }
            }
        });
    });

    $(document).on('click', '#setCreditInvoicePayment, #setDebitInvoicePayment', function () {
        var invoiceId = $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('invoices.ajaxGetInvoiceById') }}",
            data: {
                invoiceId: invoiceId
            },
            success:function(data){
                if(data.success) {
                    $('#setInvoicePaymentDialog [name=invoice_id]').val(invoiceId);
                    $('#setInvoicePaymentDialog [name=paid_value]').val(data.invoice.paid_value);
                    $('#setInvoicePaymentDialog [name=banking_cost]').val(data.invoice.banking_cost);
                    $('#setInvoicePaymentDialog [name=invoice_amount]').val(data.invoice.invoice_amount);
                    $('#setInvoicePaymentDialog [name=paid_date]').val(data.invoice.paid_date);
                    $('#setInvoicePaymentDialog [name=payment_type]').val(data.invoice.payment_type);
                    $('#setInvoicePaymentDialog').modal('show');
                }
            }
        });
    });

    $('#setInvoicePaymentDialog [name=paid_value]').on('change', function () {
        var value = $(this).val();
        var invoice_amount = $('#setInvoicePaymentDialog [name=invoice_amount]').val();
        var banking_cost = (invoice_amount-value);

        $('#setInvoicePaymentDialog [name=banking_cost]').val(banking_cost.toFixed(2));
    });

    $("#actionOrderSelection [name=select_action_category]").change( function() {
        var actionCategory = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('api.getActionsPerCategory') }}",
            data:{
                category: actionCategory,
                belongsTo: 'Order'
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

    $("#actionOrderSelection #resetBtn").click(function() {
        $('[name=action_selection]').empty();
        $("#actionOrderSelection").find('form').trigger('reset');
    });

    $('#actionOrderSelection').on('submit', function (event) {
        event.preventDefault();

        var orderId = $('#newTask').data('id');
        var actions = $('#actionOrderSelection [name=action_selection]').val();

        if(actions.length == 0)
            alert("You must select actions to add.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('orders.addActionsToOrder') }}",
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
        $('#editSelectedOrderActions').find('form').trigger('reset');
        $('#editSelectedOrderActions [name=order_action_id]').val(null);
        $('#editSelectedOrderActions').modal('show');
    });

    $('#editSelectedOrderActions').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#sendEditSelectedActions').on('click', function (event) {
        event.preventDefault();

        var orderActionId = $('#editSelectedOrderActions [name=order_action_id]').val();

        var ids = [];
        $(":checked.selectorActionsOrder").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0 && orderActionId.trim().length == 0)
            alert("You must select actions to edit.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('orders.editSelectedActions') }}",
                data:{
                    items: ids,
                    orderActionId: orderActionId,
                    toBeDoneBy: $('#editSelectedOrderActions [name=toBeDoneBy]').val(),
                    actionDate: $('#editSelectedOrderActions [name=action_date]').val(),
                    actionRemindDate: $('#editSelectedOrderActions [name=action_remind_date]').val(),
                    actionReceivedDate: $('#editSelectedOrderActions [name=action_received_date]').val(),
                    actionRemark: $('#editSelectedOrderActions [name=remark]').val()
                },
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#deleteSelectedActions').on('click', function () {
        var ids = [];
        $(":checked.selectorActionsOrder").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            alert("You must select actions to delete.");
        else if(confirm("Are you sure that you want to delete the selected actions?")) {
            $.ajax({
                type:'POST',
                url:"{{ route('orders.deleteSelectedActions') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    /* Upload order action document */
    $(document).on('click', '#uploadActionFile', function () {
        var orderActionId = $(this).data('id');

        $('#uploadActionDocument').modal('show');
        $('#uploadActionDocument [name=id]').val(orderActionId);
    });

    $(document).on('change', '#bank_account_number', function(e){
        $("#editInvoiceDialog #alert_bank_account_number").html('');
        $("#editInvoiceDialog #bank_account_number").removeClass("is-invalid");
        $("#editInvoiceDialog .number-new").removeClass('d-none');
        $.ajax({
            type:'GET',
            url:"{{ route('orders.validateInvoiceNumber') }}",
            data: {
                bank_account_number: $('#editInvoiceDialog [name=bank_account_number]').val(),
                id: $("#editCreditInvoice").attr('data-id')
            },
            beforeSend: function() {
                $("#editInvoiceDialog #bank_account_number").css("background", "#FFF url(/img/LoaderIcon.gif) no-repeat 98%");
            },
            success:function(data){
                if(data.error) {
                    $("#editInvoiceDialog #alert_bank_account_number").html(data.message);
                    $("#editInvoiceDialog #bank_account_number").addClass("is-invalid");
                    $("#editInvoiceDialog #bank_account_number").val(data.number || '');
                    $("#editInvoiceDialog .number-new").addClass('d-none');
                }else{
                    $("#editInvoiceDialog #alert_bank_account_number").html('');
                    $("#editInvoiceDialog #bank_account_number").removeClass("is-invalid");
                    $("#editInvoiceDialog #bank_account_number").addClass("is-valid");
                    $("#editInvoiceDialog .number-new").removeClass('d-none');
                }
            },
            complete: function(r){
                $("#editInvoiceDialog #bank_account_number").removeAttr("style");
                $.each( r.responseJSON.errors, function( key, value ) {
                    $("#editInvoiceDialog [name="+key+"]").addClass('is-invalid');
                    $("#editInvoiceDialog #alert_"+key+"").html(value);
                });
            }
        });
    });

    $(document).on('click', '#editInvoiceDialog .btn-primary', function(e){
        var number = '{{$invoiceBankAccountNo}}';
        var bank_account_number = $("#editInvoiceDialog [name=bank_account_number]").val();
        var default_val = $("#default_bank_account_number").val();
        if(number != bank_account_number && default_val != bank_account_number){
            alert("sadsad");
            e.preventDefault();
        }else{
            $("#editInvoiceDialog #bank_account_number").addClass("is-valid");
            $("#editOrderInvoice").submit();
        }
    });


    /* Edit order action */
    $(document).on('click', '#editAction', function () {
        var orderActionId = $(this).data('id');

        $.ajax({
            type:'POST',
            url:"{{ route('api.order-action-by-id') }}",
            data:{
                id: orderActionId,
            },
            success:function(data) {
                var actionDate = null;
                if (data.orderAction.action_date != null) {
                    actionDate = new Date(data.orderAction.action_date);
                    //convert day to 2 digits
                    var actionDateDigitDay = (actionDate.getDate() < 10) ? '0' + actionDate.getDate() : actionDate.getDate();
                    //convert month to 2 digits
                    var actionDateDigitMonth = (actionDate.getMonth() < 9) ? '0' + (actionDate.getMonth()+1) : (actionDate.getMonth()+1);
                    actionDate = actionDate.getFullYear() + '-' + actionDateDigitMonth + '-' + actionDateDigitDay;
                }

                var actionRemindDate = null;
                if (data.orderAction.action_remind_date != null) {
                    actionRemindDate = new Date(data.orderAction.action_remind_date);
                    //convert day to 2 digits
                    var actionRemindDateDigitDay = (actionRemindDate.getDate() < 10) ? '0' + actionRemindDate.getDate() : actionRemindDate.getDate();
                    //convert month to 2 digits
                    var actionRemindDateDigitMonth = (actionRemindDate.getMonth() < 9) ? '0' + (actionRemindDate.getMonth()+1) : (actionRemindDate.getMonth()+1);
                    actionRemindDate = actionRemindDate.getFullYear() + '-' + actionRemindDateDigitMonth + '-' + actionRemindDateDigitDay;
                }

                var actionReceivedDate = null;
                if (data.orderAction.action_received_date != null) {
                    actionReceivedDate = new Date(data.orderAction.action_received_date);
                    //convert day to 2 digits
                    var actionReceivedDateDigitDay = (actionReceivedDate.getDate() < 10) ? '0' + actionReceivedDate.getDate() : actionReceivedDate.getDate();
                    //convert month to 2 digits
                    var actionReceivedDateDigitMonth = (actionReceivedDate.getMonth() < 9) ? '0' + (actionReceivedDate.getMonth()+1) : (actionReceivedDate.getMonth()+1);
                    actionReceivedDate = actionReceivedDate.getFullYear() + '-' + actionReceivedDateDigitMonth + '-' + actionReceivedDateDigitDay;
                }

                $('#editSelectedOrderActions [name=order_action_id]').val(orderActionId);
                $('#editSelectedOrderActions [name=toBeDoneBy]').val(data.orderAction.toBeDoneBy);
                $('#editSelectedOrderActions [name=action_date]').val(actionDate);
                $('#editSelectedOrderActions [name=action_remind_date]').val(actionRemindDate);
                $('#editSelectedOrderActions [name=action_received_date]').val(actionReceivedDate);
                $('#editSelectedOrderActions [name=remark]').val(data.orderAction.remark);
                $('#editSelectedOrderActions').modal('show');
            }
        });
    });

    /* Update order action status */
    $(document).on('click', '#saveStatus', function () {
       var actionId = $(this).data('id');
       var actionStatus = $(this).attr('name');
       var objectType = 'order';
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

    $(document).on('click', '.reset-value', function (e) {
        var idOffer = $(this).attr('data-id');

        if(idOffer){
            var buttonValue = $(this).html();
            $(this).html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
            $.ajax({
                type:'POST',
                url:"{{ route('offers.resetValueCrateOffer') }}",
                data:{
                    idOffer: idOffer
                },
                success:function(data){
                    $.NotificationApp.send("Success message!", "Species Crate data updated successfully", 'top-right', '#fff', 'success');
                    location.reload();
                }
            });
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


    $(".surplus-select2").on("change", function(){
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
        var id_data = "{{ $order->id }}"
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
                    type: "order"
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

});
function deleteRemarks(){
    event.preventDefault();
    var btn_save = $('#remarkSaveDelete').html();
    $('#remarkSaveDelete').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    $.ajax({
        type:'POST',
        url:"{{ route('orders.updateRemark') }}",
        data:{
            id: '{{$order->id}}',
            remarks: ""
        },
        success:function(data) {
            if(typeof data.error != "undefined" && data.error === true){
                $.NotificationApp.send("Error message!", data.message, 'top-right', '#bf441d', 'error');
            }else{
                $('.remarkValue').html(data.remark);
                $('[name=order_remarks]').val(data.remark);
                $.NotificationApp.send("Success message!", data.message, 'top-right', '#5ba035', 'success');
            }
            $('#remarkSaveDelete').html(btn_save);
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
