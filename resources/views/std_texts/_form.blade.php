@include('components.errorlist')

<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('category', 'Category *') !!}
        {!! Form::select('category', $stdTextsCategories, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('code', 'Code') !!}
        {!! Form::text('code', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="mb-2">
    {!! Form::label('remarks', 'Remarks') !!}
    {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('english_text', 'English text') !!}
    {!! Form::textarea('english_text', null, ['id' => 'english_text', 'class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('spanish_text', 'Spanish text') !!}
    {!! Form::textarea('spanish_text', null, ['id' => 'spanish_text', 'class' => 'form-control']) !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('std-texts.index') }}" class="btn btn-link" type="button">Cancel</a>

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        CKEDITOR.replace('english_text', {
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
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
        });

        CKEDITOR.replace('spanish_text', {
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
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
        });
    });

</script>

@endsection
