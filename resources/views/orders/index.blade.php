@extends('layouts.admin')

@section('header-content')

    <div class="row mb-2">
        <div class="col-md-3">
            <h1 class="h1 text-white"><i class="fas fa-fw fa-file-alt mr-2"></i> {{ __('Orders') }}</h1>
            <p class="text-white">All orders where an animal is sold</p>
        </div>
        <div class="col-md-9 text-right">
            @if (Auth::user()->hasPermission('orders.create'))
                <a href="{{ route('orders.create') }}" class="btn btn-light">
                    <i class="fas fa-fw fa-plus"></i> New
                </a>
            @endif
            @if (Auth::user()->hasPermission('orders.update'))
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#editSelectedRecords">
                        <i class="fas fa-fw fa-edit"></i> Edit selection
                </button>
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
    </div>
    <div class="d-flex flex-row justify-content-between items-center text-white mb-2">
        <div class="d-flex align-items-center">
            <label class="text-white  mr-1">Status:</label>
            {!! Form::open(['id' => 'statusForm', 'route' => 'orders.ordersWithStatus', 'method' => 'GET']) !!}
            <select class="custom-select custom-select-sm w-auto" id="statusField" name="statusField">
                @foreach ($orderStatuses as $statusKey => $statusValue)
                    <option value="{{ $statusKey }}"
                            @if(isset($statusField) && $statusField == $statusKey) selected @endif>{{$statusValue}}</option>
                @endforeach
            </select>
            {!! Form::close() !!}
            <label class="text-white  mr-1">Order by:</label>
            {!! Form::open(['id' => 'ordersOrderByForm', 'route' => 'orders.filterOrders', 'method' => 'GET']) !!}
            <select class="custom-select custom-select-sm w-auto" id="orderByField" name="orderByField">
                @foreach ($orderByOptions as $orderByKey => $orderByValue)
                    <option value="{{ $orderByKey }}"
                            @if(isset($orderByField) && $orderByField == $orderByKey) selected @endif>{{$orderByValue}}</option>
                @endforeach
            </select>
            <select id="orderByDirection" name="orderByDirection" class="custom-select custom-select-sm w-auto">
                <option @if(!isset($orderByDirection)) selected @endif value="desc">Descending</option>
                <option @if(isset($orderByDirection) && $orderByDirection == 'asc') selected @endif value="asc">
                    Ascending
                </option>
            </select>
            {!! Form::close() !!}
        </div>

        <div class="d-flex align-items-center">
            Page: {{$orders->currentPage()}} | Records:&nbsp;
            @if (Auth::user()->hasPermission('orders.see-all-orders'))
                {!! Form::open(['id' => 'recordsPerPageForm', 'route' => 'orders.recordsPerPage', 'method' => 'GET']) !!}
                    {!! Form::text('recordsPerPage', $orders->count(), ['id' => 'recordsPerPage', 'class' => 'form-control form-control-sm text-center', 'style' => 'width: 50px']) !!}
                {!! Form::close() !!}
            @else
                {{$orders->count()}}
            @endif
            &nbsp;| Total: {{$orders->total()}}
        </div>
    </div>

    @if (Auth::user()->hasPermission('orders.see-all-orders'))
        <div class="float-right ml-2">
            {{$orders->links()}}
        </div>
    @endif
@endsection

