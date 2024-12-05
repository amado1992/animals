@include('components.errorlist')

<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_from', 'Email from:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_from', $email_from, ['class' => 'form-control', 'required']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_to', 'Email to:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_to', $email_to, ['class' => 'form-control', 'required']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_cc', 'Email cc:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_cc', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_subject', 'Subject:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_subject', $email_subject, ['class' => 'form-control', 'required']) !!}
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        {!! Form::label('email_body', 'Email body:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::textarea('email_body', $email_body, ['id' => 'email_body', 'class' => 'form-control']) !!}
        {!! Form::hidden('triggered_from', $triggeredFrom, ['id' => 'triggered_from', 'class' => 'form-control']) !!}
        {!! Form::hidden('triggered_id', $idTriggered, ['id' => 'triggered_id', 'class' => 'form-control']) !!}
        {!! Form::hidden('search_mailing_id', $idMailing, ['id' => 'search_mailing_id', 'class' => 'form-control']) !!}
        {!! Form::hidden('animal_id', $animal->id, ['id' => 'animal_id', 'class' => 'form-control']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if ($triggeredFrom === 'offers')
    <a href="{{ route('offers.show', $idTriggered) }}" class="btn btn-link" type="button">Cancel</a>
@elseif ($triggeredFrom === 'wanted')
    <a href="{{ route('wanted.show', $idTriggered) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('search-mailings.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    CKEDITOR.replace('email_body', {
        // Define the toolbar groups as it is a more accessible solution.
        toolbarGroups: [{
            "name": "document",
            "groups": ["mode"]
            },
            {
            "name": "basicstyles",
            "groups": ["basicstyles"]
            },
            {
            "name": "links",
            "groups": ["links"]
            },
            {
            "name": "paragraph",
            "groups": ["list", "align"]
            },
            {
            "name": "insert",
            "groups": ["insert"]
            },
            {
            "name": "styles",
            "groups": ["styles"]
            },
            {
            "name": "colors",
            "groups": ["colors"]
            }
        ],
        extraPlugins: 'stylesheetparser',
        height: 200,
        // Remove the redundant buttons from toolbar groups defined above.
        removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
    });
});

</script>

@endsection
