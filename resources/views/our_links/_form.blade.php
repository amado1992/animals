@include('components.errorlist')

<div class="mb-2">
    {!! Form::label('site_name', 'Site name *', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('siteName', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('site_url', 'Site url *', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('siteUrl', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-2">
    {!! Form::label('site_remarks', 'Site remarks', ['class' => 'font-weight-bold']) !!}
    {!! Form::text('siteRemarks', null, ['class' => 'form-control']) !!}
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('site_username', 'Login username', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('loginUsername', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('site_password', 'Login password', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('loginPassword', (isset($ourLink)) ? Crypt::decryptString($ourLink->loginPassword) : null, ['class' => 'form-control']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('our-links.index') }}" class="btn btn-link" type="button">Cancel</a>
