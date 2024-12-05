<div class="mb-2">
    {!! Form::label('text', 'Text *') !!}
    {!! Form::textarea('text', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
    @error('text')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('domain-name-link.index') }}" class="btn btn-link" type="button">Cancel</a>
