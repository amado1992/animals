@extends('layouts.admin')

@section('header-content')

    <div class="float-right">
        @if (Auth::user()->hasPermission('invoices.create'))
            <a href="{{ route('invoices.create') }}" class="btn btn-light">
                <i class="fas fa-fw fa-plus"></i> Add
            </a>
        @endif
        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#filterInvoices">
            <i class="fas fa-fw fa-search"></i> Filter
        </button>
        <a href="{{ route('invoices.showAll') }}" class="btn btn-light">
            <i class="fas fa-fw fa-window-restore"></i> Show all
        </a>
        @if (Auth::user()->hasPermission('invoices.export-survey'))
            <a id="exportInvoicesRecords" href="#" class="btn btn-light" data-toggle="modal" data-target="#exportInvoices">
                <i class="fas fa-fw fa-save"></i> Export
            </a>
        @endif
        @if (Auth::user()->hasPermission('invoices.export-survey'))
            <a href="#" class="btn btn-light" id="exportInvoicesZip">
                <i class="fas fa-fw fa-save"></i> Export Invoices Zip
            </a>
        @endif
        <a href="#" class="btn btn-light" id="sendMultipleInvoices">
            <i class="fas fa-envelope mr-2"></i> Send selected invoices
        </a>
    </div>

    <h1 class="h1 text-white"><i class="fas fa-fw fa-file-invoice-dollar mr-2"></i> {{ __('Invoices') }}</h1>
    <p class="text-white">All invoices generated.</p>

