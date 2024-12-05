<div class="mb-2">
    {!! Form::label('domain_name', 'Domain Name *') !!}
    {!! Form::text('domain_name', null, ['class' => 'form-control ' . ($errors->has('domain_name') ? 'is-invalid': ''), 'placeholder' => 'Eg: rohan.com']) !!}
    @error('domain_name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="mb-2">
    {!! Form::label('canonical_name', 'Canonical Name *') !!}
    {!! Form::text('canonical_name', null, ['class' => 'form-control ' . ($errors->has('canonical_name') ? 'is-invalid': '')]) !!}
    @error('canonical_name')
    <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('domain-name-link.index') }}" class="btn btn-link" type="button">Cancel</a>
