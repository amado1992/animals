@include('components.errorlist')

<div class="mb-2">
    {!! Form::label('name', 'Bank name *') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('iban', 'IBAN number *') !!}
    {!! Form::text('iban', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="row mb-2">
    <div class="col-md-4">
        {!! Form::label('company_name', 'Company *') !!}
        {!! Form::select('company_name', ['IZS-BV' => 'IZS-BV', 'IZS-Inc' => 'IZS-Inc', 'Personal' => 'Personal'], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('currency', 'Currency *') !!}
        {!! Form::select('currency', $currencies, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="mb-2">
    {!! Form::label('company_address', 'Company address *') !!}
    {!! Form::text('company_address', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('beneficiary_name', 'Beneficiary bank name *') !!}
    {!! Form::text('beneficiary_name', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('beneficiary_address', 'Beneficiary bank address *') !!}
    {!! Form::text('beneficiary_address', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('beneficiary_account', 'Beneficiary account number *') !!}
    {!! Form::text('beneficiary_account', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('beneficiary_swift', 'Beneficiary swift *') !!}
    {!! Form::text('beneficiary_swift', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('beneficiary_aba', 'Beneficiary aba *') !!}
    {!! Form::text('beneficiary_aba', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('correspondent_name', 'Correspondent bank name *') !!}
    {!! Form::text('correspondent_name', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('correspondent_address', 'Correspondent bank address *') !!}
    {!! Form::text('correspondent_address', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('correspondent_swift', 'Correspondent bank swift *') !!}
    {!! Form::text('correspondent_swift', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('beneficiary_account_in_correspondent', 'Benefeciary account in Correspondent bank *') !!}
    {!! Form::text('beneficiary_account_in_correspondent', null, ['class' => 'form-control']) !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($bank_account))
    <a href="{{ route('bank_accounts.show', $bank_account) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('bank_accounts.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif
