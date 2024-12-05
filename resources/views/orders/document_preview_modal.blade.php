<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => 'orders.export_document_pdf']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Document preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::textarea('document_html', null, ['id' => 'document_html', 'class' => 'form-control']) !!}
                        {!! Form::hidden('order_id', $order->id, ['id' => 'order_id', 'class' => 'form-control']) !!}
                        {!! Form::hidden('file_name', null, ['id' => 'file_name', 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                {!! Form::submit('Generate', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
