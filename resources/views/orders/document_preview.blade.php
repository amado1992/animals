@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Document preview</h1>
                @if ($code === "proforma_invoice")
                    <div id="imageAnimalMessage" class="alert alert-warning" role="warning">
                        <strong>Please check if the balance- invoice have bene sent!</strong>
                    </div>
                @endif
                {!! Form::open(['route' => 'orders.export_document_pdf']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::textarea('document_html', $document_html, ['id' => 'document_html', 'class' => 'form-control']) !!}
                            {!! Form::hidden('order_id', $order->id, ['id' => 'order_id', 'class' => 'form-control']) !!}
                            {!! Form::hidden('code', $code, ['id' => 'code', 'class' => 'form-control']) !!}
                            {!! Form::hidden('file_name', $file_name, ['id' => 'file_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <hr class="mb-4">

                    <button class="btn btn-primary btn-lg" type="submit">Generate</button>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-link" type="button">Cancel</a>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        CKEDITOR.replace('document_html', {
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
            height: 300,
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'NewPage,ExportPdf,Preview,Print,Templates,Save, Strike,Subscript,Superscript,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles'
        });
    });

</script>

@endsection
