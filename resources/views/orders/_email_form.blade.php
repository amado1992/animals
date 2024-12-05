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
        {!! Form::text('email_cc', (isset($email_cc) ? $email_cc : null), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_bcc', 'Email bcc:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_bcc', (isset($email_bcc) ? $email_bcc : null), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_subject', 'Subject:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::text('email_subject', $email_subject, ['class' => 'form-control', 'required', $email_subject ? '' : 'readonly']) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('email_attachments', 'Attachments:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        @foreach ($email_attachments as $attachment)
            {{ $attachment }},
        @endforeach
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        {!! Form::label('email_body', 'Email body:', ['class' => 'font-weight-bold']) !!}
    </div>
    <div class="col-md-10">
        {!! Form::textarea('email_body', $email_body, ['id' => 'email_body', 'class' => 'form-control']) !!}
        {!! Form::hidden('email_option', $email_code, ['class' => 'form-control']) !!}
        {!! Form::hidden('order_id', $order->id, ['id' => 'order_id', 'class' => 'form-control']) !!}
        {!! Form::hidden('invoice_id', (isset($invoice) ? $invoice->id : null), ['id' => 'invoice_id', 'class' => 'form-control']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('orders.show', $order) }}" class="btn btn-link" type="button">Cancel</a>

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