@section('main-content')
    <div class="card shadow mb-2">
        <div class="card-body">
            <div class="d-flex flex-row items-center">
                <div class="d-flex align-items-center">
                    <input type="checkbox" id="selectAll" name="selectAll" />&nbsp;Select all
                    <input type="hidden" id="countContactsVisible" value="{{ ($orders->count() > 0) ? $orders->count() : 0 }}" />
                </div>

                <div class="d-flex align-items-center">
                    <span class="ml-3 mr-1">Filtered on:</span>
                    @foreach ($filterData as $key => $value)
                        <a href="{{ route('orders.removeFromOrderSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-2">
        <div class="card-body" style='padding: 10px'>
            <div class="table-responsive mb-2">
                <table class="table table-striped table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="border-top-0" colspan="6"></th>
                            @if ($orderByField === "invoice_number")
                                <th class="border-top-0 text-center"></th>
                            @endif
                            <th class="border-top-0 text-center border-left-total" colspan="3">Costs</th>
                            <th class="border-top-0 text-center border-left-total" colspan="3">Sales</th>
                            <th class="border-top-0 text-center border-left-total" colspan="3">Profit</th>
                        </tr>
                        <tr>
                            <th class="border-top-0" style="width: 3%;"></th>
                            <th class="border-top-0" style="width: 5%;">Order No</th>
                            @if ($orderByField === "invoice_number")
                                <th class="border-top-0" style="width: 5%;">Invoice No</th>
                            @endif
                            <th class="border-top-0" style="width: 24%;">Quant. & Species</th>
                            <th class="border-top-0" style="width: 5%;">Client</th>
                            <th class="border-top-0" style="width: 10%;">Supplier</th>
                            <th class="border-top-0" style="width: 10%;">Manager</th>
                            <th class="border-top-0 border-left-total" style="width: 7%;">Orig currency</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">USD Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 7%;">Orig currency</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">USD Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 7%;">Orig currency</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">Amount</th>
                            <th class="border-top-0 border-left-total" style="width: 5%;">USD Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="pr-0">
                                <input type="checkbox" class="selector" value="{{ $order->id }}"/>
                                <a href="{{ route('orders.show', $order->id) }}" title="Edit"><i class="fas fa-search"></i></a><br>
                                @if (Auth::user()->hasPermission('orders.delete'))
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['orders.destroy', $order->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                    <a href="#" onclick="$(this).closest('form').submit();"><i class="fas fa-window-close"></i></a>
                                    {!! Form::close() !!}
                                @endif
                            </td>
                            <td>
                                {{ $order->full_number }}/{{ $order->offer->offer_number }}
                            </td>
                            @if ($orderByField === "invoice_number")
                                <td class="border-top-0" style="width: 5%;">{{ date('Y', strtotime($order->invoice_date)) . "-" . str_pad((string)$order->bank_account_number, 3, '0', STR_PAD_LEFT) }}</td>
                            @endif
                            {{--see more modal in offer species--}}
                            <td>
                                @if ($order->offer->offer_species->count() == 0)
                                    <span style="color: red;">No species added yet</span>
                                @elseif ($order->offer->offer_species->count() == 1)
                                    <p>
                                        @foreach ($order->offer->species_ordered as $species)
                                            {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                            @if ($species->oursurplus && $species->oursurplus->animal)
                                                {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                            @else
                                                ERROR - NO STANDARD SURPLUS
                                            @endif
                                        @endforeach
                                    </p>
                                @elseif($order->offer->offer_species->count() > 1)
                                    @php
                                        $species = $order->offer->offer_species[0];
                                    @endphp
                                    <p>
                                        {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                        @if ($species->oursurplus && $species->oursurplus->animal)
                                            {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                        @else
                                            ERROR - NO STANDARD SURPLUS
                                        @endif
                                    </p>
                                    <p class="modal-toggle see-more">See More</p>
                                    <div style="display: none" class="hidden-info">
                                        @foreach ($order->offer->species_ordered as $species)
                                            <p>
                                                {{$species->offerQuantityM}}-{{$species->offerQuantityF}}-{{$species->offerQuantityU}}
                                                @if ($species->oursurplus && $species->oursurplus->animal)
                                                    {{$species->oursurplus->animal->common_name}} ({{$species->oursurplus->animal->scientific_name}})
                                                @else
                                                    ERROR - NO STANDARD SURPLUS
                                                @endif
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($order->client)
                                    {{ ($order->client->organisation && $order->client->organisation->name) ? $order->client->organisation->name : $order->client->full_name }}<br>
                                    <a href="mailto:{{ $order->client->email }}"><u>{{ $order->client->email }}</u></a><br>
                                    {{ $order->client->country->name }}
                                @else
                                    no information
                                @endif
                            </td>
                            <td>
                                @if ($order->supplier)
                                    {{ ($order->supplier->organisation && $order->supplier->organisation->name) ? $order->supplier->organisation->name : $order->supplier->full_name }}<br>
                                    <a href="mailto:{{ $order->supplier->email }}"><u>{{ $order->supplier->email }}</u></a><br>
                                    {{ $order->supplier->country->name }}
                                @else
                                    International Zoo Services
                                @endif
                            </td>
                            <td class="border-left-total">
                                @if(!empty($order->manager))
                                    {{ $order->manager->name }} {{ $order->manager->last_name }}
                                @endif
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency === "USD" ? number_format(($order->offer->offerTotalCostPriceUSD), 2, '.', '') : number_format(($order->offer->offerTotalCostPrice), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ number_format(($order->offer->offerTotalCostPriceUSD), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency === "USD" ? number_format(($order->offer->offerTotalSalePriceUSD), 2, '.', '') : number_format(($order->offer->offerTotalSalePrice), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ number_format(($order->offer->offerTotalSalePriceUSD), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency }}
                            </td>
                            <td class="border-left-total">
                                {{ $order->offer->offer_currency === "USD" ? number_format(($order->offer->offerTotalSalePriceUSD - $order->offer->offerTotalCostPriceUSD), 2, '.', '') : number_format(($order->offer->offerTotalSalePrice - $order->offer->offerTotalCostPrice), 2, '.', '') }}
                            </td>
                            <td class="border-left-total">
                                {{ number_format(($order->offer->offerTotalSalePriceUSD - $order->offer->offerTotalCostPriceUSD), 2, '.', '') }}
                            </td>
                        <tr>
                            <td colspan="9"></td>
                        </tr>
                    @endforeach
                    <tr class="total-complete">
                        <td colspan="5" style="padding: 2px 0 0 20px !important;">Total:</td>
                        @if ($orderByField === "invoice_number")
                            <td></td>
                        @endif
                        <td class="text-center border-left-total-complete" colspan="3">€ {{ number_format(($totalCostOrder), 2, '.', '') }} / $ {{ number_format(($totalCostUsdOrder), 2, '.', '') }}</td>
                        <td class="text-center border-left-total-complete" colspan="3">€ {{ number_format(($totalSaleOrder), 2, '.', '') }} / $ {{ number_format(($totalSaleUsdOrder), 2, '.', '') }}</td>
                        <td class="text-center border-left-total-complete" colspan="3">€ {{ number_format(($totalProfitOrder), 2, '.', '') }} / $ {{ number_format(($totalProfitUsdOrder), 2, '.', '') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
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
            {{$orders->links()}}
        </div>
    </div>

    @include('orders.filter_modal', ['modalId' => 'filterOrders'])

    @include('orders.edit_selection_modal', ['modalId' => 'editSelectedRecords'])

    @include('export_excel.export_options_modal', ['modalId' => 'exportOrders'])

@endsection

@section('page-scripts')

    <script type="text/javascript">

        $(document).ready(function () {

            // see more modal
            $('.modal-toggle').on('click', function(e) {

                e.preventDefault();
                let text = $(this).parent().find('.hidden-info').html()
                $('.modal-content-p').html(text)
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

            $('#filterOrders').on('hidden.bs.modal', function () {
                $("#filterOrders .animal-select2").val(null).trigger('change');
                $("#filterOrders .contact-select2").val(null).trigger('change');
                $(this).find('form').trigger('reset');
            });

            //Select2 animal selection
            $('[name=filter_animal_id]').on('change', function () {
                var animalId = $(this).val();

                if (animalId != null) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('api.animal-by-id') }}",
                        data: {
                            id: animalId,
                        },
                        success: function (data) {
                            // create the option and append to Select2
                            var newOption = new Option(data.animal.common_name.trim(), data.animal.id, true, true);
                            // Append it to the select
                            $('[name=filter_animal_id]').append(newOption);
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
                $(":checked.selector").each(function () {
                    ids.push($(this).val());
                });

                if (ids.length == 0)
                    alert("You must select items to delete.");
                else if (confirm("Are you sure that you want to delete the selected projects?")) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('orders.deleteItems') }}",
                        data: {items: ids},
                        success: function (data) {
                            location.reload();
                        }
                    });
                }
            });

            $('#exportOrdersRecords').on('click', function () {
                var count_selected_records = $(":checked.selector").length;
                var count_page_records = $('#countOrdersVisible').val();
                $("label[for='count_selected_records']").html('(' + count_selected_records + ')');
                $("label[for='count_page_records']").html('(' + count_page_records + ')');

                $('#exportOrders').modal('show');
            });

            $('#exportOrders').on('submit', function (event) {
                event.preventDefault();

                var export_option = $('#exportOrders [name=export_option]:checked').val();

                var ids = [];
                if (export_option == "selection") {
                    $(":checked.selector").each(function () {
                        ids.push($(this).val());
                    });
                } else {
                    $(".selector").each(function () {
                        ids.push($(this).val());
                    });
                }

                if (ids.length == 0)
                    alert("There are not records to export.");
                else {
                    var url = "{{route('orders.export')}}?items=" + ids;
                    window.location = url;

                    $('#exportOrders').modal('hide');
                }
            });

            $('input[name=set_number_year]:checkbox').change(function () {
                if($(this).is(':checked'))
                    $('#setNumberAndCreationDate').removeClass("d-none");
                else
                    $('#setNumberAndCreationDate').addClass("d-none");
            });

            $('#sendEditSelectionForm').on('click', function(event) {
                event.preventDefault();

                var ids = [];
                $(":checked.selector").each(function(){
                    ids.push($(this).val());
                });

                if(ids.length == 0)
                    alert("You must select orders to edit.");
                else {
                    var modal_footer = $('#editSelectedRecords .modal-footer').html();
                    $('#editSelectedRecords .modal-footer').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

                    $.ajax({
                        type:'POST',
                        url:"{{ route('orders.editSelectedRecords') }}",
                        data:{
                            items: ids,
                            order_number: $('#editSelectedRecords [name=order_number]').val(),
                            created_at: $('#editSelectedRecords [name=created_at]').val(),
                            order_status: $('#editSelectedRecords [name=order_status]').val(),
                            manager_id: $('#editSelectedRecords [name=manager_id]').val(),
                            delivery_country_id: $('#editSelectedRecords [name=delivery_country_id]').val(),
                            delivery_airport_id: $('#editSelectedRecords [name=delivery_airport_id]').val(),
                            hidden_delivery_airport_id: $('#editSelectedRecords [name=hidden_delivery_airport_id]').val(),
                            cost_currency: $('#editSelectedRecords [name=cost_currency]').val(),
                            cost_price_type: $('#editSelectedRecords [name=cost_price_type]').val(),
                            sale_currency: $('#editSelectedRecords [name=sale_currency]').val(),
                            sale_price_type: $('#editSelectedRecords [name=sale_price_type]').val(),
                            cost_price_status: $('#editSelectedRecords [name=cost_price_status]').val(),
                            company: $('#editSelectedRecords [name=company]').val(),
                            bank_account_id: $('#editSelectedRecords [name=bank_account_id]').val(),
                            realized_date: $('#editSelectedRecords [name=realized_date]').val()
                        },
                        success:function(data){
                            $('#editSelectedRecords .modal-footer').html(modal_footer);
                            $.NotificationApp.send("Success message!", "The order information was successfully updated", 'top-right', '#5ba035', 'success');
                            location.reload();
                        }
                    });
                }
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

        });

    </script>

@endsection
