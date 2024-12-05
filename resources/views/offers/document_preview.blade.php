@extends('layouts.admin')

@section('main-content')

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h1 class="mb-4">Document preview</h1>
                {!! Form::open(['route' => 'offers.export_offer_or_calculation_pdf']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::textarea('document_info', $document_info, ['id' => 'document_info', 'class' => 'form-control']) !!}
                            {!! Form::hidden('offer_id', $offer->id, ['class' => 'form-control']) !!}
                            {!! Form::hidden('is_calculation', $is_calculation, ['class' => 'form-control']) !!}
                            {!! Form::hidden('x_quantity', (isset($x_quantity)) ? $x_quantity : null, ['class' => 'form-control']) !!}
                            {!! Form::hidden('parent_view', $parent_view, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <hr class="mb-4">

                    <button class="btn btn-primary btn-lg" type="submit">Generate</button>
                    @if ($parent_view === 'offer_details')
                        <a href="{{ route('offers.show', $offer) }}" class="btn btn-link" type="button">Cancel</a>
                    @elseif ($parent_view === 'order_details')
                        <a href="{{ route('orders.show', $offer->order) }}" class="btn btn-link" type="button">Cancel</a>
                    @else
                        <a href="{{ route('offers.index') }}" class="btn btn-link" type="button">Cancel</a>
                    @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')

<script type="text/javascript">

    $(document).ready(function() {
        CKEDITOR.replace('document_info', {
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
