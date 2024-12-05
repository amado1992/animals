@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        @if (Auth::user()->hasPermission('orders.create'))
            <a href="{{ route('orders.create') }}" class="btn btn-light">
                <i class="fas fa-fw fa-plus"></i> New
            </a>
        @endif
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterOrders">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('orders.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        @if (Auth::user()->hasPermission('orders.delete'))
            <button type="button" id="deleteSelectedItems" class="btn btn-light">
                <i class="fas fa-fw fa-window-close"></i> Delete
            </button>
        @endif
        @if (Auth::user()->hasPermission('orders.export-survey'))
            <a id="exportOrdersRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportOrders">
                <i class="fas fa-fw fa-save"></i> Export
            </a>
        @endif
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-file-alt mr-2"></i> {{ __('Orders') }}</h1>
    <p class="text-white">All orders where an animal is sold</p>

    <div class="d-flex flex-row items-center mb-3">
        <div class="d-flex align-items-center mr-2">
            <label class="text-white  mr-1">Status:</label>
            {!! Form::open(['id' => 'statusForm', 'route' => 'orders.ordersWithStatus', 'method' => 'GET']) !!}
                <select class="custom-select custom-select-sm w-auto" id="statusField" name="statusField">
                    @foreach ($orderStatuses as $statusKey => $statusValue)
                        <option value="{{ $statusKey }}" @if(isset($statusField) && $statusField == $statusKey) selected @endif>{{$statusValue}}</option>
                    @endforeach
                </select>
            {!! Form::close() !!}
        </div>
        <div class="d-flex align-items-center">
            <label class="text-white  mr-1">Order by:</label>
            {!! Form::open(['id' => 'ordersOrderByForm', 'route' => 'orders.orderBy', 'method' => 'GET']) !!}
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
    </div>
@endsection

@section('main-content')

<div class="card shadow mb-2">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex align-items-center mr-2">
                    <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
                    <input type="hidden" id="countOrdersVisible" value="{{ $orders->count() }}" />

                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('orders.removeFromOrderSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
        </div>
        @foreach($orders as $order)
            <div class="d-flex">
                <div class="justify-content-start" style="width: 3%">
                    <div class="mr-3">
                        <input type="checkbox" class="selector" value="{{ $order->id }}" /><br>
                        <a href="{{ route('orders.show', $order->id) }}" title="Edit"><i class="fas fa-search"></i></a><br>
                        @if (Auth::user()->hasPermission('orders.delete'))
                            {!! Form::open(['method' => 'DELETE', 'route' => ['orders.destroy', $order->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                <a href="#" onclick="$(this).closest('form').submit();"><i class="fas fa-window-close"></i></a>
                            {!! Form::close() !!}
                        @endif
                    </div>
                </div>

                <div class="w-100">
                    <div class="d-flex flex-wrap justify-content-start align-items-center">
                        <div style="flex-grow: 1" class="d-flex justify-content-start">
                            <div class="mr-2" style="width: 35%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Status</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <span>{{ $order->order_status }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 35%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Order No</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <span>{{ $order->full_number }}/{{ $order->offer->offer_number }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 100%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Quant. & Species</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        @if ($order->offer->offer_species->count() == 0)
                                            <span style="color: red;">No species added yet</span>
                                        @else
                                            @foreach ($order->offer->species_ordered as $species)
                                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}} {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})<br>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 100%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Client</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        {{ $order->client->full_name }}<br>
                                        ({{ $order->client->email }})
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 100%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Provider</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        @if ($order->supplier)
                                            {{ $order->supplier->full_name }}
                                            ({{ $order->supplier->email }})
                                        @else
                                            International Zoo Services
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mr-2" style="width: 80%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Tasks</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        @unless($order->order_today_tasks->isEmpty() && $order->order_other_tasks->isEmpty())
                                            @foreach( $order->order_today_tasks as $todayTask )
                                                <div>- {{ $todayTask->description }} ({{$todayTask->action}})</div>
                                            @endforeach
                                            @foreach( $order->order_other_tasks as $otherTask )
                                                <div>- {{ $otherTask->description }} ({{$otherTask->action}})</div>
                                            @endforeach
                                        @else
                                            <p>No tasks to to.</p>
                                        @endunless
                                    </div>
                                </div>
                            </div>

                            <div style="width: 80%;">
                                <div class="row">
                                    <div class="col">
                                        <b>Files</b>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        @foreach($order->all_docs as $doc)
                                            @php
                                                $file = pathinfo($doc);
                                            @endphp
                                            <a href="{{Storage::url('orders_docs/'.$order->full_number.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a><br>
                                        @endforeach
                                        @foreach($order->offer->all_docs as $doc)
                                            @php
                                                $file = pathinfo($doc);
                                            @endphp
                                            <a href="{{Storage::url('offers_docs/'.$order->offer->full_number.'/'.$file['basename'])}}" target="_blank">{{$file['basename']}}</a><br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <b>Manager: </b>{{ $order->manager->name }}
                </div>
            </div>
        @endforeach
        {{$orders->links()}}
    </div>
</div>

@include('orders.filter_modal', ['modalId' => 'filterOrders'])

@include('export_excel.export_options_modal', ['modalId' => 'exportOrders'])

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $(':checkbox:checked.selector').prop('checked', false);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#selectAll').on('change', function () {
        $(":checkbox.selector").prop('checked', this.checked);
    });

    $('#filterOrders').on('hidden.bs.modal', function () {
        $("#filterOrders .animal-select2").val(null).trigger('change');
        $("#filterOrders .contact-select2").val(null).trigger('change');
        $(this).find('form').trigger('reset');
    });

    //Select2 animal selection
    $('[name=filter_animal_id]').on('change', function () {
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
                    $('[name=filter_animal_id]').append(newOption);
                }
            });
        }
    });

    //Select2 contact selection
    $('[name=filter_client_id]').on('change', function () {
        var contactId = $(this).val();

        if(contactId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.contact-by-id') }}",
                data: {
                    id: contactId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.contact.email.trim(), data.contact.id, true, true);
                    // Append it to the select
                    $('[name=filter_client_id]').append(newOption);
                }
            });
        }
    });

    //Select2 contact selection
    $('[name=filter_supplier_id]').on('change', function () {
        var contactId = $(this).val();

        if(contactId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.contact-by-id') }}",
                data: {
                    id: contactId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.contact.email.trim(), data.contact.id, true, true);
                    // Append it to the select
                    $('[name=filter_supplier_id]').append(newOption);
                }
            });
        }
    });

    $('#statusField').on('change', function () {
        $('#statusForm').submit();
    });

    $('#orderByField').on('change', function () {
        $('#ordersOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#ordersOrderByForm').submit();
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
                url:"{{ route('orders.deleteItems') }}",
                data:{items: ids},
                success:function(data){
                    location.reload();
                }
            });
        }
    });

    $('#exportOrdersRecords').on('click', function () {
        var count_selected_records = $(":checked.selector").length;
        var count_page_records = $('#countOrdersVisible').val();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportOrders').modal('show');
    });

    $('#exportOrders').on('submit', function (event) {
        event.preventDefault();

        var export_option = $('#exportOrders [name=export_option]:checked').val();

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
            var url = "{{route('orders.export')}}?items=" + ids;
            window.location = url;

            $('#exportOrders').modal('hide');
        }
    });

});

</script>

@endsection
