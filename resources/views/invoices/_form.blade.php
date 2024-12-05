@include('components.errorlist')

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('invoiceContact', 'Client or supplier *', ['class' => 'font-weight-bold']) !!}
        @if ( isset($invoice) )
            <br>{!! Form::label('invoice_contact', $invoice->contact->full_name . ' (' . $invoice->contact->email . ')', ['class' => 'text-danger']) !!}
        @endif
        <select class="contact-select2 form-control" type="default" style="width: 100%" name="invoice_contact_id">
            @if( isset($invoice) && $invoice->invoice_contact_id )
                <option value="{{ $invoice->invoice_contact_id }}" selected>{{ $invoice->contact->email }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-6">
        {!! Form::label('Date', 'Date *', ['class' => 'font-weight-bold']) !!}
        {!! Form::date('invoice_date', \Carbon\Carbon::now(), ['id' => 'invoice_date', 'class' => 'form-control', 'required']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-4">
        {!! Form::label('invoice_company', 'Company *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('invoice_company', $companies, (isset($invoice)) ? $invoice->company : null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('bank_account', 'Bank account *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('bank_account_id', $bankAccounts, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('invoice_currency', 'Currency *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('invoice_currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('invoice_amount', 'Total amount *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('invoice_amount', (isset($invoice)) ? $invoice->invoice_amount : 0, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('paid_value', 'Amount paid', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('paid_value', (isset($invoice)) ? $invoice->paid_value : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('paid_date', 'Paid_date', ['class' => 'font-weight-bold']) !!}
        {!! Form::date('paid_date', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('invoice_type', 'Type *', ['class' => 'font-weight-bold']) !!}
        {!! Form::select('invoice_type', $invoice_type, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::label('invoiceFile', 'File *', ['class' => 'font-weight-bold']) !!}
        {!! Form::file('invoiceFile', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-8">
        {!! Form::label('remark', 'Remark', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('remark', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">

</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{$submitButtonText}}</button>
<a href="{{route('invoices.index')}}" class="btn btn-link" type="button">Cancel</a>

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('[name=invoice_company]').on('change', function () {
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
});

</script>

@endsection
