@extends('layouts.admin')

@section('subnav-content')
<ol class="breadcrumb border-0 m-0 bg-primary">
    <li class="breadcrumb-item text-dark">Home</li>
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-dark">Orders</a></li>
    <li class="breadcrumb-item active text-dark">{{ $order->full_number }}/{{ $order->offer->offer_number }}</li>
</ol>
@endsection

@section('header-content')
<div class="d-flex mb-3">
    <h1 class="h3 text-white ">Order {{$order->full_number}}</h1>
</div>

<div class="w-100 mt-n2 mb-2">
    <div class="d-flex flex-wrap">
        <div style="width: 20%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <div class="d-flex">
                        @if (Auth::user()->hasPermission('orders.update'))
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i> Edit</a>
                        @endif
                        <div class="dropdown ml-2">
                            <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                @if (Auth::user()->hasPermission('orders.order-documents'))
                                    <h6 class="dropdown-header">Documents</h6>
                                    <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'reservation_supplier']) }}" title="Create reservation supplier document" id="reservation_supplier"><i class="fas fa-file-pdf"></i>&nbsp;Reservation for supplier</a>
                                    <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'reservation_client']) }}" title="Create reservation client document" id="reservation_client"><i class="fas fa-file-pdf"></i>&nbsp;Reservation for client</a>
                                    <a class="dropdown-item" href="{{ route('orders.create_order_documents_pdf', [$order->id, 'proforma_invoice']) }}" title="Create proforma invoice document" id="proforma_invoice"><i class="fas fa-file-pdf"></i>&nbsp;Proforma invoice</a>
                                    <a class="dropdown-item" href="#" id="packingList" title="Create packing list document" data-toggle="modal" data-target="#packingListDialog"><i class="fas fa-file-pdf"></i>&nbsp;Packing list</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('orders.sendEmailOption', [$order->id, 'reservation_supplier']) }}"><i class="fas fa-envelope"></i>&nbsp;Send reservation to supplier</a>
                                    <a class="dropdown-item" href="{{ route('orders.sendEmailOption', [$order->id, 'reservation_client']) }}"><i class="fas fa-envelope"></i>&nbsp;Send reservation to client</a>
                                    <a class="dropdown-item" href="{{ route('orders.sendEmailOption', [$order->id, 'checklist_client']) }}"><i class="fas fa-envelope"></i>&nbsp;Send Checklist document to client</a>
                                    <a class="dropdown-item" href="{{ route('orders.sendEmailOption', [$order->id, 'proforma_invoice']) }}"><i class="fas fa-envelope"></i>&nbsp;Send proforma invoice</a>
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
                </div>
                <div class="card-body p-2">
                    <b>Number: </b>{{ $order->full_number }}<br>
                    <b>Sale type: </b>{{ $order->sale_type }}<br>
                    <b>Status: </b>{{ $order->order_status }}<br>
                    <b>Cost currency: </b>{{ $order->cost_currency }}<br>
                    <b>Sale currency: </b>{{ $order->sale_currency }}<br>
                    <b>Destination: </b>{{ $order->delivery_country->name }}<br>
                </div>
            </div>
        </div>

        <div style="width: 20%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Client details</h5>
                </div>
                <div class="card-body p-2">
                    <b>Institution: </b>{{ ($order->client->organisation) ? $order->client->organisation->name : '' }}<br>
                    <b>Contact: </b>{{ $order->client->full_name }}<br>
                    <b>E-mail: </b>{{ $order->client->email }}<br>
                    <b>Phone: </b>{{ ($order->client->organisation) ? $order->client->organisation->phone : '' }}<br>
                    <b>Country: </b>{{ ($order->client->organisation && $order->client->organisation->country) ? $order->client->organisation->country->name : '' }}
                </div>
            </div>
        </div>

        <div style="width: 20%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Supplier details</h5>
                </div>
                <div class="card-body p-2">
                    @if ($order->supplier)
                        <b>Institution: </b>{{ ($order->supplier->organisation) ? $order->supplier->organisation->name : '' }}<br>
                        <b>Contact: </b>{{ $order->supplier->full_name }}<br>
                        <b>E-mail: </b>{{ $order->supplier->email }}<br>
                        <b>Phone: </b>{{ ($order->supplier->organisation) ? $order->supplier->organisation->phone : '' }}<br>
                        <b>Country: </b>{{ ($order->supplier->organisation && $order->supplier->organisation->country) ? $order->supplier->organisation->country->name : '' }}
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

        <div style="width: 20%;">
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
                                </div>
                            @endforeach
                            @foreach( $order->order_other_tasks as $otherTask )
                                <div>
                                    <a href="{{ route('orders.deleteOrderTask', $otherTask->id) }}" onclick="return confirm('Are you sure you want to delete this task?')"><i class="fas fa-window-close"></i></a>
                                    <a href="#" id="editTask" data-toggle="modal" data-id="{{ $otherTask->id }}" title="Edit task">{{ $otherTask->description }} ({{$otherTask->action}})</a>
                                </div>
                            @endforeach
                        @else
                            <p>No tasks to to.</p>
                        @endunless
                    </div>
                </div>
            </div>
        </div>

        <div style="width: 20%;">
            <div class="card border-left-success shadow h-100">
                <div class="card-header pb-1">
                    <h5>Remarks</h5>
                </div>
                <div class="card-body p-2">
                    {{$order->order_remarks}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('main-content')

    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="animals-tab" data-toggle="tab" href="#animalsTab" role="tab" aria-controls="animalsTab" aria-selected="true"><i class="fas fa-fw fa-paw"></i> Animals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoicesTab" role="tab" aria-controls="invoicesTab" aria-selected="false"><i class="fas fa-fw fa-wallet"></i> Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="files-tab" data-toggle="tab" href="#filesTab" role="tab" aria-controls="filesTab" aria-selected="false"><i class="fas fa-fw fa-file"></i> Files</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="animalsTab" role="tabpanel" aria-labelledby="animals-tab">
                    @if (Auth::user()->hasPermission('offers.update'))
                        <div class="row align-items-center mb-2">
                            <div class="col-md-6">
                                <label class="mr-3"><input type="checkbox" id="selectAllSpecies" name="selectAllSpecies" class="ml-2"> Select all</label>
                                <a href="#" class="mr-2 btn btn-primary btn-sm" title="Add species to offer" data-toggle="modal" data-target="#addSpecies"><i class="fas fa-plus"></i>&nbsp;Add species</a>
                                <a href="#" class="btn btn-danger btn-sm" id="deleteSelectedSpecies" title="Remove species from offer"><i class="fas fa-window-close"></i>&nbsp;Remove species</a>
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

                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="table-responsive" id="offerSpeciesTable">
                                @include('offers.offer_species_table')
                            </div>
                        </div>
                    </div>

                    @if ($order->sale_price_type != "ExZoo")
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="table-responsive" id="offerSpeciesCratesTable">
                                    @include('offers.offer_species_crates_table')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($order->sale_price_type != "ExZoo" && $offer->airfreight_type == "volKgRates")
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="table-responsive" id="offerSpeciesAirfreightsTable">
                                    @include('offers.offer_species_airfreights_table')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($order->sale_price_type != "ExZoo")
                        @if ($order->offer->airfreight_type == "byTruck")
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="accordion" id="accordionTruck">
                                        <div class="card mb-1">
                                            <a href="#" class="pl-2" data-toggle="collapse" data-target="#collapseTruck" aria-expanded="true" aria-controls="collapseTruck">
                                                <h5>Transport by truck</h5>
                                            </a>
                                            <div id="collapseTruck" class="collapse" aria-labelledby="headingTruck" data-parent="#accordionTruck">
                                                <div class="card-body">
                                                    @if (Auth::user()->hasPermission('offers.update'))
                                                        {!! Form::open(['id' => 'selectOfferTransportTruck', 'class' => 'mt-n3']) !!}
                                                        <div class="row">
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
                                                                    <button class="btn btn-sm btn-dark" type="submit"><i class="fas fa-arrows-alt-h"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {!! Form::close() !!}
                                                    @endif
                                                    <div class="table-responsive" id="offerTransportTruckDiv">
                                                        @include('offers.offer_transport_truck_table')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($order->offer->airfreight_type == "pallets")
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="accordion" id="accordionPallet">
                                        <div class="card mb-1">
                                            <a href="#" class="pl-2" data-toggle="collapse" data-target="#collapsePallet" aria-expanded="true" aria-controls="collapsePallet">
                                                <h5>Airfreight by pallets</h5>
                                            </a>
                                            <div id="collapsePallet" class="collapse" aria-labelledby="headingPallet" data-parent="#accordionPallet">
                                                <div class="card-body">
                                                    @if (Auth::user()->hasPermission('offers.update'))
                                                        <div class="mt-n3 mb-2">
                                                            @if (Auth::user()->hasPermission('airfreights.create') && $order->offer->airfreight_pallet == null)
                                                                <a href="{{ route('airfreights.create', [$species->id, 'order_pallet']) }}" class="mr-3" title="Add new airfreight"><i class="fas fa-plus"></i>&nbsp;Add airfreight</a>
                                                            @endif
                                                            <a href="#" title="Select airfreight pallet" id="selectAirfreightPallet" data-toggle="modal" data-id="{{ $order->offer->id }}" isPallet="1"><i class="fas fa-plane"></i>&nbsp;Select airfreight pallet</a>
                                                        </div>
                                                    @endif
                                                    <div class="table-responsive" id="offerAirfreightPalletsDiv">
                                                        @include('offers.offer_airfreight_pallet_table')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="accordion" id="accordionTestsCosts">
                                    <div class="card mb-1">
                                        <a href="#" class="pl-2" data-toggle="collapse" data-target="#collapseTestsCosts" aria-expanded="true" aria-controls="collapseTestsCosts">
                                            <h5>Tests & Quarantine</h5>
                                        </a>
                                        <div id="collapseTestsCosts" class="collapse" aria-labelledby="headingTestsCosts" data-parent="#accordionTestsCosts">
                                            <div class="card-body">
                                                @if (Auth::user()->hasPermission('offers.update'))
                                                    {!! Form::open(['id' => 'addTestAdditionalCost', 'class' => 'form-inline mt-n3 mb-2']) !!}
                                                    <div class="form-group">
                                                        {!! Form::label('testAdditionalCostName', 'Name:', ['class' => 'font-weight-bold mr-3']) !!}
                                                        {!! Form::text('testAdditionalCostName', null, ['class' => 'form-control form-control-sm']) !!}
                                                        {!! Form::hidden('offer_id', $order->offer->id, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="form-group ml-3">
                                                        <button class="btn btn-sm btn-dark" type="submit"><i class="fas fa-save"></i></button>
                                                    </div>
                                                    {!! Form::close() !!}
                                                @endif
                                                <div class="table-responsive" id="additionalTestsDiv" offerId="{{ $order->offer->id }}" isTest="1">
                                                    @include('offers.additional_tests_table')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="accordion" id="accordionBasicCosts">
                                    <div class="card mb-1">
                                        <a href="#" class="pl-2" data-toggle="collapse" data-target="#collapseBasicCosts" aria-expanded="true" aria-controls="collapseBasicCosts">
                                            <h5>Basis costs</h5>
                                        </a>
                                        <div id="collapseBasicCosts" class="collapse" aria-labelledby="headingBasicCosts" data-parent="#accordionBasicCosts">
                                            <div class="card-body">
                                                @if (Auth::user()->hasPermission('offers.update'))
                                                    {!! Form::open(['id' => 'addBasicAdditionalCost', 'class' => 'form-inline mt-n3 mb-2']) !!}
                                                    <div class="form-group">
                                                        {!! Form::label('basicAdditionalCostName', 'Name:', ['class' => 'font-weight-bold mr-3']) !!}
                                                        {!! Form::text('basicAdditionalCostName', null, ['class' => 'form-control form-control-sm']) !!}
                                                        {!! Form::hidden('offer_id', $order->offer->id, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="form-group ml-3">
                                                        <button class="btn btn-sm btn-dark" type="submit"><i class="fas fa-save"></i></button>
                                                    </div>
                                                    {!! Form::close() !!}
                                                @endif
                                                <div class="table-responsive" id="additionalCostsDiv" offerId="{{ $order->offer->id }}" isTest="0">
                                                    @include('offers.additional_costs_table')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive" id="totalAndProfitSection">
                                @include('offers.total_and_profit_table')
                            </div>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->hasPermission('orders.order-invoices'))
                    <div class="tab-pane fade show" id="invoicesTab" role="tabpanel" aria-labelledby="invoices-tab">
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'airfreight']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'crates']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'cites_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'veterinary_docs']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'documents']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'suppliers_offers']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
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
                                        <a href="{{ route('offers.delete_file', [$order->offer->id, $file['basename'], 'others']) }}" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-window-close ml-1"></i></a>
                                    @endif
                                    <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('offers.add_species_modal', ['modalId' => 'addSpecies', 'offerId' => $order->offer->id])

@include('tasks.task_form_modal', ['modalId' => 'orderTaskForm', 'route' => 'orders.orderTask'])

@include('offers.species_airfreight_selection_modal', ['modalId' => 'selectSpeciesAirfreight'])
@include('offers.offer_pallet_selection_modal', ['modalId' => 'selectOfferAirfreightPallet'])

@include('orders.invoice_preview_modal', ['modalId' => 'invoicePreview'])
@include('orders.document_preview_modal', ['modalId' => 'documentPreview'])

@include('orders.edit_invoice_modal', ['modalId' => 'editInvoiceDialog'])
@include('orders.set_invoice_payment_modal', ['modalId' => 'setInvoicePaymentDialog'])

@include('orders.packing_list_modal', ['modalId' => 'packingListDialog'])

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

    $(':checkbox:checked.selectorSpecies').prop('checked', false);

    $('#selectAllSpecies').on('change', function () {
        $(":checkbox.selectorSpecies").prop('checked', this.checked);
    });

    $('#addSpecies').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        /*$(this).find('form')[0].reset();*/
    });

    $('#tempSelectedSpecies').on('click', function () {
        var m = $('#addSpecies [name=quantityM]').val();
        var f = $('#addSpecies [name=quantityF]').val();
        var u = $('#addSpecies [name=quantityU]').val();

        var ourSurplusId = $('#addSpecies [name=select_surplus]').val();
        var select2Data = $('#addSpecies [name=select_surplus]').select2('data');

        if(!$.isNumeric(m) || !$.isNumeric(f) || !$.isNumeric(u))
            alert("Quantities must be a number.");
        else if ((m + f + u) == 0)
            alert("You must insert a quantity.");
        else if (ourSurplusId == 0)
            alert("You must select a surplus species.");
        else {
            $("#selectedSpecies").append('<tr class="item" males="' + m + '" females="' + f + '" unsexed="' + u + '" surplusId="' + ourSurplusId + '">' +
                    '<td><a href="#" class="remove-tr"><i class="fas fa-window-close"></i></a></td>' +
                    '<td style="text-align: center;">' + m + '</td>' +
                    '<td style="text-align: center;">' + f + '</td>' +
                    '<td style="text-align: center;">' + u + '</td>' +
                    '<td>' + select2Data[0].animal.common_name + ' (' + select2Data[0].animal.scientific_name + ')</td>' +
                    '<td></td>' +
                '</tr>');

            $('#addSpecies [name=quantityM]').val(0);
            $('#addSpecies [name=quantityF]').val(0);
            $('#addSpecies [name=quantityU]').val(0);
            $('#addSpecies [name=select_surplus]').val(null).trigger('change');
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
            selectedSurplus.push([row.attr('surplusId'), row.attr('males'), row.attr('females'), row.attr('unsexed')]);
        });

        $.ajax({
            type:'POST',
            url:"{{ route('offers.addOfferSpecies') }}",
            data:{
                items: selectedSurplus,
                offerId: $('#addSpecies [name=offer_id]').val()
            },
            success:function(data){
                location.reload();
                /*alert(data.msg);*/
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
            $.ajax({
                type:'POST',
                url:"{{ route('offers.deleteSpecies') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
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
                success:function(data){
                    $('#'+divId).html(data.html);
                    $('#offerSpeciesAirfreightsTable').html(data.speciesAirfreightsHtml);
                    $('#totalAndProfitSection').html(data.totalAndProfitHtml);
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

        var value = sender.val();

        $.ajax({
            type:'POST',
            url:"{{ route('offers.saveSpeciesCrateValues') }}",
            data:{
                idOfferSpeciesCrate: idOfferSpeciesCrate,
                column: column,
                value: value
            },
            success:function(data){
                $('#'+divId).html(data.html);
                $('#offerSpeciesAirfreightsTable').html(data.speciesAirfreightsHtml);
                $('#offerAirfreightPalletsDiv').html(data.offerSpeciesPallets);
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
            }
        });
    }

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
                        '<td style="text-align: center;">' + item.fromCountry + '</td>' +
                        '<td style="text-align: center;">' + item.toCountry + '</td>' +
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

    $('#selectSpeciesAirfreight [name=from_country]').on('change', function () {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('countries.getAirportsByCountryId') }}",
            data:{
                value: value,
            },
            success:function(data){
                $('#selectSpeciesAirfreight [name=from_airport]').empty();
                $('#selectSpeciesAirfreight [name=from_airport]').append('<option value="">- select -</option>');
                $.each(data.airports, function(key, value) {
                    $('#selectSpeciesAirfreight [name=from_airport]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#selectOfferAirfreightPallet [name=from_country]').on('change', function () {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('countries.getAirportsByCountryId') }}",
            data:{
                value: value,
            },
            success:function(data){
                $('#selectOfferAirfreightPallet [name=from_airport]').empty();
                $('#selectOfferAirfreightPallet [name=from_airport]').append('<option value="">- select -</option>');
                $.each(data.airports, function(key, value) {
                    $('#selectOfferAirfreightPallet [name=from_airport]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#selectSpeciesAirfreight [name=to_country]').on('change', function () {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('countries.getAirportsByCountryId') }}",
            data:{
                value: value,
            },
            success:function(data){
                $('#selectSpeciesAirfreight [name=to_airport]').empty();
                $('#selectSpeciesAirfreight [name=to_airport]').append('<option value="">- select -</option>');
                $.each(data.airports, function(key, value) {
                    $('#selectSpeciesAirfreight [name=to_airport]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#selectOfferAirfreightPallet [name=to_country]').on('change', function () {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('countries.getAirportsByCountryId') }}",
            data:{
                value: value,
            },
            success:function(data){
                $('#selectOfferAirfreightPallet [name=to_airport]').empty();
                $('#selectOfferAirfreightPallet [name=to_airport]').append('<option value="">- select -</option>');
                $.each(data.airports, function(key, value) {
                    $('#selectOfferAirfreightPallet [name=to_airport]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#selectSpeciesAirfreight #getAirfreights').on('click', function () {
        var from_country = $('#selectSpeciesAirfreight [name=from_country]').val();
        var from_airport = $('#selectSpeciesAirfreight [name=from_airport]').val();
        var to_country = $('#selectSpeciesAirfreight [name=to_country]').val();
        var to_airport = $('#selectSpeciesAirfreight [name=to_airport]').val();

        $.ajax({
            type:'POST',
            url:"{{ route('airfreights.getAirfreightsByCountriesAndAirports') }}",
            data:{
                isPallet: 0,
                from_country: from_country,
                from_airport: from_airport,
                to_country: to_country,
                to_airport: to_airport
            },
            success:function(data){
                $('#selectSpeciesAirfreight [name=freights]').empty();
                $('#selectSpeciesAirfreight [name=freights]').append('<option value="">- select -</option>');
                $.each(data.freights, function(key, value) {
                    $('#selectSpeciesAirfreight [name=freights]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#selectOfferAirfreightPallet #getAirfreights').on('click', function () {
        var from_country = $('#selectOfferAirfreightPallet [name=from_country]').val();
        var from_airport = $('#selectOfferAirfreightPallet [name=from_airport]').val();
        var to_country = $('#selectOfferAirfreightPallet [name=to_country]').val();
        var to_airport = $('#selectOfferAirfreightPallet [name=to_airport]').val();

        $.ajax({
            type:'POST',
            url:"{{ route('airfreights.getAirfreightsByCountriesAndAirports') }}",
            data:{
                isPallet: 1,
                from_country: from_country,
                from_airport: from_airport,
                to_country: to_country,
                to_airport: to_airport
            },
            success:function(data){
                $('#selectOfferAirfreightPallet [name=freights]').empty();
                $('#selectOfferAirfreightPallet [name=freights]').append('<option value="">- select -</option>');
                $.each(data.freights, function(key, value) {
                    $('#selectOfferAirfreightPallet [name=freights]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#selectSpeciesAirfreight #tempAddSelectedAirfreight').on('click', function () {
        var selectedFreightId = $('#selectSpeciesAirfreight [name=freights]').val();

        if (selectedFreightId == null)
            alert("You must select an airfreight.");
        else {
            $.ajax({
                type:'POST',
                url:"{{ route('airfreights.getAirfreightById') }}",
                data:{
                    selectedFreightId: selectedFreightId
                },
                success:function(data){
                    $('#selectSpeciesAirfreight #selectedAirfreights').append('<tr class="item" airfreightId="' + data.airfreight.id  + '">' +
                        '<td><a href="#" class="remove-airfreight-tr" offerSpeciesAirfreightId="' + data.airfreight.offerSpeciesAirfreightId + '"><i class="fas fa-window-close"></i></a></td>' +
                        '<td style="text-align: center;">' + data.airfreight.fromCountry + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.toCountry + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.type + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.currency + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.volKg_weight_cost + '</td>' +
                        '<td style="text-align: center;">' + data.airfreight.remarks + '</td>' +
                    '</tr>');
                }
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
                }
            });
        }
        else
            alert("You have not selected any airfreight.");
    });

    $('#selectOfferAirfreightPallet').on('submit', function (event) {
        event.preventDefault();

        var offerId = $('#selectOfferAirfreightPallet [name=offer_id]').val();
        var from_country = $('#selectOfferAirfreightPallet [name=from_country]').val();
        var from_airport = $('#selectOfferAirfreightPallet [name=from_airport]').val();
        var to_country = $('#selectOfferAirfreightPallet [name=to_country]').val();
        var to_airport = $('#selectOfferAirfreightPallet [name=to_airport]').val();
        var selectedPallet = $('#selectOfferAirfreightPallet [name=freights]').val();

        if(from_country != "" && from_airport != "" && to_country != "" && to_airport != "" && selectedPallet != "") {
            $.ajax({
                type:'POST',
                url:"{{ route('offers.saveOfferAirfreightPallet') }}",
                data:{
                    offerId: offerId,
                    from_country: from_country,
                    to_country: to_country,
                    selectedPallet: selectedPallet
                },
                success:function(data){
                    if(data.success) {
                        $('#offerAirfreightPalletsDiv').html(data.html);
                        $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    }

                    $('#selectOfferAirfreightPallet').modal('hide');
                }
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
            success:function(data){
                $('#'+divId).html(data.html);
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
            }
        });
    }

    $('#selectOfferTransportTruck').on('submit', function (event) {
        event.preventDefault();

        var offerId = $('#selectOfferTransportTruck [name=offer_id]').val();
        var from_country = $('#selectOfferTransportTruck [name=truck_from_country]').val();
        var to_country = $('#selectOfferTransportTruck [name=truck_to_country]').val();

        if(from_country != "" && to_country != "") {
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
                        $('#offerTransportTruckDiv').html(data.html);
                        $('#totalAndProfitSection').html(data.totalAndProfitHtml);
                    }
                }
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
            success:function(data){
                $('#'+divId).html(data.html);
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
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
            success:function(data){
                $('#'+divId).html(data.html);
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
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
        var divId = container.parents('div').attr('id');
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
            success:function(data){
                $('#'+divId).html(data.html);
                $('#totalAndProfitSection').html(data.totalAndProfitHtml);
            }
        });
    }

    $('#addTestAdditionalCost').on('submit', function (event) {
        event.preventDefault();

        var offerId = $('#addTestAdditionalCost [name=offer_id]').val();
        var name = $('#addTestAdditionalCost [name=testAdditionalCostName]').val();

        if(name != "") {
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
                        $('#addTestAdditionalCost [name=testAdditionalCostName]').val('');
                        location.reload();
                    }
                }
            });
        }
        else
            alert("You need to write a name.");
    });

    $('#addBasicAdditionalCost').on('submit', function (event) {
        event.preventDefault();

        var offerId = $('#addBasicAdditionalCost [name=offer_id]').val();
        var name = $('#addBasicAdditionalCost [name=basicAdditionalCostName]').val();

        if(name != "") {
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
                        $('#addBasicAdditionalCost [name=basicAdditionalCostName]').val('');
                        location.reload();
                    }
                }
            });
        }
        else
            alert("You need to write a name.");
    });

    $(document).on('change', '#createInvoice [name=invoice_percent]', invoiceAmountBasedOnPercent);

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
        else if (amountValue == 0 || percentValue == 0)
            alert("You cannot generate an invoice with amount or percent in zero.");
        else {
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

    //Select2 invoice contact selection
    $('[name=invoice_contact_id]').on('change', function () {
        var invoiceContactId = $(this).val();

        if(invoiceContactId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.contact-by-id') }}",
                data: {
                    id: invoiceContactId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.contact.email.trim(), data.contact.id, true, true);
                    // Append it to the select
                    $('[name=invoice_contact_id]').append(newOption);
                }
            });
        }
    });

});

</script>

@endsection
