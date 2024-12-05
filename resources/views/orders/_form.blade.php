
@include('components.errorlist')

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card shadow">
                @if (isset($order) && isset($order->client))
                    <div class="card-header pl-2 pt-1 pb-0">
                        {!! Form::label('client_info', 'CLIENT: ' . $order->client->full_name . ' (' . $order->client->email .')', ['class' => 'text-danger']) !!}
                    </div>
                @endif
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::radio('filter_client_option', 'institution_client', true) !!}
                            {!! Form::label('institution_contact', 'Institution client', ['class' => 'mr-2']) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('institution_client', 'Institution client*') !!}
                                {!! Form::hidden('hidden_client_id', ((isset($order)) ? $order->client->id : null), ['class' => 'form-control']) !!}
                                <select class="institution-select2 form-control" type="default" style="width: 100%" name="institution_client_id"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('order_client', 'Contact client *') !!}
                                {!! Form::label('order_client_msg', '(You must select a contact from below box.)', ['class' => 'text-danger']) !!}
                                {!! Form::select('client_id', array(), null, ['class' => 'form-control', 'placeholder' => '- select client -']) !!}
                                <div id="clientSelect2" class="d-none">
                                    <select class="contact-select2 form-control" type="default" style="width: 100%;" name="contact_client_id"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                @if (isset($order) && isset($order->supplier))
                    <div class="card-header pl-2 pt-1 pb-0">
                        {!! Form::label('supplier_info', 'SUPPLIER: ' . $order->supplier->full_name . ' (' . $order->supplier->email .')', ['class' => 'text-danger']) !!}
                    </div>
                @endif
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::radio('filter_supplier_option', 'institution_supplier', true) !!}
                            {!! Form::label('institution_contact', 'Institution supplier', ['class' => 'mr-2']) !!}
                            {!! Form::radio('filter_supplier_option', 'supplier_contact') !!}
                            {!! Form::label('specific_contact', 'Specific supplier') !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('institution_supplier', 'Institution supplier*') !!}
                                <select class="institution-select2 form-control" type="default" style="width: 100%" name="institution_supplier_id"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('order_supplier', 'Contact supplier *') !!}
                                {!! Form::label('order_supplier_msg', '(You must select a contact from below box.)', ['class' => 'text-danger']) !!}
                                {!! Form::select('supplier_id', array(), null, ['class' => 'form-control', 'placeholder' => '- select supplier -']) !!}
                                <div id="supplierSelect2" class="d-none">
                                    <select class="contact-select2 form-control" type="default" style="width: 100%;" name="contact_supplier_id"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="d-flex align-items-start">
                <div class="mr-3">
                    {!! Form::checkbox('set_number_year', null) !!}
                    {!! Form::label('set_number_year', 'Set number and creation date: ') !!}
                </div>
                <div class="d-none" id="setNumberAndCreationDate">
                    <div class="d-flex form-group">
                        {!! Form::label('order_number', 'Order number') !!}
                        {!! Form::number('order_number', null, ['class' => 'form-control ml-2 mr-3']) !!}
                        {!! Form::label('created_date', 'Created date') !!}
                        {!! Form::date('created_at', (isset($order)) ? $order->created_date : null, ['class' => 'form-control ml-2']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('status', 'Status *') !!}
                {!! Form::select('order_status', $orderStatuses, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('manager', 'Manager *') !!}
                {!! Form::select('manager_id', $admins, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('country', 'Destination country *') !!}
                {!! Form::select('delivery_country_id', $countries, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('airport', 'Destination airport *') !!}
                {!! Form::select('delivery_airport_id', [], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
                {!! Form::hidden('hidden_delivery_airport_id', isset($order) ? $order->delivery_airport_id : null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cost_currency', 'Cost currency *') !!}
                {!! Form::select('cost_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cost_price_type', 'Cost price type *') !!}
                {!! Form::select('cost_price_type', $price_type, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sale_currency', 'Sale currency *') !!}
                {!! Form::select('sale_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sale_price_type', 'Sale price type *') !!}
                {!! Form::select('sale_price_type', $price_type, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cost_price_status', 'Cost price status *') !!}
                {!! Form::select('cost_price_status', ['Estimation' => 'Estimation', 'Exactly' => 'Exactly'], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('company', 'Company *') !!}
                {!! Form::select('company', $companies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('bank_account', 'Bank account *') !!}
                {!! Form::select('bank_account_id', $bankAccounts, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
            </div>
        </div>
        @if (isset($order) && $order->order_status === 'Realized')
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('realized_date', 'Realized date') !!}
                    {!! Form::date('realized_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        @endif
    </div>

    <a class="btn btn-warning mb-2" data-toggle="collapse" href="#collapseOtherContacts" role="button" aria-expanded="false" aria-controls="collapseOtherContacts"><i class="fas fa-fw fa-plus"></i> Other contacts</a>
    <div class="row collapse mt-2" id="collapseOtherContacts">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('contact_final_destination', 'Contact final destination') !!}
                <select class="contact-select2 form-control" type="default" style="width: 100%;" name="contact_final_destination_id">
                    @if (isset($order) && $order->contact_final_destination != null)
                        <option value="{{ $order->contact_final_destination_id }}" selected>{{ $order->contact_final_destination->email }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('contact_origin', 'Contact origin') !!}
                <select class="contact-select2 form-control" type="default" style="width: 100%;" name="contact_origin_id">
                    @if (isset($order) && $order->contact_origin != null)
                        <option value="{{ $order->contact_origin_id }}" selected>{{ $order->contact_origin->email }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('airfreight_agent', 'Airfreight agent') !!}
                <select class="contact-select2 form-control" type="default" style="width: 100%;" name="airfreight_agent_id">
                    @if (isset($order) && $order->airfreight_agent != null)
                        <option value="{{ $order->airfreight_agent_id }}" selected>{{ $order->airfreight_agent->email }}</option>
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('order_remarks', 'Order remarks') !!}
                {!! Form::textarea('order_remarks', null, ['class' => 'form-control', 'rows' => '2']) !!}
            </div>
        </div>
    </div>

    <hr class="mb-2">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if ($id == "newOrderForm")
    <a href="{{ route('orders.index') }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('orders.show', $order) }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = $('#orderForm');
        original = form.serialize();

        form.submit(function() {
            window.onbeforeunload = null
        })

        window.onbeforeunload = function() {
            if (form.serialize() != original)
                return 'Are you sure you want to leave?'
        }

        $('[name=company]').on('change', function () {
            var value = $(this).val();

            $.ajax({
                type:'POST',
                url:"{{ route('orders.getBankAccountsByCompany') }}",
                data:{
                    value: value,
                },
                success:function(data){
                    $("[name=bank_account_id]").empty();
                    $('[name=bank_account_id]').append('<option value="">- select -</option>');
                    $.each(data.bankAccounts, function(key, value) {
                        $('[name=bank_account_id]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        });

        $('[name=delivery_country_id]').trigger('change');
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

    //Load institution contacts when institution client is selected.
    $('[name=institution_client_id]').on('change', function () {
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
                        var selected = (data.contacts.length == 1) ? 'selected' : '';

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
                        var selected = (data.contacts.length == 1) ? 'selected' : '';

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

    $('input[name=set_number_year]:checkbox').change(function () {
        if($(this).is(':checked'))
            $('#setNumberAndCreationDate').removeClass("d-none");
        else
            $('#setNumberAndCreationDate').addClass("d-none");
    });

</script>

@endsection
