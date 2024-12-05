@include('components.errorlist')

<div class="mb-3">
    {!! Form::label('site_name', 'Site name *', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('siteName', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('site_url', 'Site url', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('siteUrl', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('site_remarks', 'Site remarks', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('siteRemarks', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('site_username', 'Login username', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('loginUsername', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('site_password', 'Login password', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('loginPassword', (isset($code)) ? Crypt::decryptString($code->loginPassword) : null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::checkbox('only_for_john', null) !!}
    {!! Form::label('onlyForJohn', 'Only for John') !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('codes.index') }}" class="btn btn-link" type="button">Cancel</a>