@endsection

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-body">

        <div class="d-flex flex-row align-items-center mb-3">
            <div class="d-flex flex-row align-items-center">
                <span class="mr-1">Order by:</span>
                {!! Form::open(['id' => 'invoicesOrderByForm', 'route' => 'invoices.orderBy', 'method' => 'GET']) !!}
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
            <div class="d-flex flex-row align-items-center">
                <span class="ml-3 mr-1">Filtered on:</span>
                @foreach ($filterData as $key => $value)
                    <a href="{{ route('invoices.removeFromInvoiceSession', $key) }}" class="btn btn-sm btn-secondary btn-icon-split mr-1"><span class="text">{{$value}}</span><span class="icon text-white-50"><i class="fas fa-times"></i></span></a>
                @endforeach
            </div>
        </div>

      @unless($invoices->isEmpty())
        <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
            <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" name="selectAll" /></th>
                    <td></th>
                    <th>Order No.</th>
                    <th>Order status</th>
                    <th>Invoice No.</th>
                    <th>Invoice Date</th>
                    <th>Client or provider</th>
                    <th>Description</th>
                    <th>Bank account</th>
                    <th>Curr</th>
                    <th>Amount</th>
                    <th>Deb./Cred</th>
                    <th>Received or paid</th>
                    <th>Product</th>
                    <th>Payment date</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $key_invoice => $invoice)
                <tr>
                    <td>
                        <input type="checkbox" class="selector" value="{{ $invoice->id }}" />
                        @if (!$invoice->belong_to_order)
                            @if (Auth::user()->hasPermission('invoices.update'))
                                <a href="{{ route('invoices.edit', [$invoice->id]) }}" title="Edit item"><i class="fas fa-edit"></i></a>
                            @endif
                            @if (Auth::user()->hasPermission('invoices.delete'))
                                {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id], 'onsubmit' => 'return confirm("Are you sure to delete this record?")']) !!}
                                    <a href="#" onclick="$(this).closest('form').submit();"><i class="fas fa-window-close"></i></a>
                                {!! Form::close() !!}
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($invoice->belong_to_order)
                            @if ($invoice->invoice_type === 'credit')
                                <a href="{{ Storage::url('orders_docs/'.$invoice->order->full_number.'/outgoing_invoices/'.$invoice->invoice_file) }}" target="_blank"><i class="fas fa-file-alt mr-2"></i></a>
                            @else
                                <a href="{{ Storage::url('orders_docs/'.$invoice->order->full_number.'/incoming_invoices/'.$invoice->invoice_file) }}" target="_blank"><i class="fas fa-file-alt mr-2"></i></a>
                            @endif
                        @else
                            <a href="{{ Storage::url('other_invoices/'.$invoice->invoice_file) }}" target="_blank"><i class="fas fa-file-alt mr-2"></i></a>
                        @endif
                        <a href="#" title="Set invoice payment." id="setCreditInvoicePayment" data-id="{{ $invoice->id }}"><i class="fas fa-edit mr-2"></i></a>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col">
                                <span>{{ ($invoice->order) ? $invoice->order->full_number : '' }}</span><br>
                                <span>{{ ($invoice->order) && ($invoice->order->offer) ? $invoice->order->offer->offer_type : '' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ ($invoice->order) ? $invoice->order->order_status : '' }}</td>
                    <td>{{ $invoice->full_number }}</td>
                    <td>{{date('m-d-Y', strtotime($invoice->invoice_date))}}</td>
                    <td>
                        @if ($invoice->contact != null)
                            {{$invoice->contact->full_name}}<br>
                            {{$invoice->contact->email}}
                        @endif
                    </td>
                    <td style="word-wrap: break-word; min-width: 130px;max-width: 130px; white-space: normal;">
                        @if ($invoice->order)
                            @foreach ($invoice->order->offer->species_ordered as $key => $animal)
                                @if($key == 0)
                                    @if ($loop->last)
                                        {{ $animal->oursurplus->animal->common_name}}
                                    @else
                                        {{ $animal->oursurplus->animal->common_name}}/
                                    @endif
                                @else
                                    <div class="more-species-{{ $key_invoice }} d-none">
                                        @if ($loop->last)
                                            {{ $animal->oursurplus->animal->common_name}}
                                        @else
                                            {{ $animal->oursurplus->animal->common_name}}/
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                            @if (!empty($invoice->order->offer->species_ordered) && count($invoice->order->offer->species_ordered) > 1)
                                <p class="modal-toggle see-more see-more-species" data-id="{{ $key_invoice }}">See More</p>
                            @endif
                        @endif
                    </td>
                    <td>{{ ($invoice->bank_account) ? $invoice->bank_account->company_name : '' }}</td>
                    <td>{{$invoice->invoice_currency}}</td>
                    <td>{{ number_format($invoice->invoice_amount, 2, '.', '') }}</td>
                    <td>{{$invoice->invoice_type}}</td>
                    <td>{{ number_format($invoice->paid_value, 2, '.', '') }}</td>
                    <td>{{ $invoice->invoice_from ?? "" }}</td>
                    <td>{{$invoice->paid_date}}</td>
                    <td>{{ number_format(($invoice->invoice_amount - $invoice->paid_value), 2, '.', '') }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        {{$invoices->links()}}
      @else
        <p> No invoices are added yet </p>
      @endunless
    </div>
  </div>

  @include('invoices.filter_modal', ['modalId' => 'filterInvoices'])

  @include('export_excel.export_options_modal', ['modalId' => 'exportInvoices'])
  @include('invoices.set_invoice_payment_modal', ['modalId' => 'setInvoicePaymentDialog'])

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

    $('#orderByField').on('change', function () {
        $('#invoicesOrderByForm').submit();
    });

    $('#orderByDirection').on('change', function () {
        $('#invoicesOrderByForm').submit();
    });

    $('#filterInvoices').on('hidden.bs.modal', function () {
        $("#filterInvoices .contact-select2").val(null).trigger('change');
        $(this).find('form').trigger('reset');
    });

    //Select2 species selection
    $('[name=filter_invoice_species_id]').on('change', function () {
        var speciesId = $(this).val();

        if(speciesId != null) {
            $.ajax({
                type:'POST',
                url:"{{ route('api.animal-by-id') }}",
                data: {
                    id: speciesId,
                },
                success:function(data) {
                    // create the option and append to Select2
                    var newOption = new Option(data.animal.common_name.trim(), data.animal.id, true, true);
                    // Append it to the select
                    $('[name=filter_invoice_species_id]').append(newOption);
                }
            });
        }
    });

    $('#filterInvoices [name=filter_invoice_company]').on('change', function () {
        var value = $(this).val();

        $.ajax({
            type:'POST',
            url:"{{ route('orders.getBankAccountsByCompany') }}",
            data:{
                value: value,
            },
            success:function(data) {
                $("#filterInvoices [name=filter_bank_account_id]").empty();
                $('#filterInvoices [name=filter_bank_account_id]').append('<option value="">- select -</option>');
                $.each(data.bankAccounts, function(key, value) {
                    $('#filterInvoices [name=filter_bank_account_id]').append('<option value="'+ key +'">'+ value +'</option>');
                });
            }
        });
    });

    $('#exportInvoicesRecords').on('click', function () {
        var table = $('.datatable').DataTable();

        var count_selected_records = $(":checked.selector").length;
        var count_page_records = table.rows().count();
        $("label[for='count_selected_records']").html('('+count_selected_records+')');
        $("label[for='count_page_records']").html('('+count_page_records+')');

        $('#exportInvoices').modal('show');
    });

    $('#exportInvoices').on('submit', function (event) {
        event.preventDefault();

        var table = $('.datatable').DataTable();

        var export_option = $('#exportInvoices [name=export_option]:checked').val();

        var ids = [];
        if(export_option == "selection") {
            $(":checked.selector").each(function(){
                ids.push($(this).val());
            });
        }
        else {
            table.rows().every( function (rowIdx) {
                var row = $(this.node());
                ids.push(row.find('input').val());
            });
        }

        if(ids.length == 0)
            alert("There are not records to export.");
        else {
            var url = "{{route('invoices.export')}}?items=" + ids;
            window.location = url;

            $('#exportInvoices').modal('hide');
        }
    });

    $('#exportInvoicesZip').on('click', function (event) {
        event.preventDefault();

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0)
            $.NotificationApp.send("Error message!", "There are not records to export.", 'top-right', '#bf441d', 'error');
        else {
            var url = "{{route('invoices.exportZip')}}?items=" + ids;
            window.location = url;
        }
    });

    $('#sendMultipleInvoices').on('click', function (event) {
        event.preventDefault();
        var sendMultipleInvoices = $('#sendMultipleInvoices').html();
        $('#sendMultipleInvoices').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

        var ids = [];
        $(":checked.selector").each(function(){
            ids.push($(this).val());
        });

        if(ids.length == 0){
            $('#sendMultipleInvoices').html(sendMultipleInvoices);
            $.NotificationApp.send("Error message!", "There are not records to send.", 'top-right', '#bf441d', 'error');
        }
        else {
            var url = "{{route('invoices.sendMultipleClientInvoice')}}?items=" + ids;
            window.location = url;
        }
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

    $(".see-more-species").on("click", function(){
        var id = $(this).attr("data-id");
        $(".more-species-" + id).removeClass("d-none");
        $(this).addClass("d-none");
    });

</script>

@endsection
